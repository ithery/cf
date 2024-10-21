<?php

class CVendor_MailerSend_Endpoints_Email extends CVendor_MailerSend_Endpoints_AbstractEndpoint
{
    protected string $endpoint = 'email';

    /**
     * @throws \JsonException
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function send(CVendor_MailerSend_Helpers_Builder_EmailParams $params): array
    {
        CVendor_MailerSend_Helpers_GeneralHelpers::validateEmailParams($params);

        $recipients_mapped = CVendor_MailerSend_Helpers_GeneralHelpers::mapToArray($params->getRecipients(), CVendor_MailerSend_Helpers_Builder_Recipient::class);
        $cc_mapped = CVendor_MailerSend_Helpers_GeneralHelpers::mapToArray($params->getCc(), CVendor_MailerSend_Helpers_Builder_Recipient::class);
        $bcc_mapped = CVendor_MailerSend_Helpers_GeneralHelpers::mapToArray($params->getBcc(), CVendor_MailerSend_Helpers_Builder_Recipient::class);
        $attachments_mapped = CVendor_MailerSend_Helpers_GeneralHelpers::mapToArray($params->getAttachments(), CVendor_MailerSend_Helpers_Builder_Attachment::class);
        $variables_mapped = CVendor_MailerSend_Helpers_GeneralHelpers::mapToArray($params->getVariables(), CVendor_MailerSend_Helpers_Builder_Variable::class);
        $personalization_mapped = CVendor_MailerSend_Helpers_GeneralHelpers::mapToArray($params->getPersonalization(), CVendor_MailerSend_Helpers_Builder_Personalization::class);

        return $this->httpLayer->post(
            $this->buildUri($this->endpoint),
            array_filter(
                [
                    'from' => [
                        'email' => $params->getFrom(),
                        'name' => $params->getFromName(),
                        ],
                    'reply_to' => [
                        'email' => $params->getReplyTo(),
                        'name' => $params->getReplyToName(),
                        ],
                    'to' => $recipients_mapped,
                    'cc' => $cc_mapped,
                    'bcc' => $bcc_mapped,
                    'subject' => $params->getSubject(),
                    'template_id' => $params->getTemplateId(),
                    'text' => $params->getText(),
                    'html' => $params->getHtml(),
                    'tags' => $params->getTags(),
                    'attachments' => $attachments_mapped,
                    'variables' => $variables_mapped,
                    'personalization' => $personalization_mapped,
                    'send_at' => $params->getSendAt(),
                    'precedence_bulk' => $params->getPrecedenceBulkHeader(),
                    'in_reply_to' => $params->getInReplyToHeader(),
                    'settings' => [
                        'track_clicks' => $params->trackClicks(),
                        'track_opens' => $params->trackOpens(),
                        'track_content' => $params->trackContent(),
                    ],
                    'headers' => $params->getHeaders(),
                ],
                fn ($v) => is_array($v) ? array_filter($v, fn ($v) => $v !== null) : $v !== null
            )
        );
    }
}
