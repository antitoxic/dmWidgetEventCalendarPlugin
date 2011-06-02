<?php

echo _tag( 'h2' , __( 'Events' ) );
echo _open( 'ol.dm_calendar.clearfix' );
if ($calendar->getOption( 'showWeekDays' ))
{

	foreach ($calendar->getWeekDayNames() as $weekDayName)
	{
		echo _open( 'li.week-day' );
		echo $weekDayName;
		echo _close( 'li.week-day' );
	}
}
// preceding days
foreach ($calendar->getPreceedingDays() as $day)
{
	echo _open( 'li.day.prev' );

	if ($calendar->getOption( 'numberPadDays' ))
	{
		echo $day;
	}
	echo _close( 'li.day.prev' );
}

// month days
foreach ($calendar->getDays() as $day)
{
	$dayContent = '';
	$dayTagOptions = '.day';
	$dayTagOptions .= $calendar->isCurrentDay( $day ) ? '.current' : '';
	$dayTagOptions .= $day->hasAnyEvents() ? '.events' : '';
	$dayTagOptions .= $day->hasManyEvents() ? '.multi' : '';
	$isFirstEvent = true;

	if (!$day->hasAnyEvents())
	{
		$dayContent .= $day;
	}
	foreach ($day->getEvents() as $event)
	{
		if ($event instanceof dmHtmlCalendarEventInterface)
		{
			$dayTagOptions .= $calendar->parseClasses( $event->getAdditionalDayClasses($day) );
			$dayContent .= $event->renderEvent( $day , $isFirstEvent );
		} else
		{
			$dayContent .= $day . $event;
		}
		if ($isFirstEvent)
		{
			$isFirstEvent = false;
		}
	}
	echo _open( 'li' . $dayTagOptions );
	echo $dayContent;
	echo _close( 'li.day' );
}

// following days
foreach ($calendar->getFollowingDays() as $day)
{
	echo _open( 'li.day.next' );
	if ($calendar->getOption( 'numberPadDays' ))
	{
		echo $day;
	}
	echo _close( 'li.day.next' );
}

echo _close( 'ol.dm_calendar' );