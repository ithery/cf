<?php

class CRawPrintLX300Builder extends CRawPrintBuilder {
    /*
     * esc$=CHR$(27)
      emph$=esc$+"E"
      emphOFF$=esc$+"F"
      dubStrike$=esc$+"G"
      dubStrikeOFF$=esc$+"H"

      Then you can build up multiples like...

      bold$=emph$ + dubStrike$
      boldOFF$=emphOFF$+dubStrikeOFF$

      $initialized = chr(27).chr(64);
      $condensed1 = chr(15);
      $condensed0 = chr(18);
      $elongated1 = chr(27).chr(87).chr(1);
      $elongated0 = chr(27).chr(87).chr(0);
      $draftmode = chr(27).chr(120).chr(0);
      $lqmode = chr(27).chr(120).chr(1);
      $doublestrike1 = chr(27).chr(71);
      $doublestrike0 = chr(27).chr(72);
      $italic1 = chr(27).chr(52);
      $italic0 = chr(27).chr(53);
      $doubleheight1 = chr(27).chr(119).chr(1);
      $doubleheight0 = chr(27).chr(119).chr(0);
      $print10cpi = chr(27).chr(80);
      $print12cpi = chr(27).chr(77);
      $elongatedline1 = chr(14);
      $elongatedline0 = chr(20);
      $changefont1 = chr(27).chr(120).chr(49);
      $changefont0 = chr(27).chr(120).chr(48);
      $roman = chr(27).chr(107).chr(48);
      $sans = chr(27).chr(107).chr(49);
      $draftmode = chr(27).chr(120).chr(48);
      $underline1 = chr(27).chr(45).chr(49);
      $underline0 =chr(27).chr(45).chr(48);
      $small =chr(27).chr(33).chr(5);
      $newpage = chr(12);
      $movetoleft = chr(13);
      $linefeed = chr(10);
      $sizekwt = chr(27)."1".chr(27).chr(67)."9";
      $sizerek = chr(27)."2".chr(27).chr(67)."66";

     * * 
     * 
     */

    public function start_bold() {
        $this->escape_code(chr(69));
    }

    public function stop_bold() {
        $this->escape_code(chr(70));
    }

    public function start_italic() {
        $this->escape_code(chr(52));
    }

    public function stop_italic() {
        $this->escape_code(chr(53));
    }

    public function start_underline() {
        $this->add_code(chr(45) . chr(49));
    }

    public function stop_underline() {
        $this->add_code(chr(45) . chr(48));
    }

    public function goto_next_page() {
//        return chr(27) . chr(78). chr(6); //skip to start over perforation
//        return chr(12); //skip 2 page 
//        return chr(27) . chr(67); //numpuk tulisan
//        return chr(27) . chr(57); //numpuk tulisan ke atas
//        return chr(27). chr(105).chr(0); //numpuk
//        return chr(27) . chr(14);//numpuk
//        return chr(27) . chr(60);//memotong karakter
//        return chr(14);//start tulisan besar
//        return chr(24). chr(120).chr(0);//memotong karakter
        return chr(12); //next page
//        return chr(10);//line feed
//        return chr(27) . "1" . chr(27) . chr(67) . "9";
//        return chr(27).chr(64);//numpuk
//        return chr(27) . chr(71) . chr(31); //membuat line
//        return chr(13);
    }

    public function stop_goto_next_page() {
        $n = 5;
//        return chr(27) . chr(79);//skip to stop over perforation
//        return chr(20);//stop tulisan besar
    }

    public function start_line() {
//        return chr(14);
//        return chr(27) . chr(65) . chr(5);
//        return chr(27) . chr(49);//line 7/72 inchi
//        return chr(27) . chr(51) . chr(3);//numpuk
//        return chr(10);
//        return chr(12);
        return chr(27) . chr(51) . chr(127);
//        return chr(27). chr(78). chr(2);
//        return chr(27) . chr(25) . chr(50);
    }

    public function stop_line() {
//        return chr(20);
        return chr(27) . chr(79);
    }

}
