<?php

abstract class dmCalendarBase {
	public static function getFormmatter($culture=null) {
		$culture = is_null($culture) ? sfContext::getInstance()->getUser()->getCulture() : $culture;
		return new dmCalendarDateFormat($culture);
	}
}
?>
