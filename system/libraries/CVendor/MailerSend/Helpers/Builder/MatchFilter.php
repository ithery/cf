<?php

use Illuminate\Contracts\Support\Arrayable;

class CVendor_MailerSend_Helpers_Builder_MatchFilter implements Arrayable, \JsonSerializable {
    protected string $type;

    protected array $filters = [];

    public function __construct(string $type) {
        $this->type = $type;
    }

    public function getType(): string {
        return $this->type;
    }

    public function setType(string $type): self {
        $this->type = $type;

        return $this;
    }

    public function getFilters(): array {
        return $this->filters;
    }

    public function setFilters(array $filters): self {
        $this->filters = $filters;

        return $this;
    }

    public function addFilter(CVendor_MailerSend_Helpers_Builder_Filter $filter): self {
        $this->filters[] = $filter;

        return $this;
    }

    public function toArray(): array {
        $array = [
            'type' => $this->getType(),
        ];

        if (count($this->getFilters()) > 0) {
            carr::set($array, 'filters', $this->getFilters());
        }

        return $array;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize() {
        return $this->toArray();
    }
}
