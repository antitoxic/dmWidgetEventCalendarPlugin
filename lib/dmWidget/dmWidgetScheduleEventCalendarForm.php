<?php

class dmWidgetScheduleEventCalendarForm extends dmWidgetPluginForm
{

	public function configure()
	{
		$choices = dmEventCalendar::getEventModels();
		$choices = array_combine( $choices , $choices );
		$choices = array_reverse($choices, true);
		$choices[''] = '';
		$choices = array_reverse($choices, true);
		$this->setWidget( 'model' , new sfWidgetFormChoice( array(
				'choices' => $choices
				) ) );
		$this->setValidator( 'model' , new sfValidatorChoice( array(
				'choices' => $choices ,
				'required' => false
				) ) );

		$weekDayNames = dmCalendarBase::getFormmatter()->getFormatInfo()->getDayNames();
		$this->setWidget( 'week_start' , new sfWidgetFormChoice( array(
				'choices' => $weekDayNames
				) ) );
		$this->setValidator( 'week_start' , new sfValidatorChoice( array(
				'choices' => array_keys( $weekDayNames )
				) ) );

		$this->setWidget( 'show_week_days' , new sfWidgetFormInputCheckbox() );
		$this->setValidator( 'show_week_days' , new sfValidatorBoolean() );
		$this->setWidget( 'pad_days' , new sfWidgetFormInputCheckbox() );
		$this->setValidator( 'pad_days' , new sfValidatorBoolean() );
		$weekDayFormats = array(
			'EE' => 'Single letter (S)' ,
			'EEE' => 'Abbreviation (Sun)' ,
			'EEEE' => 'Full name (Sunday)'
		);
		$this->setWidget( 'week_day_format' , new sfWidgetFormChoice( array(
				'choices' => $weekDayFormats
				) ) );
		$this->setValidator( 'week_day_format' , new sfValidatorChoice( array(
				'choices' => array_keys( $weekDayFormats )
				) ) );

		// Help messages
		$this->widgetSchema->setHelps(
			array(
				'pad_days' => 'Whether to show days from previous/next months'
		) );

		// Labels, Display names
		$this->widgetSchema->setLabels(
			array(
				'week_start' => 'Start of the week' ,
				'pad_days' => 'Pad days'
		) );

		// Labels, Display names
		if ($this->getDmWidget()->isNew()) {
			$this->setDefault('show_week_days' ,true);
			$this->setDefault('pad_days' ,true);
		}
		parent::configure();
	}

	public function getStylesheets()
	{
		return array(
//			'lib.ui-tabs' ,
			'dmWidgetEventCalendarPlugin.form'
		);
	}

	public function getJavascripts()
	{
		return array(
//			'lib.ui-tabs' ,
//			'core.tabForm' ,
			'dmWidgetEventCalendarPlugin.form'
		);
	}

}
