<?php

class Tools {
	public static function calculateETA($cur_lat, $cur_long, $tar_lat, $tar_long, $speed) {
		$distance = self::calculateDTG($cur_lat, $cur_long, $tar_lat, $tar_long);

		return $distance / $speed * 60 * 60;
	}

	public static function calculateDTG($cur_lat, $cur_long, $tar_lat, $tar_long) {
		// Thanks to http://www.movable-type.co.uk/scripts/latlong.html

		$R = 6371; // km
		$fi1 = deg2rad($cur_lat);
		$fi2 = deg2rad($tar_lat);
		$deltaFi = deg2rad($tar_lat - $cur_lat);
		$deltaLambda = deg2rad($tar_long - $cur_long);

		$a = sin($deltaFi / 2) * sin($deltaFi / 2) +
			cos($fi1) * cos($fi2) *
			sin($deltaLambda / 2) * sin($deltaLambda / 2);
		$c = 2 * atan2(sqrt($a), sqrt(1-$a));

		return $R * $c * 0.5399568;
	}
}