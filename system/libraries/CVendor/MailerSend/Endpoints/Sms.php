<?php

class CVendor_MailerSend_Endpoints_Sms extends CVendor_MailerSend_Endpoints_AbstractEndpoint {
    protected string $endpoint = 'sms';

    /**
     * @throws \JsonException
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function send(CVendor_MailerSend_Helpers_Builder_SmsParams $params): array {
        CVendor_MailerSend_Helpers_GeneralHelpers::validateSmsParams($params);

        $personalization_mapped = CVendor_MailerSend_Helpers_GeneralHelpers::mapToArray($params->getPersonalization(), CVendor_MailerSend_Helpers_Builder_SmsPersonalization::class);

        return $this->httpLayer->post(
            $this->url($this->endpoint),
            array_filter(
                [
                    'from' => $params->getFrom(),
                    'to' => $params->getTo(),
                    'text' => $params->getText(),
                    'personalization' => $personalization_mapped,
                ],
                fn ($v) => is_array($v) ? array_filter($v, fn ($v) => $v !== null) : $v !== null
            )
        );
    }
}
