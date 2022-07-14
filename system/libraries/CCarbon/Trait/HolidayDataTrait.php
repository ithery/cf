<?php

trait CCarbon_Trait_HolidayDataTrait {
    public $holidayData = [];

    /**
     * Get stored data set for the a given holiday ID.
     *
     * @return \Closure
     */
    public function getHolidayDataById() {
        $mixin = $this;

        /**
         * Get stored data set for the a given holiday ID.
         *
         * @param string $id
         *
         * @return null|array
         */
        return static function ($id) use ($mixin) {
            return isset($mixin->holidayData[$id]) ? $mixin->holidayData[$id] : [];
        };
    }

    /**
     * Set stored data set for the a given holiday ID.
     *
     * @return \Closure
     */
    public function setHolidayDataById() {
        $mixin = $this;

        /**
         * Set stored data set for the a given holiday ID.
         *
         * @return null|$this
         */
        return static function ($id, array $data) use ($mixin) {
            $mixin->holidayData[$id] = $data;

            return isset($this) && CCarbon_Context::isNotMixin($this, $mixin) ? $this : null;
        };
    }

    /**
     * Get stored data set for the current holiday or null if it's not a holiday.
     *
     * @return \Closure
     */
    public function getHolidayData() {
        $mixin = $this;

        /**
         * Get stored data set for the current holiday or null if it's not a holiday.
         *
         * @return null|array
         */
        return static function () {
            /** @var Carbon|CCarbon_BusinessDay $self */
            $self = static::this();
            $holidayId = $self->getHolidayId();

            if (!$holidayId) {
                return null;
            }

            return $self::getHolidayDataById($holidayId);
        };
    }

    /**
     * Set stored data set for the current holiday, does nothing if it's not a holiday.
     *
     * @return \Closure
     */
    public function setHolidayData() {
        /**
         * Set stored data set for the current holiday, does nothing if it's not a holiday.
         *
         * @return null|$this
         */
        return static function (array $data) {
            /** @var Carbon|BusinessDay $self */
            $self = static::this();
            $holidayId = $self->getHolidayId();

            if (!$holidayId) {
                return null;
            }

            $carbonClass = get_class($self);

            return $carbonClass::setHolidayDataById($holidayId, $data);
        };
    }
}
