<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Psr\Http\Message\RequestInterface;
use CVendor_Firebase_Messaging_MessageTarget as MessageTarget;

final class CVendor_Firebase_Messaging_MulticastSendReport implements Countable {

    /** @var SendReport[] */
    private $items = [];

    private function __construct() {
        
    }

    /**
     * @param SendReport[] $items
     */
    public static function withItems(array $items) {
        $report = new self();

        foreach ($items as $item) {
            $report = $report->withAdded($item);
        }

        return $report;
    }

    public static function fromRequestsAndResponses(CVendor_Firebase_Http_Requests $requests, CVendor_Firebase_Http_Responses $responses) {
        $reports = [];
        $errorHandler = new CVendor_Firebase_Messaging_ApiExceptionConverter();

        foreach ($responses as $response) {
            $contentIdHeader = $response->getHeaderLine('Content-ID');
            $contentIdHeaderParts = \explode('-', $contentIdHeader);

            if (!($responseId = \array_pop($contentIdHeaderParts) ?: null)) {
                continue;
            }

            $matchingRequest = $requests->findBy(static function (RequestInterface $request) use ($responseId) {
                $contentIdHeader = $request->getHeaderLine('Content-ID');
                $contentIdHeaderParts = \explode('-', $contentIdHeader);
                $contentId = \array_pop($contentIdHeaderParts);

                return $contentId === $responseId;
            });

            if (!$matchingRequest) {
                continue;
            }

            try {
                $requestData = CHelper::json()->decode((string) $matchingRequest->getBody(), true);
            } catch (CVendor_Firebase_Exception_InvalidArgumentException $e) {
                continue;
            }

            $target = null;

            if ($token = carr::get($requestData, 'message.token', null)) {
                $target = MessageTarget::with(MessageTarget::TOKEN, (string) $token);
            } elseif ($topic = carr::get($requestData, 'message.topic', null)) {
                $target = MessageTarget::with(MessageTarget::TOPIC, (string) $topic);
            } elseif ($condition = carr::get($requestData, 'message.condition', null)) {
                $target = MessageTarget::with(MessageTarget::CONDITION, (string) $condition);
            }

            if ($target === null) {
                continue;
            }

            if ($response->getStatusCode() < 400) {
                try {
                    $responseData = JSON::decode((string) $response->getBody(), true);
                } catch (CVendor_Firebase_Exception_InvalidArgumentException $e) {
                    $responseData = [];
                }

                $reports[] = CVendor_Firebase_Messaging_SendReport::success($target, $responseData);
            } else {
                $error = $errorHandler->convertResponse($response);
                $reports[] = CVendor_Firebase_Messaging_SendReport::failure($target, $error);
            }
        }

        return self::withItems($reports);
    }

    public function withAdded(CVendor_Firebase_Messaging_SendReport $report) {
        $new = clone $this;
        $new->items[] = $report;

        return $new;
    }

    /**
     * @return SendReport[]
     */
    public function getItems() {
        return $this->items;
    }

    public function successes() {
        return self::withItems(\array_filter($this->items, static function (CVendor_Firebase_Messaging_SendReport $item) {
                            return $item->isSuccess();
                        }));
    }

    public function failures() {
        return self::withItems(\array_filter($this->items, static function (CVendor_Firebase_Messaging_SendReport $item) {
                            return $item->isFailure();
                        }));
    }

    public function hasFailures() {
        return $this->failures()->count() > 0;
    }

    public function count() {
        return \count($this->items);
    }

}
