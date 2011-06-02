<?php

interface dmHtmlCalendarEventInterface {

	public function renderEvent($day, $isFirstEvent=true);
    /**
	 * $param dmHtmlCalendarDay $day The day it's called for
     * @return array Classes that will be appended to day markup element
     */
	public function getAdditionalDayClasses($day);
    /**
     * @return array Attributes that will be appended to day markup element
     */
	public function getAdditionalDayAttributes();
    /**
     * @return string Date of the event
     */
	public function getCalendarDate();
    /**
     * @return string Name of the column that will be used to query the events in this event
     */
	public static function getCalendarDateColumnName();
    /**
     * @return Doctrine_Query Query to find the events of the current month
     */
	public static function getCalendarQuery($startDate, $endDate);
}

?>
