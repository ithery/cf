<?php

class cformatting {
	
	
	public static function trailingslashit($string) {
		return cformatting::untrailingslashit($string) . '/';
	}
	public static function untrailingslashit($string) {
		return rtrim($string, '/');
	}
	public static function prepend_http($url) {
		if (!preg_match("/^(http|ftp):/", $$url)) $url = 'http://'.$url;  
		return $url;
	}
	function ordinal($cdnl){
		$test_c = abs($cdnl) % 10;
		$ext = ((abs($cdnl) %100 < 21 && abs($cdnl) %100 > 4) ? 'th' : (($test_c < 4) ? ($test_c < 3) ? ($test_c < 2) ? ($test_c < 1) ? 'th' : 'st' : 'nd' : 'rd' : 'th'));
		return $cdnl.$ext;
	}
	
	public static function size_format( $bytes, $decimals = 0 ) {
		$bytes = floatval( $bytes );

		if ( $bytes < 1024 ) {
			return $bytes . ' B';
		} else if ( $bytes < pow( 1024, 2 ) ) {
			return number_format( $bytes / 1024, $decimals, '.', '' ) . ' KiB';
		} else if ( $bytes < pow( 1024, 3 ) ) {
			return number_format( $bytes / pow( 1024, 2 ), $decimals, '.', '' ) . ' MiB';
		} else if ( $bytes < pow( 1024, 4 ) ) {
			return number_format( $bytes / pow( 1024, 3 ), $decimals, '.', '' ) . ' GiB';
		} else if ( $bytes < pow( 1024, 5 ) ) {
			return number_format( $bytes / pow( 1024, 4 ), $decimals, '.', '' ) . ' TiB';
		} else if ( $bytes < pow( 1024, 6 ) ) {
			return number_format( $bytes / pow( 1024, 5 ), $decimals, '.', '' ) . ' PiB';
		} else {
			return number_format( $bytes / pow( 1024, 5 ), $decimals, '.', '' ) . ' PiB';
		}
    }
	/**
         * Converts all accent characters to ASCII characters
         *
         * If there are no accent characters, then the string given is just
         * returned
         *
         * @param   string  $string  Text that might have accent characters
         * @return  string  Filtered string with replaced "nice" characters
         *
         * @link    http://codex.wordpress.org/Function_Reference/remove_accents
         *
         * @access  public
         * @since   1.0.000
         * @static
         */
	public static function remove_accents( $string ) {
		if ( ! preg_match( '/[\x80-\xff]/', $string ) ) {
			return $string;
		}

		if ( self::seems_utf8( $string ) ) {
			$chars = array(

				// Decompositions for Latin-1 Supplement
				chr(194).chr(170) => 'a', chr(194).chr(186) => 'o',
				chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
				chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
				chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
				chr(195).chr(134) => 'AE',chr(195).chr(135) => 'C',
				chr(195).chr(136) => 'E', chr(195).chr(137) => 'E',
				chr(195).chr(138) => 'E', chr(195).chr(139) => 'E',
				chr(195).chr(140) => 'I', chr(195).chr(141) => 'I',
				chr(195).chr(142) => 'I', chr(195).chr(143) => 'I',
				chr(195).chr(144) => 'D', chr(195).chr(145) => 'N',
				chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
				chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
				chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
				chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
				chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
				chr(195).chr(158) => 'TH',chr(195).chr(159) => 's',
				chr(195).chr(160) => 'a', chr(195).chr(161) => 'a',
				chr(195).chr(162) => 'a', chr(195).chr(163) => 'a',
				chr(195).chr(164) => 'a', chr(195).chr(165) => 'a',
				chr(195).chr(166) => 'ae',chr(195).chr(167) => 'c',
				chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
				chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
				chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
				chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
				chr(195).chr(176) => 'd', chr(195).chr(177) => 'n',
				chr(195).chr(178) => 'o', chr(195).chr(179) => 'o',
				chr(195).chr(180) => 'o', chr(195).chr(181) => 'o',
				chr(195).chr(182) => 'o', chr(195).chr(184) => 'o',
				chr(195).chr(185) => 'u', chr(195).chr(186) => 'u',
				chr(195).chr(187) => 'u', chr(195).chr(188) => 'u',
				chr(195).chr(189) => 'y', chr(195).chr(190) => 'th',
				chr(195).chr(191) => 'y', chr(195).chr(152) => 'O',

				// Decompositions for Latin Extended-A
				chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
				chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
				chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
				chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
				chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
				chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
				chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
				chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
				chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
				chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
				chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
				chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
				chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
				chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
				chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
				chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
				chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
				chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
				chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
				chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
				chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
				chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
				chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
				chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
				chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
				chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
				chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
				chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
				chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
				chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
				chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
				chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
				chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
				chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
				chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
				chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
				chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
				chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
				chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
				chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
				chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
				chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
				chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
				chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
				chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
				chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
				chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
				chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
				chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
				chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
				chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
				chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
				chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
				chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
				chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
				chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
				chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
				chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
				chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
				chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
				chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
				chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
				chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
				chr(197).chr(190) => 'z', chr(197).chr(191) => 's',

				// Decompositions for Latin Extended-B
				chr(200).chr(152) => 'S', chr(200).chr(153) => 's',
				chr(200).chr(154) => 'T', chr(200).chr(155) => 't',

				// Euro Sign
				chr(226).chr(130).chr(172) => 'E',
				// GBP (Pound) Sign
				chr(194).chr(163) => ''
			);

			$string = strtr( $string, $chars );
		} else {

			// Assume ISO-8859-1 if not UTF-8
			$chars['in'] = chr(128).chr(131).chr(138).chr(142).chr(154).chr(158)
				 .chr(159).chr(162).chr(165).chr(181).chr(192).chr(193).chr(194)
				 .chr(195).chr(196).chr(197).chr(199).chr(200).chr(201).chr(202)
				 .chr(203).chr(204).chr(205).chr(206).chr(207).chr(209).chr(210)
				 .chr(211).chr(212).chr(213).chr(214).chr(216).chr(217).chr(218)
				 .chr(219).chr(220).chr(221).chr(224).chr(225).chr(226).chr(227)
				 .chr(228).chr(229).chr(231).chr(232).chr(233).chr(234).chr(235)
				 .chr(236).chr(237).chr(238).chr(239).chr(241).chr(242).chr(243)
				 .chr(244).chr(245).chr(246).chr(248).chr(249).chr(250).chr(251)
				 .chr(252).chr(253).chr(255);

			$chars['out'] = 'EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy';

			$string = strtr( $string, $chars['in'], $chars['out'] );
			$double_chars['in'] = array( chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254) );
			$double_chars['out'] = array( 'OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th' );
			$string = str_replace( $double_chars['in'], $double_chars['out'], $string );
		}

		return $string;
	}
	
	/**
	 * Pads a given string with zeroes on the left
	 *
	 * @param   int  $number  The number to pad
	 * @param   int  $length  The total length of the desired string
	 * @return  string
	 *
	 * @access  public
	 * @since   1.0.000
	 * @static
	 */
	public static function zero_pad( $number, $length ) {
		return str_pad( $number, $length, '0', STR_PAD_LEFT );
	}
	
	public static function old_human_time_diff( $from, $to = '', $as_text = FALSE, $suffix = ' ago' ) {
		if ( $to == '' ) {
			$to = time();
		}
		if(is_string($from)) $from = strtotime($from);
		if(is_string($to)) $to = strtotime($to);
		$from = new DateTime( date( 'Y-m-d H:i:s', $from ) );
		$to   = new DateTime( date( 'Y-m-d H:i:s', $to ) );
		$seconds = floor(($to->format('U') - $from->format('U')));
		$minutes = floor(($to->format('U') - $from->format('U')) / (60));
		$hours = floor(($to->format('U') - $from->format('U')) / (60*60));
		$days = floor(($to->format('U') - $from->format('U')) / (60*60*24));
		$months = floor(($to->format('U') - $from->format('U')) / (60*60*24*30));
		$years = floor(($to->format('U') - $from->format('U')) / (60*60*24*30*12));
		
		if ( $years > 1 ) {
			$text = $years. clang::__(' years');
		} else if ( $years == 1 ) {
			$text = '1 year';
		} else if ( $months > 1 ) {
			$text = $months . clang::__(' months');
		} else if ( $months == 1 ) {
			$text = '1'.clang::__(' month');
		} else if ( $days > 7 ) {
			$text = ceil( $days / 7 ) . clang::__(' weeks');
		} else if ( $days == 7 ) {
			$text = '1'.clang::__(' week');
		} else if ( $days > 1 ) {
			$text = $days . clang::__(' days');
		} else if ( $days == 1 ) {
			$text = '1'.clang::__(' day');
		} else if ( $hours > 1 ) {
			$text = $hours . clang::__(' hours');
		} else if ( $hours == 1 ) {
			$text = ' 1'.clang::__(' hour');
		} else if ( $minutes > 1 ) {
			$text = $minutes . clang::__(' minutes');
		} else if ( $minutes == 1 ) {
			$text = '1'.clang::__(' minute');
		} else if ( $seconds > 1 ) {
			$text = $seconds . clang::__(' seconds');
		} else {
			$text = '1'.clang::__(' second');
		}

		if ( $as_text ) {
			$text = explode( ' ', $text, 2 );
			$text = self::number_to_word( $text[0] ) . ' ' . $text[1];
		}

		return trim( $text ) . $suffix;
	}
	public static function human_time_diff( $from, $to = '', $as_text = FALSE, $suffix = ' ago' ) {
		if(!function_exists('date_diff')) {
			return cformatting::old_human_time_diff($from, $to, $as_text, $suffix);
		}
		if ( $to == '' ) {
			$to = time();
		}
		if(is_string($from)) $from = strtotime($from);
		if(is_string($to)) $to = strtotime($to);
		
		
		
		$from = new DateTime( date( 'Y-m-d H:i:s', $from ) );
		$to   = new DateTime( date( 'Y-m-d H:i:s', $to ) );
		
		
		
		
		$diff = $from->diff( $to );

		if ( $diff->y > 1 ) {
			$text = $diff->y . ' years';
		} else if ( $diff->y == 1 ) {
			$text = '1 year';
		} else if ( $diff->m > 1 ) {
			$text = $diff->m . ' months';
		} else if ( $diff->m == 1 ) {
			$text = '1 month';
		} else if ( $diff->d > 7 ) {
			$text = ceil( $diff->d / 7 ) . ' weeks';
		} else if ( $diff->d == 7 ) {
			$text = '1 week';
		} else if ( $diff->d > 1 ) {
			$text = $diff->d . ' days';
		} else if ( $diff->d == 1 ) {
			$text = '1 day';
		} else if ( $diff->h > 1 ) {
			$text = $diff->h . ' hours';
		} else if ( $diff->h == 1 ) {
			$text = ' 1 hour';
		} else if ( $diff->i > 1 ) {
			$text = $diff->i . ' minutes';
		} else if ( $diff->i == 1 ) {
			$text = '1 minute';
		} else if ( $diff->s > 1 ) {
			$text = $diff->s . ' seconds';
		} else {
			$text = '1 second';
		}

		if ( $as_text ) {
			$text = explode( ' ', $text, 2 );
			$text = self::number_to_word( $text[0] ) . ' ' . $text[1];
		}

		return trim( $text ) . $suffix;
	}
	/**
	 * Converts a number into the text equivalent. For example, 456 becomes
	 * four hundred and fifty-six
	 *
	 * @param   int|float  $number  The number to convert into text
	 * @return  string
	 *
	 * @link    http://bloople.net/num2text
	 *
	 * @access  public
	 * @author  Brenton Fletcher
	 * @since   1.0.000
	 * @static
	 */
	public static function number_to_word( $number )
	{
		$number = (string) $number;

		if ( strpos( $number, '.' ) !== FALSE ) {
			list( $number, $decimal ) = explode( '.', $number );
		} else {
			$number = $number;
			$decimal = FALSE;
		}

		$output = '';

		if ( $number[0] == '-' ) {
			$output = 'negative ';
			$number = ltrim( $number, '-' );
		} else if ( $number[0] == '+' ) {
			$output = 'positive ';
			$number = ltrim( $number, '+' );
		}

		if ( $number[0] == '0' ) {
			$output .= 'zero';
		} else {
			$number = str_pad( $number, 36, '0', STR_PAD_LEFT );
			$group  = rtrim( chunk_split( $number, 3, ' ' ), ' ' );
			$groups = explode( ' ', $group );

			$groups2 = array();

			foreach ( $groups as $group ) {
				$groups2[] = self::_number_to_word_three_digits( $group[0], $group[1], $group[2] );
			}

			for ( $z = 0; $z < count( $groups2 ); $z++ ) {
				if ( $groups2[$z] != '' ) {
					$output .= $groups2[$z] . self::_number_to_word_convert_group( 11 - $z );
					$output .= ( $z < 11 && ! array_search( '', array_slice( $groups2, $z + 1, -1 ) ) && $groups2[11] != '' && $groups[11][0] == '0' ? ' and ' : ', ' );
				}
			}

			$output = rtrim( $output, ', ' );
		}

		if ( $decimal > 0 ) {
			$output .= ' point';

			for ( $i = 0; $i < strlen( $decimal ); $i++ ) {
				$output .= ' ' . self::_number_to_word_convert_digit( $decimal[$i] );
			}
		}

		return $output;
	}

	protected static function _number_to_word_convert_group( $index )
	{
		switch( $index ) {
			case 11:
				return ' decillion';
			case 10:
				return ' nonillion';
			case 9:
				return ' octillion';
			case 8:
				return ' septillion';
			case 7:
				return ' sextillion';
			case 6:
				return ' quintrillion';
			case 5:
				return ' quadrillion';
			case 4:
				return ' trillion';
			case 3:
				return ' billion';
			case 2:
				return ' million';
			case 1:
				return ' thousand';
			case 0:
				return '';
		}
	}

	protected static function _number_to_word_three_digits( $digit1, $digit2, $digit3 )
	{
		$output = '';

		if ( $digit1 == '0' && $digit2 == '0' && $digit3 == '0') {
			return '';
		}

		if ( $digit1 != '0' ) {
			$output .= self::_number_to_word_convert_digit( $digit1 ) . ' hundred';

			if ( $digit2 != '0' || $digit3 != '0' ) {
				$output .= ' and ';
			}
		}

		if ( $digit2 != '0') {
			$output .= self::_number_to_word_two_digits( $digit2, $digit3 );
		} else if( $digit3 != '0' ) {
			$output .= self::_number_to_word_convert_digit( $digit3 );
		}

		return $output;
	}

	protected static function _number_to_word_two_digits( $digit1, $digit2 )
	{
		if ( $digit2 == '0' ) {
			switch ( $digit2 ) {
				case '1':
					return 'ten';
				case '2':
					return 'twenty';
				case '3':
					return 'thirty';
				case '4':
					return 'forty';
				case '5':
					return 'fifty';
				case '6':
					return 'sixty';
				case '7':
					return 'seventy';
				case '8':
					return 'eighty';
				case '9':
					return 'ninety';
			}
		} else if ( $digit1 == '1' ) {
			switch ( $digit2 ) {
				case '1':
					return 'eleven';
				case '2':
					return 'twelve';
				case '3':
					return 'thirteen';
				case '4':
					return 'fourteen';
				case '5':
					return 'fifteen';
				case '6':
					return 'sixteen';
				case '7':
					return 'seventeen';
				case '8':
					return 'eighteen';
				case '9':
					return 'nineteen';
			}
		} else {
			$second_digit = self::_number_to_word_convert_digit( $digit2 );

			switch ( $digit1 ) {
				case '2':
					return "twenty-{$second_digit}";
				case '3':
					return "thirty-{$second_digit}";
				case '4':
					return "forty-{$second_digit}";
				case '5':
					return "fifty-{$second_digit}";
				case '6':
					return "sixty-{$second_digit}";
				case '7':
					return "seventy-{$second_digit}";
				case '8':
					return "eighty-{$second_digit}";
				case '9':
					return "ninety-{$second_digit}";
			}
		}
	}

	protected static function _number_to_word_convert_digit( $digit ) {
		switch ( $digit ) {
			case '0':
				return 'zero';
			case '1':
				return 'one';
			case '2':
				return 'two';
			case '3':
				return 'three';
			case '4':
				return 'four';
			case '5':
				return 'five';
			case '6':
				return 'six';
			case '7':
				return 'seven';
			case '8':
				return 'eight';
			case '9':
				return 'nine';
		}
	}
	
	/**
	 * Returns the file permissions as a nice string, like -rw-r--r--
	 *
	 * @param   string  $file  The name of the file to get permissions form
	 * @return  string
	 *
	 * @access  public
	 * @since   1.0.000
	 * @static
	 */
	public static function full_permissions( $file )
	{
		$perms = fileperms( $file );

		if ( ( $perms & 0xC000 ) == 0xC000 ) {
			// Socket
			$info = 's';
		} else if ( ( $perms & 0xA000 ) == 0xA000 ) {
			// Symbolic Link
			$info = 'l';
		} else if ( ( $perms & 0x8000 ) == 0x8000 ) {
			// Regular
			$info = '-';
		} else if ( ( $perms & 0x6000 ) == 0x6000 ) {
			// Block special
			$info = 'b';
		} else if ( ( $perms & 0x4000 ) == 0x4000 ) {
			// Directory
			$info = 'd';
		} else if ( ( $perms & 0x2000 ) == 0x2000 ) {
			// Character special
			$info = 'c';
		} else if ( ( $perms & 0x1000 ) == 0x1000 ) {
			// FIFO pipe
			$info = 'p';
		} else {
			// Unknown
			$info = 'u';
		}

		// Owner
		$info .= ( ( $perms & 0x0100 ) ? 'r' : '-' );
		$info .= ( ( $perms & 0x0080 ) ? 'w' : '-' );
		$info .= ( ( $perms & 0x0040 ) ?
					( ( $perms & 0x0800 ) ? 's' : 'x' ) :
					( ( $perms & 0x0800 ) ? 'S' : '-' ) );

		// Group
		$info .= ( ( $perms & 0x0020 ) ? 'r' : '-' );
		$info .= ( ( $perms & 0x0010 ) ? 'w' : '-' );
		$info .= ( ( $perms & 0x0008 ) ?
					( ( $perms & 0x0400 ) ? 's' : 'x' ) :
					( ( $perms & 0x0400 ) ? 'S' : '-' ) );

		// World
		$info .= ( ( $perms & 0x0004 ) ? 'r' : '-' );
		$info .= ( ( $perms & 0x0002 ) ? 'w' : '-' );
		$info .= ( ( $perms & 0x0001 ) ?
					( ( $perms & 0x0200 ) ? 't' : 'x' ) :
					( ( $perms & 0x0200 ) ? 'T' : '-' ) );

		return $info;
	}
}
?>