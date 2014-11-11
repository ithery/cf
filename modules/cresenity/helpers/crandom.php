<?php

    class crandom {

        public static function readable_random_string($length = 6) {
            $conso = array("b", "c", "d", "f", "g", "h", "j", "k", "l", "m", "n",
                "p", "r", "s", "t", "v", "w", "x", "y", "z");
            $vocal = array("a", "e", "i", "o", "u");
            $password = "";
            srand((double) microtime() * 1000000);
            $max = $length / 2;
            for ($i = 1; $i <= $max; $i++) {
                $password.=$conso[rand(0, 19)];
                $password.=$vocal[rand(0, 4)];
            }
            return $password;
        }

        public static function random_string($length, $human_friendly = TRUE, $include_symbols = FALSE, $no_duplicate_chars = FALSE) {
            $nice_chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefhjkmnprstuvwxyz23456789';
            $all_an = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
            $symbols = '!@#$%^&*()~_-=+{}[]|:;<>,.?/"\'\\`';
            $string = '';

            // Determine the pool of available characters based on the given parameters
            if ($human_friendly) {
                $pool = $nice_chars;
            }
            else {
                $pool = $all_an;

                if ($include_symbols) {
                    $pool .= $symbols;
                }
            }

            // Don't allow duplicate letters to be disabled if the length is
            // longer than the available characters
            if ($no_duplicate_chars && strlen($pool) < $length) {
                throw new LengthException('$length exceeds the size of the pool and $no_duplicate_chars is enabled');
            }

            // Convert the pool of characters into an array of characters and
            // shuffle the array
            $pool = str_split($pool);
            shuffle($pool);

            // Generate our string
            for ($i = 0; $i < $length; $i++) {
                if ($no_duplicate_chars) {
                    $string .= array_shift($pool);
                }
                else {
                    $string .= $pool[0];
                    shuffle($pool);
                }
            }

            return $string;
        }

        function random_string_2($l) {
            $c = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
            srand((double) microtime() * 1000000);
            for ($i = 0; $i < $l; $i++) {
                $rand.= $c[rand() % strlen($c)];
            }
            return $rand;
        }

    }