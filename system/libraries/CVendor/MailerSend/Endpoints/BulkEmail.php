<?php


use Assert\Assertion;

class CVendor_MailerSend_Endpoints_BulkEmail extends CVendor_MailerSend_Endpoints_AbstractEndpoint
{
    protected string $endpoint = 'bulk-email';

    /**
     * @throws \JsonException
     * @throws CVendor_MailerSend_Exceptions_MailerSendAssertException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function send(array $bulkParams): array
    {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(fn () => Assertion::minCount($bulkParams, 1, 'Bulk params should contain at least 1 email'));

        $requestData = [];

        foreach ($bulkParams as $params) {
            CVendor_MailerSend_Helpers_GeneralHelpers::validateEmailParams($params);

            $recipients_mapped = CVendor_MailerSend_Helpers_GeneralHelpers::mapToArray($params->getRecipients(), CVendor_MailerSend_Helpers_Builder_Recipient::class);
            $cc_mapped = CVendor_MailerSend_Helpers_GeneralHelpers::mapToArray($params->getCc(), CVendor_MailerSend_Helpers_Builder_Recipient::class);
            $bcc_mapped = CVendor_MailerSend_Helpers_GeneralHelpers::mapToArray($params->getBcc(), CVendor_MailerSend_Helpers_Builder_Recipient::class);
            $attachments_mapped = CVendor_MailerSend_Helpers_GeneralHelpers::mapToArray($params->getAttachments(), CVendor_MailerSend_Helpers_Builder_Attachment::class);
            $variables_mapped = CVendor_MailerSend_Helpers_GeneralHelpers::mapToArray($params->getVariables(), CVendor_MailerSend_Helpers_Builder_Variable::class);
            $personalization_mapped = CVendor_MailerSend_Helpers_GeneralHelpers::mapToArray($params->getPersonalization(), CVendor_MailerSend_Helpers_Builder_Personalization::class);

            $requestData[] = array_filter(
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
                ],
                fn ($v) => is_array($v) ? array_filter($v, fn ($v) => $v !== null) : $v !== null
            );
        }

        return $this->httpLayer->post(
            $this->buildUri($this->endpoint),
            $requestData
        );
    }

    /**
     * @param string $bulkEmailId
     * @return array
     * @throws \JsonException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function getStatus(string $bulkEmailId): array
    {
        CVendor_MailerSend_Helpers_GeneralHelpers::assert(
            fn () => Assertion::minLength($bulkEmailId, 1, 'Bulk email id is required.')
        );

        return $this->httpLayer->get(
            $this->buildUri("$this->endpoint/$bulkEmailId")
        );
    }
}
