<?php

class CVendor_MailerSend_Helpers_Builder_SmsActivityParams
{
    protected ?string $sms_number_id = null;
    protected ?int $page = null;
    protected ?int $limit = null;
    protected ?int $date_from = null;
    protected ?int $date_to = null;
    protected array $status = [];

    public function getSmsNumberId(): ?string
    {
        return $this->sms_number_id;
    }

    public function setSmsNumberId(?string $sms_number_id): CVendor_MailerSend_Helpers_Builder_SmsActivityParams
    {
        $this->sms_number_id = $sms_number_id;
        return $this;
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function setPage(?int $page): CVendor_MailerSend_Helpers_Builder_SmsActivityParams
    {
        $this->page = $page;
        return $this;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setLimit(?int $limit): CVendor_MailerSend_Helpers_Builder_SmsActivityParams
    {
        $this->limit = $limit;
        return $this;
    }

    public function getDateFrom(): ?int
    {
        return $this->date_from;
    }

    public function setDateFrom(?int $date_from): CVendor_MailerSend_Helpers_Builder_SmsActivityParams
    {
        $this->date_from = $date_from;
        return $this;
    }

    public function getDateTo(): ?int
    {
        return $this->date_to;
    }

    public function setDateTo(?int $date_to): CVendor_MailerSend_Helpers_Builder_SmsActivityParams
    {
        $this->date_to = $date_to;
        return $this;
    }

    public function getStatus(): array
    {
        return $this->status;
    }

    public function setStatus(array $status): CVendor_MailerSend_Helpers_Builder_SmsActivityParams
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
            'date_from' => $this->getDateFrom(),
            'date_to' => $this->getDateTo(),
            'status' => $this->getStatus(),
        ];
    }
}
