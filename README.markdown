dmWidgetEventCalendarPlugin displays the days in from the current month in a list.
You can attach events to each day and they will be displayed on the calendar.
Events can be populated into the calendar from a model's records if the model implements
the provided interface.

## Installation

### Download it

Run in project's directory:

	git clone git://github.com/antitoxic/dmWidgetEventCalendarPlugin.git plugins/dmWidgetEventCalendarPlugin

### Enable it

Edit `ProjectConfiguration`

	class ProjectConfiguration extends dmProjectConfiguration
	{
		public function setup()
		{
			parent::setup();

			$this->enablePlugins(array(
				// your enabled plugins
				 'dmWidgetEventCalendarPlugin'
			));

### Just clear the cache

Run in project's directory:

	php symfony cc

### Style the calendar

Include the plugin assets in your `apps/front/config/view.yml` file:

    stylesheets:
      - dmWidgetEventCalendarPlugin.view

Don't forget to publish the plugins' assets:

    php symfony dm:publish-assets

## What is provided?

It includes:

 * **Event Calendar** widget under the **Schedule** widget category.
 * `event_calendar` service. Universally reach it via `dmContext::getInstance()->getServiceContainer()->getService('event_calendar')`
 * Offers `dmHtmlCalendarEventInterface` that your model should implement in
order to be valid Event for the calendar.
Only the following methods should be defined:

	* **renderEvent** `($day, $isFirstEvent=true)` Used to output the event
	* **getAdditionalDayClasses** You can simply define empty method. Returns
 the classes that will be appended to day markup element
	* **getAdditionalDayAttributes** You can simply define empty method.
Returns the attributes that will be appended to day markup element
	* **getCalendarDate** Returns the date of the event
	* **getCalendarDateColumnName** STATIC If the CalendarQuery method
return Doctrine_Query object you can leave this an empty method. be Return the name of the column in the database that represent the date
    * **getCalendarQuery** `($startDate, $endDate)` STATIC You can simply
 define empty method. Otherwise it should be a `Doctrine_Query` instance.

## Widget options

 * **showWeekDays** Whether to display the names of the week days. Defaults to true.
 * **numberPadDays** Whether to display days from the following/next month
 that complete the last/first week
 * **weekNameFormat** Format of the week day names. Either `'EE'`(Single letter)
| `'EEE'`(Abbreviated) | `'EEE'`(Full name)
 * **weekDayStart** Which is the first day of the week. Defaults to 'sun'
 - Sunday. One of 'sun','mon','tue','wed','thu','fri','sat' OR 0,1,2,3,4,5,6
 * **model** Model that implements `dmHtmlCalendarEventInterface` interface.
 The model's table will be queried to retrieve events from the current month.
 * **culture** Calendar culture

## Example Model class implementation

Here is the minimum if you want Events to be populated into the calendar from a model's records:

	class Appointment extends BaseAppointment implements dmHtmlCalendarEventInterface
	{

		public function getAdditionalDayAttributes()
		{
		}

		public function getAdditionalDayClasses()
		{
		}

		public function renderEvent( $day , $isFirstEvent = true )
		{
			return $day.' '.$this->getClient();
		}

		public function getCalendarDate()
		{
			return $this->getScheduledOn();
		}

		public static function getCalendarDateColumnName()
		{
			return 'scheduled_on';
		}
		public static function getCalendarQuery($startDate, $endDate) {

		}

	}