<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Debugger helper class.
 */
class cdbg {

    protected static $deprecated_has_run = false;
    
    /**
     * A collapse icon, using in the dump_var function to allow collapsing
     * an array or object
     *
     * @access  public
     * @since   1.0.000
     * @static
     * @var     string
     */
    public static $icon_collapse = 'iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAMAAADXT/YiAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA2RpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDo3MjlFRjQ2NkM5QzJFMTExOTA0MzkwRkI0M0ZCODY4RCIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpFNzFDNDQyNEMyQzkxMUUxOTU4MEM4M0UxRDA0MUVGNSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpFNzFDNDQyM0MyQzkxMUUxOTU4MEM4M0UxRDA0MUVGNSIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M1IFdpbmRvd3MiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo3NDlFRjQ2NkM5QzJFMTExOTA0MzkwRkI0M0ZCODY4RCIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo3MjlFRjQ2NkM5QzJFMTExOTA0MzkwRkI0M0ZCODY4RCIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PuF4AWkAAAA2UExURU9t2DBStczM/1h16DNmzHiW7iNFrypMvrnD52yJ4ezs7Onp6ejo6P///+Tk5GSG7D9h5SRGq0Q2K74AAAA/SURBVHjaLMhZDsAgDANRY3ZISnP/y1ZWeV+jAeuRSky6cKL4ryDdSggP8UC7r6GvR1YHxjazPQDmVzI/AQYAnFQDdVSJ80EAAAAASUVORK5CYII=';

    /**
     * A collapse icon, using in the dump_var function to allow collapsing
     * an array or object
     *
     * @access  public
     * @since   1.0.000
     * @static
     * @var     string
     */
    public static $icon_expand = 'iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAMAAADXT/YiAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA2RpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDo3MTlFRjQ2NkM5QzJFMTExOTA0MzkwRkI0M0ZCODY4RCIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpFQzZERTJDNEMyQzkxMUUxODRCQzgyRUNDMzZEQkZFQiIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpFQzZERTJDM0MyQzkxMUUxODRCQzgyRUNDMzZEQkZFQiIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M1IFdpbmRvd3MiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo3MzlFRjQ2NkM5QzJFMTExOTA0MzkwRkI0M0ZCODY4RCIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo3MTlFRjQ2NkM5QzJFMTExOTA0MzkwRkI0M0ZCODY4RCIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PkmDvWIAAABIUExURU9t2MzM/3iW7ubm59/f5urq85mZzOvr6////9ra38zMzObm5rfB8FZz5myJ4SNFrypMvjBStTNmzOvr+mSG7OXl8T9h5SRGq/OfqCEAAABKSURBVHjaFMlbEoAwCEPRULXF2jdW9r9T4czcyUdA4XWB0IgdNSybxU9amMzHzDlPKKu7Fd1e6+wY195jW0ARYZECxPq5Gn8BBgCr0gQmxpjKAwAAAABJRU5ErkJggg==';

    public static function var_dump($var, $return = FALSE) {
        $html = '<pre style="margin-bottom: 18px;' .
                'background: #f7f7f9;' .
                'border: 1px solid #e1e1e8;' .
                'padding: 8px;' .
                'border-radius: 4px;' .
                '-moz-border-radius: 4px;' .
                '-webkit-border radius: 4px;' .
                'display: block;' .
                'font-size: 12.05px;' .
                'white-space: pre-wrap;' .
                'word-wrap: break-word;' .
                'color: #333;' .
                'font-family: Menlo,Monaco,Consolas,\'Courier New\',monospace;">';
        $html .= self::var_dump_plain($var);
        $html .= '</pre>';

        if (!$return) {
            echo $html;
        } else {
            return $html;
        }
    }

    public static function var_dump_plain($var) {
        $html = '';

        if (is_bool($var)) {
            $html .= '<span style="color:#588bff;">bool</span><span style="color:#999;">(</span><strong>' . ( ( $var ) ? 'true' : 'false' ) . '</strong><span style="color:#999;">)</span>';
        } else if (is_int($var)) {
            $html .= '<span style="color:#588bff;">int</span><span style="color:#999;">(</span><strong>' . $var . '</strong><span style="color:#999;">)</span>';
        } else if (is_float($var)) {
            $html .= '<span style="color:#588bff;">float</span><span style="color:#999;">(</span><strong>' . $var . '</strong><span style="color:#999;">)</span>';
        } else if (is_string($var)) {
            $html .= '<span style="color:#588bff;">string</span><span style="color:#999;">(</span>' . strlen($var) . '<span style="color:#999;">)</span> <strong>"' . c::htmlentities($var) . '"</strong>';
        } else if (is_null($var)) {
            $html .= '<strong>NULL</strong>';
        } else if (is_resource($var)) {
            $html .= '<span style="color:#588bff;">resource</span>("' . get_resource_type($var) . '") <strong>"' . $var . '"</strong>';
        } else if (is_array($var)) {
            $uuid = 'include-php-' . uniqid();

            $html .= '<span style="color:#588bff;">array</span>(' . count($var) . ')';

            if (!empty($var)) {
                $html .= ' <img id="' . $uuid . '" data-expand="data:image/png;base64,' . self::$icon_expand . '" style="position:relative;left:-5px;top:-1px;cursor:pointer;" src="data:image/png;base64,' . self::$icon_collapse . '" /><br /><span id="' . $uuid . '-collapsable">[<br />';

                $indent = 4;
                $longest_key = 0;

                foreach ($var as $key => $value) {
                    if (is_string($key)) {
                        $longest_key = max($longest_key, strlen($key) + 2);
                    } else {
                        $longest_key = max($longest_key, strlen($key));
                    }
                }

                foreach ($var as $key => $value) {
                    if (is_numeric($key)) {
                        $html .= str_repeat(' ', $indent) . str_pad($key, $longest_key, ' ');
                    } else {
                        $html .= str_repeat(' ', $indent) . str_pad('"' . c::htmlentities($key) . '"', $longest_key, ' ');
                    }

                    $html .= ' => ';

                    $value = explode('<br />', self::var_dump_plain($value));

                    foreach ($value as $line => $val) {
                        if ($line != 0) {
                            $value[$line] = str_repeat(' ', $indent * 2) . $val;
                        }
                    }

                    $html .= implode('<br />', $value) . '<br />';
                }

                $html .= ']</span>';

                $html .= preg_replace('/ +/', ' ', '<script type="text/javascript">(function() {
				var img = document.getElementById("' . $uuid . '");
				img.onclick = function() {
					if ( document.getElementById("' . $uuid . '-collapsable").style.display == "none" ) {
						document.getElementById("' . $uuid . '-collapsable").style.display = "inline";
						img.src = img.getAttribute("data-collapse");
						var previousSibling = document.getElementById("' . $uuid . '-collapsable").previousSibling;

						while ( previousSibling != null && ( previousSibling.nodeType != 1 || previousSibling.tagName.toLowerCase() != "br" ) ) {
							previousSibling = previousSibling.previousSibling;
						}

						if ( previousSibling != null && previousSibling.tagName.toLowerCase() == "br" ) {
							previousSibling.style.display = "inline";
						}
					} else {
						document.getElementById("' . $uuid . '-collapsable").style.display = "none";
						img.setAttribute( "data-collapse", img.getAttribute("src") );
						img.src = img.getAttribute("data-expand");
						var previousSibling = document.getElementById("' . $uuid . '-collapsable").previousSibling;

						while ( previousSibling != null && ( previousSibling.nodeType != 1 || previousSibling.tagName.toLowerCase() != "br" ) ) {
							previousSibling = previousSibling.previousSibling;
						}

						if ( previousSibling != null && previousSibling.tagName.toLowerCase() == "br" ) {
							previousSibling.style.display = "none";
						}
					}
				};
				})();
				</script>');
            }
        } else if (is_object($var)) {
            $uuid = 'include-php-' . uniqid();

            $html .= '<span style="color:#588bff;">object</span>(' . get_class($var) . ') <img id="' . $uuid . '" data-expand="data:image/png;base64,' . self::$icon_expand . '" style="position:relative;left:-5px;top:-1px;cursor:pointer;" src="data:image/png;base64,' . self::$icon_collapse . '" /><br /><span id="' . $uuid . '-collapsable">[<br />';

            $original = $var;
            $var = (array) $var;

            $indent = 4;
            $longest_key = 0;

            foreach ($var as $key => $value) {
                if (substr($key, 0, 2) == "\0*") {
                    unset($var[$key]);
                    $key = 'protected:' . substr($key, 2);
                    $var[$key] = $value;
                } else if (substr($key, 0, 1) == "\0") {
                    unset($var[$key]);
                    $key = 'private:' . substr($key, 1, strpos(substr($key, 1), "\0")) . ':' . substr($key, strpos(substr($key, 1), "\0") + 1);
                    $var[$key] = $value;
                }

                if (is_string($key)) {
                    $longest_key = max($longest_key, strlen($key) + 2);
                } else {
                    $longest_key = max($longest_key, strlen($key));
                }
            }

            foreach ($var as $key => $value) {
                if (is_numeric($key)) {
                    $html .= str_repeat(' ', $indent) . str_pad($key, $longest_key, ' ');
                } else {
                    $html .= str_repeat(' ', $indent) . str_pad('"' . c::htmlentities($key) . '"', $longest_key, ' ');
                }

                $html .= ' => ';

                $value = explode('<br />', self::var_dump_plain($value));

                foreach ($value as $line => $val) {
                    if ($line != 0) {
                        $value[$line] = str_repeat(' ', $indent * 2) . $val;
                    }
                }

                $html .= implode('<br />', $value) . '<br />';
            }

            $html .= ']</span>';

            $html .= preg_replace('/ +/', ' ', '<script type="text/javascript">(function() {
			var img = document.getElementById("' . $uuid . '");
			img.onclick = function() {
				if ( document.getElementById("' . $uuid . '-collapsable").style.display == "none" ) {
					document.getElementById("' . $uuid . '-collapsable").style.display = "inline";
					img.src = img.getAttribute("data-collapse");
					var previousSibling = document.getElementById("' . $uuid . '-collapsable").previousSibling;

					while ( previousSibling != null && ( previousSibling.nodeType != 1 || previousSibling.tagName.toLowerCase() != "br" ) ) {
						previousSibling = previousSibling.previousSibling;
					}

					if ( previousSibling != null && previousSibling.tagName.toLowerCase() == "br" ) {
						previousSibling.style.display = "inline";
					}
				} else {
					document.getElementById("' . $uuid . '-collapsable").style.display = "none";
					img.setAttribute( "data-collapse", img.getAttribute("src") );
					img.src = img.getAttribute("data-expand");
					var previousSibling = document.getElementById("' . $uuid . '-collapsable").previousSibling;

					while ( previousSibling != null && ( previousSibling.nodeType != 1 || previousSibling.tagName.toLowerCase() != "br" ) ) {
						previousSibling = previousSibling.previousSibling;
					}

					if ( previousSibling != null && previousSibling.tagName.toLowerCase() == "br" ) {
						previousSibling.style.display = "none";
					}
				}
			};
			})();
			</script>');
        }

        return $html;
    }

    public static function deprecated($message = '') {
        //run just once to make this performance good
        
        
        if(self::$deprecated_has_run){
            return true;
        }
        if(!self::$deprecated_has_run) {
            self::$deprecated_has_run=true;
        }
        
        $backtrace = debug_backtrace();
        $full_function_1 = '';
        $full_function_2 = '';
        if (count($backtrace) > 1) {
            $state = $backtrace[1];
            $function = carr::get($state, 'function', '');
            $class = carr::get($state, 'class', '');
            $type = carr::get($state, 'type', '');
            $line = carr::get($state, 'line', '');
            $line_str = '';
            if (strlen($line) > 0) {
                $line_str = ' on line ' . $line;
            }
            $full_function_1 = $class . $type . $function . $line_str;
        }
        if (count($backtrace) > 2) {
            $state = $backtrace[2];
            $function = carr::get($state, 'function', '');
            $class = carr::get($state, 'class', '');
            $type = carr::get($state, 'type', '');
            $line = carr::get($state, 'line', '');
            $line_str = '';
            if (strlen($line) > 0) {
                $line_str = ' on line ' . $line;
            }
            $full_function_2 = $class . $type . $function . $line_str;
        }
        $subject = 'CApp Deprecated on '.CF::domain().' '.date('Y-m-d H:i:s');

        if (strlen($message) > 0) {
            $body = '<p>' . $message . '</p>';
        }
        $body .= '<br/><br/>';
        $body .= '<h4>CApp Deprecated on calling function ' . $full_function_1 . '<h4>';
        if(strlen($full_function_2)) {
            $body .= '<h4>before calling function ' . $full_function_2 . '<h4>';
        }
        $body .= '<br/><br/>';
        $body .= 'Domain:'.CF::domain().'<br/>';
        $body .= 'App Code:'.CF::app_code().'<br/>';
        $body .= 'Org Code:'.CF::org_code().'<br/>';
        $body .= 'User Agent:'.crequest::user_agent().'<br/>';
        $body .= 'Remote Address:'.crequest::remote_address().'<br/>';
        $body .= 'Browser:'.crequest::browser().'<br/>';
        $body .= '<br/><br/>';
        
        $backtrace = array_slice($backtrace, 0, 5, true);
        $body .= cdbg::var_dump($backtrace, true);
        try {
            cmail::send_smtp('hery@ittron.co.id', $subject, $body);
        } catch (Exception $ex) {
            echo "Error Email Deprecated".$ex->getMessage();
        }
        return true;
       
    }

}
