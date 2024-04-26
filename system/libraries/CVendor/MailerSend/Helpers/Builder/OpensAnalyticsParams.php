<?php

class CVendor_MailerSend_Helpers_Builder_OpensAnalyticsParams
{
    protected ?string $domain_id = null;
    protected int $date_from;
    protected int $date_to;
    protected array $tags = [];

    public function __construct(int $date_from, int $date_to)
    {
        $this->setDateFrom($date_from);
        $this->setDateTo($date_to);
    }

    public function getDomainId(): ?string
    {
        return $this->domain_id;
    }

    public function setDomainId(?string $domain_id): CVendor_MailerSend_Helpers_Builder_OpensAnalyticsParams
    {
        $this->domain_id = $domain_id;
        return $this;
    }

    public function getDateFrom(): int
    {
        return $this->date_from;
    }

    protected function setDateFrom(int $date_from): void
    {
        $this->date_from = $date_from;
    }

    public function getDateTo(): int
    {
        return $this->date_to;
    }

    protected function setDateTo(int $date_to): void
    {
        $this->date_to = $date_to;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags): CVendor_MailerSend_Helpers_Builder_OpensAnalyticsParams
    {
        $this->tags = $tags;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'domain_id' => $this->getDomainId(),
            'date_from' => $this->getDateFrom(),
            'date_to' => $this->getDateTo(),
            'tags' => $this->getTags(),
        ];
    }
}
