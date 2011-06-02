<?php

/*
 * Fromat codes and info @ http://trac.symfony-project.org/wiki/formatDateHowTo
 */
class dmHtmlCalendar extends dmCalendarBase
{

	protected $options = array(
		'showWeekDays' => true ,
		'numberPadDays' => true , // to show or not days from the following/next calendar
		'weekNameFormat' => 'EE' ,
		'weekDayStart' => 'sun' , //sunday
		'culture' => 'en'
	);
//	protected $weekDayMaping = array(
//		'sun' => 0 ,
//		'mon' => 1 ,
//		'tue' => 2 ,
//		'wed' => 3 ,
//		'thu' => 4 ,
//		'fri' => 5 ,
//		'sat' => 6
//	);
	protected $weekDayMaping = array(
		'sun',
		'mon',
		'tue',
		'wed',
		'thu',
		'fri',
		'sat'
	);
	protected $formatter;
	protected $culture;
	protected $date;
	protected $days;
	protected $preceedingDays = array( );
	protected $followingDays = array( );

	/**
	 *
	 * @param array Various options 'showWeekDays','numberPadDays', 'weekNameFormat' ,'weekDayStart', 'culture'
	 * @param int Timestamp of a day from the month you want to display a calendar
	 */
	public function __construct( $options = array( ) , $date = null )
	{
		if (is_null( $date ))
		{
			$this->date = time();
		}
		$this->setOption( 'culture' , sfContext::getInstance()->getUser()->getCulture() );
		$this->extendOptions( $options );
		$this->setWeekDayStart($this->getOption('weekDayStart'));
		$this->formatter = self::getFormmatter( $this->getOption( 'culture' ) );
		$this->loadDays();
	}

	/**
	 * Generate days for the preceeding, current and following arrays
	 */
	public function loadDays()
	{
		$preceedingDays = $this->getNbPreceedingFillDays();
		if ($preceedingDays > 0)
		{
			$prevMonth = $this->getPreviousMonth();
			$daysInPreviousMonth = $this->getNbDays( $prevMonth );
			$startDay = $daysInPreviousMonth - $preceedingDays + 1;
			for ($i = $startDay; $i <= $daysInPreviousMonth; $i++)
			{
				$date = $this->changeDay( $prevMonth , $i );
				$dayIndex = $this->getDateAssocIndex( $date );
				if (isset( $this->preceedingDays[$dayIndex] ))
				{
					continue;
				}
				$this->preceedingDays[$this->getDateAssocIndex( $date )] = $this->createCalendarDay( $date );
			}
		}
		$followingDays = $this->getNbFollowingFillDays();
		if ($followingDays > 0)
		{
			$nextMonth = $this->getNextMonth();
			for ($i = 1; $i <= $followingDays; $i++)
			{
				$date = $this->changeDay( $nextMonth , $i );
				$dayIndex = $this->getDateAssocIndex( $date );
				if (isset( $this->followingDays[$dayIndex] ))
				{
					continue;
				}
				$this->followingDays[$this->getDateAssocIndex( $date )] = $this->createCalendarDay( $date );
			}
		}
		for ($i = 1; $i <= $this->getNbDays(); $i++)
		{
			$date = $this->getMonthDate( $i );
			$dayIndex = $this->getDateAssocIndex( $date );
			if (isset( $this->days[$dayIndex] ))
			{
				continue;
			}
			$this->days[$this->getDateAssocIndex( $date )] = $this->createCalendarDay( $date );
		}
	}

	/**
	 *
	 * @param int Timestamp
	 * @return string Formatted date like 2006-11-25
	 */
	public function getDateAssocIndex( $date )
	{
		return date( 'Y-m-d' , $date );
	}

	/**
	 *
	 * @return int Integer from 0 (sunday) to 6(saturday)
	 */
	public function getWeekDayStartNum()
	{
		return $this->getOption( 'weekDayStart' );
	}

	/**
	 *
	 * @param type dmHtmlCalendarDay Day to compare
	 * @return boolean
	 */
	public function isCurrentDay( $calendarDay )
	{
		return date( 'Y-m-d' , $calendarDay->getDate() ) == date( 'Y-m-d' , $this->date );
	}

	/**
	 *
	 * @return array  Collection of dmHtmlCalendarDay
	 */
	public function getPreceedingDays()
	{
		$num = $this->getNbPreceedingFillDays();
		return array_slice( $this->preceedingDays , -$num , $num );
	}

	/**
	 *
	 * @return array  Collection of dmHtmlCalendarDay
	 */
	public function getFollowingDays()
	{
		return array_slice( $this->followingDays , 0 , $this->getNbFollowingFillDays() );
	}


	/**
	 *
	 * @return array Collection of dmHtmlCalendarDay from the current month
	 */
	public function getDays()
	{
		return $this->days;
	}

	/**
	 *
	 * @param int $date Timestamp
	 * @return dmHtmlCalendarDay
	 */
	public function createCalendarDay( $date=null )
	{
		$date = is_null( $date ) ? $this->date : $date;
		return new dmHtmlCalendarDay( $date , $this->formatter );
	}

	/**
	 *
	 * @return int Timestamp of a day from the previous month
	 */
	public function getPreviousMonth()
	{
		return strtotime( date( 'Y-m-d' , $this->getFirstDateOfMonth() ) . " -1 day" );
	}

	/**
	 *
	 * @return int Timestamp of a day from the next month
	 */
	public function getNextMonth()
	{
		return strtotime( date( 'Y-m-d' , $this->getLastDateOfMonth() ) . " +1 day" );
	}

	/**
	 * Less strict alias of changeDay()
	 * @param int The number of the day that you want timestamp of
	 * @param int Timestamp in which only the year and the month are relevant
	 * @return int Timestamp of the day from the provided month $date
	 */
	public function getMonthDate( $day , $date = null )
	{
		$date = is_null( $date ) ? $this->date : $date;
//		2010-02-02
		return $this->changeDay( $date , $day );
	}

	/**
	 *
	 * @param int $monthDate Date in which only year and month are relevant
	 * @param int $day The day that should be changed in the date
	 * @return int timestamp
	 */
	public function changeDay( $monthDate , $day )
	{
//		2010-02-02
		return strtotime( date( 'Y' , $monthDate ) . '-' . date( 'm' , $monthDate ) . '-' . sprintf( "%02d" , $day ) );
	}

	/**
	 *
	 * @param int Timestamp of a date, which only the year and month are relevant. Defaults to the current calendar date.
	 * @return int The number of the days of the provided date.
	 */
	public function getNbDays( $date=null )
	{
		$date = is_null( $date ) ? $this->date : $date;
		return (int) date( 't' , $date );
	}

	/**
	 *
	 * @return int Timestamp of the first day of month
	 */
	public function getFirstDateOfMonth()
	{
		return $this->getMonthDate( 1 );
	}

	/**
	 * @return int timestamp of the last day of the month (28th | 29th | 30th | 31st)
	 */
	public function getLastDateOfMonth()
	{
		return $this->getMonthDate( $this->getNbDays() );
	}

	/**
	 * @return int Number of the days from the previous month that are required to complete the first displayed week
	 */
	public function getNbPreceedingFillDays()
	{
		$diff = $this->createCalendarDay( $this->getFirstDateOfMonth() )->getWeekDayNum() - $this->getWeekDayStartNum();
		if ($diff === 0)
		{
			return $diff;
		}
		return $diff < 0 ? 7 + $diff : $diff;
	}

	/**
	 * @return int Number of the days from the next month that are required to complete the last displayed week
	 */
	public function getNbFollowingFillDays()
	{
		$diff = $this->createCalendarDay( $this->getLastDateOfMonth() )->getWeekDayNum() - $this->getWeekDayStartNum();
		if ($diff === 0)
		{
			return 6;
		}
		return $diff > 0 ? 7 - $diff - 1 : abs( $diff ) - 1;
	}

	/**
	 * @return int timestampt of the first displayed date
	 */
	public function getStartDate()
	{
		$preceedingDays = $this->getNbPreceedingFillDays();
		if ($preceedingDays === 0)
		{
			return $this->getFirstDateOfMonth();
		}
		$prevMonth = $this->getPreviousMonth();
		$daysInPreviousMonth = $this->getNbDays( $prevMonth );
		$startDay = $daysInPreviousMonth - $preceedingDays;
		return $this->changeDay( $prevMonth , $startDay );
	}

	/**
	 * @return int timestampt of the last displayed date
	 */
	public function getEndDate()
	{
		$NbFollowingDays = $this->getNbFollowingFillDays();
		if ($NbFollowingDays === 0)
		{
			return $this->getLastDateOfMonth();
		}
		$nextMonth = $this->getNextMonth();
		return $this->changeDay( $nextMonth , $NbFollowingDays );
	}

	/**
	 *
	 * @param array $userOptions Array of new options
	 * @return dmHtmlCalendar
	 */
	public function extendOptions( $userOptions )
	{
		$this->options = array_merge( $this->options , $userOptions );
		return $this;
	}

	/**
	 *
	 * @return array Week-day names formatted as stated in "weekNameFormat" option
	 */
	public function getWeekDayNames()
	{

		$formatterInfo = $this->formatter->getFormatInfo();
		switch ($this->getOption( 'weekNameFormat' ))
		{

//			"S", "M", "T", "W", "T", "F", "S"
			case 'EE':
				$weekDayNames = $formatterInfo->getNarrowDayNames();
				break;
//			"Sun", "Mon", "Tue", "Wed", "Thu", "Fri", and "Sat".
			case 'EEE':
				$weekDayNames = $formatterInfo->getAbbreviatedDayNames();
				break;
//			"Tuesday", "Wednesday", "Thursday", "Friday", and "Saturday".
			case 'EEEE':
				$weekDayNames = $formatterInfo->getDayNames();
				break;
			default:
				throw new sfException( 'The pattern for day of the week is "E", "EE", "EEE", or "EEEE".' );
		}
		array_rotate( $weekDayNames , $this->getWeekDayStartNum() );
		return $weekDayNames;
	}

	/**
	 *
	 * @param string $optionKey The option name
	 * @param mixed $default Default value if there isn't such option set
	 * @return boolean
	 */
	public function getOption( $optionKey , $default = null )
	{
		return isset( $this->options[$optionKey] ) ? $this->options[$optionKey] : $default;
	}

	/**
	 *
	 * @param string $optionKey The option name
	 * @param mixed $value The option value
	 * @return dmHtmlCalendar
	 */
	public function setOption( $optionKey , $value )
	{
		$this->options[$optionKey] = $value;
		if ($optionKey == 'weekDayStart')
		{
			$this->setWeekDayStart($value);
			$this->loadDays();
		}
		return $this;
	}

	/**
	 *
	 * @param mixed $value Either 0-6 or sun-sat
	 * @return dmHtmlCalendar
	 */
	protected function setWeekDayStart( $value )
	{
		if (!(in_array( $value , $this->weekDayMaping ) || in_array( $value , range(0, 6) )))
		{
			throw new sfException( 'dmCalendar: The option "weekDayStart" can only be one of the following: ' . implode( ', ' , $this->weekDayMaping ).' or '. implode( ', ' , range(0,6) ) );
		}
		$this->options['weekDayStart'] = in_array( $value , range(0, 6) ) ? $value : array_search($value , $this->weekDayMaping);
		return $this;
	}

	/**
	 *
	 * @param int $date Timestamp
	 * @return type
	 */
	public function getDay( $date )
	{
		$key = $this->getDateAssocIndex( $date );
		if (!key_exists( $key , $this->days ))
		{
			if (key_exists( $key , $this->followingDays ))
			{
				return $this->followingDays[$key];
			}
			if (key_exists( $key , $this->preceedingDays ))
			{
				return $this->preceedingDays[$key];
			}
		}
		return $this->days[$key];
	}

	public function addEvent( $date , $event )
	{
		$day = $this->getDay( $date );
		$day->addEvent( $event );
	}

	public function parseClasses( $classes )
	{
		if (is_string( $classes ))
		{
			$classes = trim( $classes , ' .' );
			if (empty( $classes ))
			{
				return '';
			}
			$classes = explode( ' ' , $classes );
		}
		if (!is_array( $classes ))
		{
			return '';
		}
		$classes = array_unique($classes);
		return '.' . implode( '.' , $classes );
	}

	public function getCurrentDate() {
		return $this->date;
	}

}

function array_rotate( &$arr , $times=1 )
{
	if ($times <= 0)
	{
		return;
	}
	$elm = array_shift( $arr );
	array_push( $arr , $elm );
	return $times == 1 ? $elm : array_rotate( $arr , $times - 1 );
}

?>
