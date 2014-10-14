<?php

class LX300 {

    public function start_bold() {
        return chr(27) . chr(69);
    }

    public function stop_bold() {
        return chr(27) . chr(70);
    }

    public function start_italic() {
        return chr(27) . chr(52);
    }

    public function stop_italic() {
        return chr(27) . chr(53);
    }

    public function start_underline() {
        return chr(27) . chr(45) . chr(49);
    }

    public function stop_underline() {
        return chr(27) . chr(45) . chr(48);
    }

    public function start_justify() {
        return chr(27, 97, 3);
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
        return chr(27) . chr(67) . chr(0). chr(32);
//        return chr(27). chr(78). chr(2);
//        return chr(27) . chr(25) . chr(50);

        }

        public function stop_line() {
//        return chr(20);
        return chr ( 27) . chr(79);
        }

}