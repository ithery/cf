<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Dec 25, 2017, 10:08:50 PM
 */
trait CModel_Trait_Timestamps {
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Update the model's update timestamp.
     *
     * @return bool
     */
    public function touch() {
        if (!$this->usesTimestamps()) {
            return false;
        }

        $this->updateTimestamps();

        return $this->save();
    }

    /**
     * Update the creation and update timestamps.
     *
     * @return void
     */
    protected function updateTimestamps() {
        /** @var CModel $this */
        $time = $this->freshTimestamp();
        if (!is_null(static::UPDATED) && !$this->isDirty(static::UPDATED)) {
            $this->setUpdatedAt($time);
        }

        if (!$this->exists && !$this->isDirty(static::CREATED)) {
            $this->setCreatedAt($time);
        }
    }

    /**
     * Set the value of the "created at" attribute.
     *
     * @param mixed $value
     *
     * @return $this
     */
    public function setCreatedAt($value) {
        /** @var CModel $this */
        $this->{static::CREATED} = $value;

        return $this;
    }

    /**
     * Set the value of the "updated at" attribute.
     *
     * @param mixed $value
     *
     * @return $this
     */
    public function setUpdatedAt($value) {
        /** @var CModel $this */
        $this->{static::UPDATED} = $value;

        return $this;
    }

    /**
     * Get a fresh timestamp for the model.
     *
     * @return \CCarbon
     */
    public function freshTimestamp() {
        return new CCarbon();
    }

    /**
     * Get a fresh timestamp for the model.
     *
     * @return string
     */
    public function freshTimestampString() {
        return $this->fromDateTime($this->freshTimestamp());
    }

    /**
     * Determine if the model uses timestamps.
     *
     * @return bool
     */
    public function usesTimestamps() {
        return $this->timestamps;
    }

    /**
     * Get the name of the "created at" column.
     *
     * @return string
     */
    public function getCreatedAtColumn() {
        /** @var CModel $this */
        return static::CREATED;
    }

    /**
     * Get the name of the "updated at" column.
     *
     * @return string
     */
    public function getUpdatedAtColumn() {
        /** @var CModel $this */
        return static::UPDATED;
    }
}
