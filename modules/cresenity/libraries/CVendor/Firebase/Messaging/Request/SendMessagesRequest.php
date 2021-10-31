<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Psr\Http\Message\RequestInterface;

final class CVendor_Firebase_Messaging_Request_SendMessagesRequest implements CVendor_Firebase_Http_HasSubRequestsInterface, RequestInterface {

    const MAX_AMOUNT_OF_MESSAGES = 500;

    use CVendor_Firebase_Trait_WrappedPsr7RequestTrait;

    public function __construct($projectId, CVendor_Firebase_Messaging_Messages $messages) {
        if ($messages->count() > self::MAX_AMOUNT_OF_MESSAGES) {
            throw new CVendor_Firebase_Exception_InvalidArgumentException('Only ' . self::MAX_AMOUNT_OF_MESSAGES . ' can be sent at a time.');
        }

        $subRequests = [];

        $index = 0;

        foreach ($messages as $message) {
            $subRequests[] = (new CVendor_Firebase_Messaging_Request_SendMessageRequest($projectId, $message))
                    // see https://github.com/firebase/firebase-admin-node/blob/master/src/messaging/batch-request.ts#L104
                    ->withHeader('Content-ID', (string) ++$index)
                    ->withHeader('Content-Transfer-Encoding', 'binary')
                    ->withHeader('Content-Type', 'application/http');
        }

        $this->wrappedRequest = new CVendor_Firebase_Http_RequestWithSubRequests(
                'https://fcm.googleapis.com/batch', new CVendor_Firebase_Http_Requests(...$subRequests)
        );
    }

}
