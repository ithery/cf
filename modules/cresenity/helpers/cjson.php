<?php

class cjson {

    public static function encode($json) {

        if (!is_string($json)) {
            if (phpversion() && phpversion() >= 5.4) {
                $jsonEncoded = json_encode($json, JSON_PRETTY_PRINT);

                return $jsonEncoded;
            }
            $json = json_encode($json);

            $result = '';
            $pos = 0;               // indentation level
            $strLen = strlen($json);
            $indentStr = "\t";
            $newLine = "\n";
            $prevChar = '';
            $outOfQuotes = true;

            for ($i = 0; $i < $strLen; $i++) {
                // Grab the next character in the string
                $char = substr($json, $i, 1);

                // Are we inside a quoted string?
                if ($char == '"' && $prevChar != '\\') {
                    $outOfQuotes = !$outOfQuotes;
                }
                // If this character is the end of an element,
                // output a new line and indent the next line
                else if (($char == '}' || $char == ']') && $outOfQuotes) {
                    $result .= $newLine;
                    $pos--;
                    for ($j = 0; $j < $pos; $j++) {
                        $result .= $indentStr;
                    }
                }
                // eat all non-essential whitespace in the input as we do our own here and it would only mess up our process
                else if ($outOfQuotes && false !== strpos(" \t\r\n", $char)) {
                    continue;
                }

                // Add the character to the result string
                $result .= $char;
                // always add a space after a field colon:
                if ($char == ':' && $outOfQuotes) {
                    $result .= ' ';
                }

                // If the last character was the beginning of an element,
                // output a new line and indent the next line
                if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
                    $result .= $newLine;
                    if ($char == '{' || $char == '[') {
                        $pos++;
                    }
                    for ($j = 0; $j < $pos; $j++) {
                        $result .= $indentStr;
                    }
                }
                $prevChar = $char;
            }
        } else {
            $result = json_encode($json);
        }

        return $result;
    }

    public static function decode($json) {
        if (is_string($json)) {
            return json_decode($json, true);
        }
        $result = array();
        return $result;
    }

}

?>