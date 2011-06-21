<?php

class dmEventCalendar extends dmHtmlCalendar
{

	protected static
	$eventModels;

	/**
	 *
	 * @param array $options Options for the calendar
	 * @param int $date  Timestamp of a date from the Week/Month/Year you want to display
	 */
	public function __construct($options = array( ), $date = null )
	{
		parent::__construct( $options, $date );
	}

	public static function getEventModels()
	{

		if (!is_null( self::$eventModels ))
		{
			return self::$eventModels;
		}
		// get Model files including their subclasses
		$libDir = dmOs::normalize( sfConfig::get( 'sf_lib_dir' ) );
		$modelFiles = glob( $libDir . '/model/doctrine/*.class.php' , GLOB_BRACE );
		// get files to be filtered out
		$modelTableFiles = glob( $libDir . '/model/doctrine/*{Table,DoctrineRecord}.class.php' , GLOB_BRACE );
		$modelFiles = array_diff( $modelFiles , $modelTableFiles );
		$modelFiles;
		$models = array( );
		// extract classnames
		foreach ($modelFiles as $file)
		{
			$models[] = preg_replace( '|^(\w+).class.php$|' , '$1' , basename( $file ) );
		}
		// filter classnames that implement the Event interface
		$models = array_filter( $models , array( 'dmEventCalendar' , 'isClassEvent' ) );
		self::$eventModels = $models;
		return $models;
	}

	public static function isClassEvent( $class )
	{
		$reflection = new ReflectionClass( $class );
		return $reflection->implementsInterface( 'dmHtmlCalendarEventInterface' );
	}

	protected function attachModelEvents(Doctrine_Collection $events)
	{
		foreach ($events as $event)
		{
			$this->addEvent( strtotime( $event->getCalendarDate() ) , $event );
		}
	}

	protected function retrieveModelEvents()
	{
		$model = $this->getOption( 'model');
		if (is_null($model)) {
			return null;
		}
		if (!self::isClassEvent($model)) {
			throw new sfException( 'The provided model ('.$model.') for Diem Calendar should implement dmHtmlCalendarEventInterface' );
		}
		//get this month's events

		$query = call_user_func(array($model, 'getCalendarQuery'), $this->getFirstDateOfMonth(), $this->getLastDateOfMonth());
		if (is_null( $query) || !$query instanceof Doctrine_Query) {
			$date_column = call_user_func(array($model, 'getCalendarDateColumnName'));
			$query = dmDb::table( $model )
				->createQuery( 'e' )
				->andWhere( "DATEDIFF('" . date( 'Y-m-d' , $this->getFirstDateOfMonth() ) . "', e." . $date_column . ") < 0" )
				->andWhere( "DATEDIFF('" . date( 'Y-m-d' , $this->getLastDateOfMonth() ) . "', e." . $date_column . ") > 0" );
		}
		return $query->execute();
	}

	public function render()
	{
		$html = '';

		$modelEvents = $this->retrieveModelEvents();
		if (!is_null($modelEvents)) {
			$this->attachModelEvents($modelEvents);
		}
		$html = dmContext::getInstance()->getHelper()->renderPartial( 'dmWidget' , 'dmWidgetScheduleEventCalendar' , array(
			'calendar' => $this
		) );
		return $html;
	}

}