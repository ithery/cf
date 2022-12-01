<?php

class CDevCloud {
    /**
     * @var null|CDevCloud_Inspector
     */
    protected static $inspector;

    public static function inspector() {
        if (self::$inspector == null) {
            $key = base64_encode(CF::appId() . '|' . CF::appCode() . '|' . CF::orgId() . '|' . CF::orgCode() . '|' . CF::environment());
            $configuration = (new CDevCloud_Inspector_Configuration($key))
                ->setEnabled(CF::config('devcloud.inspector.enable', true))
                ->setUrl(CF::config('devcloud.inspector.url', 'https://cpanel.ittron.co.id/inspector'))
                //->setUrl('https://webhook.site/e3fc6bdf-0062-4f07-97da-58a67d27c45f')
                ->setVersion(CF::version())
                ->setTransport(CF::config('devcloud.inspector.transport', 'async'))
                ->setOptions(CF::config('devcloud.inspector.options', []))
                ->setMaxItems(CF::config('devcloud.inspector.max_items', 100));

            self::$inspector = new CDevCloud_Inspector($configuration);
        }

        return self::$inspector;
    }
}
