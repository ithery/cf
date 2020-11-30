<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 29, 2020 
 * @license Ittron Global Teknologi
 */
class CComponent_Response {

    public $request;
    public $fingerprint;
    public $effects;
    public $memo;

    public static function fromRequest($request) {
        return new static($request);
    }

    public function __construct($request) {
        $this->request = $request;

        $this->fingerprint = $request->fingerprint;
        $this->memo = $request->memo;
        $this->effects = [];
    }

    public function id() {
        return $this->fingerprint['id'];
    }

    public function embedThyselfInHtml() {
        if (!$html = carr::get($this->effects, 'html')) {
            return;
        }
        $this->effects['html'] = (new CComponent_HydrationMiddleware_AddAttributesToRootTagOfHtml)->__invoke($html, [
            'initial-data' => $this->toArrayWithoutHtml(),
        ]);
    }

    public function embedIdInHtml() {
        if (!$html = carr::get($this->effects, 'html')) {
            return;
        }

        $this->effects['html'] = (new CComponent_HydrationMiddleware_AddAttributesToRootTagOfHtml)->__invoke($html, [
            'id' => $this->fingerprint['id'],
        ]);
    }

    public function html() {
        return carr::get($this->effects, 'html', null);
    }

    public function toArrayWithoutHtml() {
        return [
            'fingerprint' => $this->fingerprint,
            'effects' => array_diff_key($this->effects, ['html' => null]),
            'serverMemo' => $this->memo,
        ];
    }

    public function toInitialResponse() {
        return c::tap($this)->embedIdInHtml();
    }

    public function toSubsequentResponse() {
        $this->embedIdInHtml();

        $requestMemo = $this->request->memo;
        $responseMemo = $this->memo;
        $dirtyMemo = [];

        // Only send along the memos that have changed to not bloat the payload.
        foreach ($responseMemo as $key => $newValue) {
            // If the memo key is not in the request, add it.
            if (!isset($requestMemo[$key])) {
                $dirtyMemo[$key] = $newValue;

                continue;
            }

            // If the memo values are the same, skip adding them.
            if ($requestMemo[$key] === $newValue)
                continue;

            $dirtyMemo[$key] = $newValue;
        }

        // If 'data' is present in the response memo, diff it one level deep.
        if (isset($dirtyMemo['data']) && isset($requestMemo['data'])) {
            foreach ($dirtyMemo['data'] as $key => $value) {
                if ($value === $requestMemo['data'][$key]) {
                    unset($dirtyMemo['data'][$key]);
                }
            }
        }

        // Make sure any data marked as "dirty" is present in the resulting data payload.
        foreach (CF::get($this, 'effects.dirty', []) as $property) {
            $property = carr::head(explode('.', $property));

            CF::set($dirtyMemo, 'data.' . $property, $responseMemo['data'][$property]);
        }

        return [
            'effects' => $this->effects,
            'serverMemo' => $dirtyMemo,
        ];
    }

}
