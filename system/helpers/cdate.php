<?php

//@codingStandardsIgnoreStart
class cdate {
    //@codingStandardsIgnoreEnd

    /**
     * Calculate Age
     *
     * @param string $birthday
     *
     * @return void
     */
    public static function age($birthday) {
        $diffYear = (date('md', strtotime($birthday)) > date('md') ? (date('Y') - date('Y', strtotime($birthday)) - 1) : (date('Y') - date('Y', strtotime($birthday))));
        $diffMonth = (date('d', strtotime($birthday)) > date('d') ? (date('m') - date('m', strtotime($birthday)) - 1) : (date('m') - date('m', strtotime($birthday))));
        return $diffYear;
    }
}
