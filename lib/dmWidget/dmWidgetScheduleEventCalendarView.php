<?php

class dmWidgetScheduleEventCalendarView extends dmWidgetPluginView
{

	protected function filterViewVars( array $vars = array( ) )
	{
		$serviceContainer = $this->context->getServiceContainer();
		$defaultOptions = $serviceContainer->getParameter('event_calendar.options');
		$newOptions = array();
		if (isset( $vars['show_week_days'] ))
		{
//			$calendar->setOption( 'showWeekDays' , $vars['show_week_days'] );
			$newOptions['showWeekDays'] = $vars['show_week_days'];
		}
		if (isset( $vars['pad_days'] ))
		{
//			$calendar->setOption( 'numberPadDays' , $vars['pad_days'] );
			$newOptions['numberPadDays'] = $vars['pad_days'];
		}
		if (isset( $vars['week_day_format'] ))
		{
//			$calendar->setOption( 'weekNameFormat' , $vars['week_day_format'] );
			$newOptions['weekNameFormat'] = $vars['week_day_format'];
		}
		if (isset( $vars['week_start'] ))
		{
//			$calendar->setOption( 'weekDayStart' , $vars['week_start'] );
			$newOptions['weekDayStart'] = $vars['week_start'];
		}
		$newOptions = array_merge($defaultOptions, $newOptions);
		$serviceContainer->setParameter('event_calendar.options', $newOptions);
		$calendar = $this->getService( 'event_calendar' );
		$serviceContainer->setParameter('event_calendar.options', $defaultOptions);
		$vars['calendar'] = $calendar;
		$calendar->setOption( 'model' , !isset( $vars['model'] )? null :$vars['model'] );
		return $vars;
	}

	protected function doRender()
	{
		if ($this->isCachable() && $cache = $this->getCache())
		{
			return $cache;
		}

		$vars = $this->getViewVars();
		$html = $vars['calendar']->render();
		if ($this->isCachable())
		{
			$this->setCache( $html );
		}

		return $html;
	}

}
