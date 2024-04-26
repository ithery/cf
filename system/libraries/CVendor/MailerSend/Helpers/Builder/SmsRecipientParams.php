<?php

class CVendor_MailerSend_Helpers_Builder_SmsRecipientParams
{
    protected ?string $sms_number_id = null;
    protected ?int $page = null;
    protected ?int $limit = null;
    protected ?string $status = null;

    public function getSmsNumberId(): ?string
    {
        return $this->sms_number_id;
    }

    public function setSmsNumberId(?string $sms_number_id): CVendor_MailerSend_Helpers_Builder_SmsRecipientParams
    {
        $this->sms_number_id = $sms_number_id;
        return $this;
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function setPage(?int $page): CVendor_MailerSend_Helpers_Builder_SmsRecipientParams
    {
        $this->page = $page;
        return $this;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setLimit(?int $limit): CVendor_MailerSend_Helpers_Builder_SmsRecipientParams
    {
        $this->limit = $limit;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): CVendor_MailerSend_Helpers_Builder_SmsRecipientParams
    {
        $this->status = $status;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'sms_number_id' => $this->getSmsNumberId(),
            'page' => $this->getPage(),
            'limit' => $this->getLimit(),
            'status' => $this->getStatus(),
        ];
    }
}
