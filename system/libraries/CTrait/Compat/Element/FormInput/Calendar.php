<?php

defined('SYSPATH') or die('No direct access allowed.');

//@codingStandardsIgnoreStart
trait CTrait_Compat_Element_FormInput_Calendar {
    /**
     * @param string $query
     *
     * @deprecated 1.5
     *
     * @return $this
     */
    public function set_query($query) {
        return $this->setQuery($query);
    }

    /**
     * @param mixed $events
     *
     * @deprecated 1.5
     *
     * @return $this
     */
    public function set_events($events) {
        return $this->setEvents($events);
    }
}
