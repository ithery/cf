<?php

/** @mixin CPeriod */
trait CPeriod_Trait_ComparisonTrait {
    public function startsBefore(DateTimeInterface $date) {
        return $this->includedStart() < $date;
    }

    public function startsBeforeOrAt(DateTimeInterface $date) {
        return $this->includedStart() <= $date;
    }

    public function startsAfter(DateTimeInterface $date) {
        return $this->includedStart() > $date;
    }

    public function startsAfterOrAt(DateTimeInterface $date) {
        return $this->includedStart() >= $date;
    }

    public function startsAt(DateTimeInterface $date) {
        return $this->includedStart()->getTimestamp()
            === $this->precision->roundDate($date)->getTimestamp();
    }

    public function endsBefore(DateTimeInterface $date) {
        return $this->includedEnd() < $this->precision->roundDate($date);
    }

    public function endsBeforeOrAt(DateTimeInterface $date) {
        return $this->includedEnd() <= $this->precision->roundDate($date);
    }

    public function endsAfter(DateTimeInterface $date) {
        return $this->includedEnd() > $this->precision->roundDate($date);
    }

    public function endsAfterOrAt(DateTimeInterface $date) {
        return $this->includedEnd() >= $this->precision->roundDate($date);
    }

    public function endsAt(DateTimeInterface $date) {
        return $this->includedEnd()->getTimestamp()
            === $this->precision->roundDate($date)->getTimestamp();
    }

    /**
     * @param CPeriod $period
     *
     * @return bool
     */
    public function overlapsWith(CPeriod $period) {
        $this->ensurePrecisionMatches($period);

        if ($this->includedStart() > $period->includedEnd()) {
            return false;
        }

        if ($period->includedStart() > $this->includedEnd()) {
            return false;
        }

        return true;
    }

    /**
     * @param CPeriod $other
     *
     * @return bool
     */
    public function touchesWith(CPeriod $other) {
        $this->ensurePrecisionMatches($other);

        if ($this->includedEnd() < $other->includedStart()) {
            /*
             * [=======]
             *          [======]
             */
            $intervalBetween = $this->precision->roundDate($this->includedEnd()->add($this->interval))
                ->diff(
                    $other->precision->roundDate($other->includedStart())
                );
        } elseif ($this->includedStart() > $other->includedEnd()) {
            /*
             *          [=====]
             *  [======]
             */
            $intervalBetween = $other->precision->roundDate($other->includedEnd()->add($other->interval))
                ->diff(
                    $this->precision->roundDate($this->includedStart())
                );
        } else {
            return false;
        }

        foreach (['y', 'm', 'd', 'h', 'i', 's'] as $field) {
            if ($intervalBetween->{$field} === 0) {
                continue;
            }

            return false;
        }

        return true;
    }

    /**
     * @param DateTimeInterface|CPeriod $other
     *
     * @return bool
     */
    public function contains($other) {
        if ($other instanceof CPeriod) {
            return $this->includedStart() <= $other->includedStart()
                && $this->includedEnd() >= $other->includedEnd();
        }

        $roundedDate = $this->precision->roundDate($other);

        return $roundedDate >= $this->includedStart() && $roundedDate <= $this->includedEnd();
    }

    /**
     * @param CPeriod $period
     *
     * @return bool
     */
    public function equals(CPeriod $period) {
        $this->ensurePrecisionMatches($period);

        if ($period->includedStart()->getTimestamp() !== $this->includedStart()->getTimestamp()) {
            return false;
        }

        if ($period->includedEnd()->getTimestamp() !== $this->includedEnd()->getTimestamp()) {
            return false;
        }

        return true;
    }
}
