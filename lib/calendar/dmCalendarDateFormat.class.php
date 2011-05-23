<?php

class dmCalendarDateFormat extends sfDateFormat {

//	public function getWday($date, $pattern = 'EE') {
//		// if the $date comes from our home-made get date
//		if (!isset($date['wday'])) {
//			$date = $this->getUnixDate($date);
//		}
//		$day = $date['wday'];
//
//		switch ($pattern) {
//			case 'E':
//				return $day;
//				break;
//			case 'EE':
//				return $this->formatInfo->NarrowDayNames[$day];
//			case 'EEE':
//				return $this->formatInfo->AbbreviatedDayNames[$day];
//				break;
//			case 'EEEE':
//				return $this->formatInfo->DayNames[$day];
//				break;
//			default:
//				throw new sfException('The pattern for day of the week is "E", "EE", "EEE", or "EEEE".');
//		}
//	}

	public function getFormatInfo() {
		return $this->formatInfo;
	}
	
	public function format($time, $pattern = 'F', $inputPattern = null, $charset = 'UTF-8') {
		$charset = sfConfig::get('sf_charset');
		return parent::format($time, $pattern, $inputPattern, $charset);
	}

}

?>
