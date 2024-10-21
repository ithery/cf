<?php

class CVendor_MailerSend_Helpers_Builder_BlocklistParams extends CVendor_MailerSend_Helpers_Builder_SuppressionParams
{
    private array $patterns = [];

    /**
     * @return array
     */
    public function getPatterns(): array
    {
        return $this->patterns;
    }

    /**
     * @param array $patterns
     */
    public function setPatterns(array $patterns): CVendor_MailerSend_Helpers_Builder_BlocklistParams
    {
        $this->patterns = $patterns;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'domain_id' => $this->getDomainId(),
            'recipients' => $this->getRecipients(),
            'patterns' => $this->getPatterns(),
        ];
    }
}
