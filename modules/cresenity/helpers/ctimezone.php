<?php
class ctimezone {
	public static function format_offset($offset) {
        $hours = $offset / 3600;
        $remainder = $offset % 3600;
        $sign = $hours > 0 ? '+' : '-';
        $hour = (int) abs($hours);
        $minutes = (int) abs($remainder / 60);

        if ($hour == 0 AND $minutes == 0) {
            $sign = ' ';
        }
        return 'GMT' . $sign . str_pad($hour, 2, '0', STR_PAD_LEFT) 
                .':'. str_pad($minutes,2, '0');

	}
	public static function timezone_list() {
		$list = DateTimeZone::listAbbreviations();
		$idents = DateTimeZone::listIdentifiers();

		$data = $offset = $added = array();
		foreach ($list as $abbr => $info) {
			foreach ($info as $zone) {
				if ( ! empty($zone['timezone_id'])
					AND
					! in_array($zone['timezone_id'], $added)
					AND 
					  in_array($zone['timezone_id'], $idents)) {
					$z = new DateTimeZone($zone['timezone_id']);
					$c = new DateTime(null, $z);
					$zone['time'] = $c->format('H:i a');
					$data[] = $zone;
					$offset[] = $z->getOffset($c);
					$added[] = $zone['timezone_id'];
				}
			}
		}

		array_multisort($offset, SORT_ASC, $data);
		$options = array();
		foreach ($data as $key => $row) {
			$options[$row['timezone_id']] = $row['time'] . ' - '
											. ctimezone::format_offset($row['offset']) 
											. ' ' . $row['timezone_id'];
		}
		return $options;
	}
	
	public static function get_timezone_offset($remote_tz, $origin_tz = null) {
		if($origin_tz === null) {
			if(!is_string($origin_tz = date_default_timezone_get())) {
				return false; // A UTC timestamp was returned -- bail out!
			}
		}
		$origin_dtz = new DateTimeZone($origin_tz);
		$remote_dtz = new DateTimeZone($remote_tz);
		$origin_dt = new DateTime("now", $origin_dtz);
		$remote_dt = new DateTime("now", $remote_dtz);
		$offset = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);
		return $offset;
	}
	
	
	
	
}
