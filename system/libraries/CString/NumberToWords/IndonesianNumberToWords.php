<?php

use Symfony\Component\Routing\Exception\InvalidParameterException;

class CString_NumberToWords_IndonesianNumberToWords {
    protected static $numberLength = 15;

    /**
     * @param int $value
     *
     * @return string
     */
    public static function toWords($value) {
        if (strlen((string) $value) > self::$numberLength) {
            throw new InvalidParameterException('Number length must be equal or less then 15 chars.');
        }

        return (new self())->numberToWords($value);
    }

    protected function getUnder1000($val) {
        $C_NUMBER = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan'];
        $res = '';
        if ($val >= 1000) {
            return $res;
        }
        //process hundred
        $tempv = $val;
        if ($tempv >= 100) {
            if (floor($tempv / 100) == 1) {
                $res = $res . ' Seratus';
            } else {
                $res .= ' ' . $C_NUMBER[floor($tempv / 100)] . ' Ratus';
            }
        }
        //process ten
        $tempv = $val % 100;
        if ($tempv >= 10) {
            if (floor($tempv / 10) == 1) {
                if ($tempv % 10 == 0) {
                    $res .= ' Sepuluh';
                } elseif ($tempv % 10 == 1) {
                    $res .= ' Sebelas';
                } else {
                    $res .= ' ' . $C_NUMBER[$tempv % 10] . ' Belas';
                }
            } else {
                $res .= ' ' . $C_NUMBER[floor($tempv / 10)] . ' Puluh';
                if ($tempv % 10 > 0) {
                    $res .= ' ' . $C_NUMBER[$tempv % 10];
                }
            }
        } else {
            if ($tempv % 10 > 0) {
                $res .= ' ' . $C_NUMBER[$tempv % 10];
            }
        }
        $res = trim($res);

        return $res;
    }

    public function numberToWords($val) {
        $res = '';
        $tempval = $val;
        if ($tempval >= 1000000000000) {
            $temp_under_1000 = floor($tempval / 1000000000000);
            $res = $res . ' ' . $this->getUnder1000($temp_under_1000) . ' Triliun';
        }
        $tempval = $val % 1000000000000;
        if ($tempval >= 1000000000) {
            $temp_under_1000 = floor($tempval / 1000000000);
            $res = $res . ' ' . $this->getUnder1000($temp_under_1000) . ' Miliar';
        }
        $tempval = $val % 1000000000;
        if (floor($val / 1000000) > 0) {
            $temp_under_1000 = floor($tempval / 1000000);
            $res = $res . ' ' . $this->getUnder1000($temp_under_1000) . ' Juta';
        }
        $tempval = $val % 1000000;
        if ($tempval >= 1000) {
            $temp_under_1000 = floor($tempval / 1000);
            if ($temp_under_1000 == 1) {
                $res = $res . ' Seribu';
            } else {
                $res = $res . ' ' . $this->getUnder1000($temp_under_1000) . ' Ribu';
            }
        }
        $tempval = $val % 1000;
        $res = $res . ' ' . $this->getUnder1000($tempval);
        $result = trim($res);

        return $result;
    }
}
