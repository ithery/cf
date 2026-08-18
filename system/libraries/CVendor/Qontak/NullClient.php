<?php

final class CVendor_Qontak_NullClient implements CVendor_Qontak_ClientInterface {
    public function send(string $templateId, string $channelId, CVendor_Qontak_Message $message): CVendor_Qontak_Response {
        return new CVendor_Qontak_Response('messageId', $message->getReceiver()->getName());
    }
}
