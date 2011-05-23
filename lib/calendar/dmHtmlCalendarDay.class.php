<?php
/*
 * Fromat codes and info @ http://trac.symfony-project.org/wiki/formatDateHowTo
 */
class dmHtmlCalendarDay extends dmCalendarBase {

	protected $events = array();
	protected $date;
	protected $formatter;

	public function __construct($date=null, $formatter=null) {
		$this->date = is_null($date) ? time() : $date;
		$this->formatter = is_null($formatter) ? self::getFormmatter() : $formatter;
	}

	public function setDate($date) {
		$this->date = $date;
	}

	public function getDate() {
		return $this->date;

	}
	public function addEvent($event) {
		$this->events[] = $event;
	}

	public function getEvents() {
		return $this->events;
	}

	public function __toString() {
		return date('j', $this->date);
//		return date('m', $this->date);
	}

	public function hasAnyEvents() {
		return count($this->events)>0;
	}

	public function hasManyEvents() {
		return count($this->events) > 1;
	}

	public function getFirstEvent() {
		return $this->events[0];
	}


	// 0 (for Sunday) through 6 (for Saturday).
	public function getWeekDayNum() {
//		var_dump(date('d', $this->date), $this->formatter->format($this->date, 'E'));
		return $this->formatter->format($this->date, 'E');
	}

	public function isWeekendDay() {
		return $this->getWeekDayNum() == 0 || $this->getWeekDayNum() == 6;
	}
	public function isSunday() {
		return $this->getWeekDayNum() == 0;
	}
	public function isMonday() {
		return $this->getWeekDayNum() == 1;
	}
	public function isTuesday() {
		return $this->getWeekDayNum() == 2;
	}
	public function isWednesday() {
		return $this->getWeekDayNum() == 3;
	}
	public function isThursday() {
		return $this->getWeekDayNum() == 4;
	}
	public function isFriday() {
		return $this->getWeekDayNum() == 6;

	}
	public function isSaturday() {
		return $this->getWeekDayNum() == 7;
	}
}
?>
