<?php

class CVendor_MailerSend_Helpers_Builder_ActivityParams
{
    protected ?int $page = null;
    protected ?int $limit = null;
    protected ?int $date_from = null;
    protected ?int $date_to = null;
    protected array $event = [];

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function setPage(?int $page): CVendor_MailerSend_Helpers_Builder_ActivityParams
    {
        $this->page = $page;
        return $this;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setLimit(?int $limit): CVendor_MailerSend_Helpers_Builder_ActivityParams
    {
        $this->limit = $limit;
        return $this;
    }

    public function getDateFrom(): ?int
    {
        return $this->date_from;
    }

    public function setDateFrom(?int $date_from): CVendor_MailerSend_Helpers_Builder_ActivityParams
    {
        $this->date_from = $date_from;
        return $this;
    }

    public function getDateTo(): ?int
    {
        return $this->date_to;
    }

    public function setDateTo(?int $date_to): CVendor_MailerSend_Helpers_Builder_ActivityParams
    {
        $this->date_to = $date_to;
        return $this;
    }

    public function getEvent(): array
    {
        return $this->event;
    }

    public function setEvent(array $event): CVendor_MailerSend_Helpers_Builder_ActivityParams
    {
        $this->event = $event;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'page' => $this->getPage(),
            'limit' => $this->getLimit(),
            'date_from' => $this->getDateFrom(),
            'date_to' => $this->getDateTo(),
            'event' => $this->getEvent(),
        ];
    }
}
