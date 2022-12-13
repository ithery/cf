<?php
class CMetric_Flux_FluxSelect {
    private $select;

    /**
     * @param string[] $fields
     *
     * @return CMetric_Flux_FluxSelect
     */
    public function fields(array $fields) {
        $sp = 'r._field == ' . implode(' or r._field == ', c::collect($fields)->map(function ($s) {
            return '"' . $s . '"';
        })->toArray());
        if (!$this->select) {
            $this->select = $sp;
        } else {
            $this->select .= ' and ' . $sp;
        }

        return $this;
    }

    /**
     * @param string[] $fields
     *
     * @return CMetric_Flux_FluxSelect
     */
    public function tags(array $fields) {
        $sp = 'r.tag == ' . implode(' or r.tag == ', c::collect($fields)->map(function ($s) {
            return '"' . $s . '"';
        })->toArray());
        if (!$this->select) {
            $this->select = $sp;
        } else {
            $this->select .= ' and ' . $sp;
        }

        return $this;
    }

    public function getSelect() {
        return $this->select;
    }
}
