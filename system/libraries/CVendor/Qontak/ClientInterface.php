<?php

interface CVendor_Qontak_ClientInterface {
    public function send(string $templateId, string $channelId, CVendor_Qontak_Message $message): CVendor_Qontak_Response;
}
