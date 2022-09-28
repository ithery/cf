<?php

trait CPeriod_Trait_DateTimeCopierTrait {
    /**
     * @param DateTimeInterface $date
     *
     * @return \DateTime|\DateTimeImmutable
     */
    protected function copyDateTime(DateTimeInterface $date) {
        return $date instanceof DateTimeImmutable ? $date : clone $date;
    }

    /**
     * @param DateTimeInterface $date
     *
     * @return DateTimeInterface
     */
    protected function yesterday(DateTimeInterface $date) {
        return $this->copyDateTime($date)->modify('-1 day');
    }
}
