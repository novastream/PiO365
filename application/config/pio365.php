<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Default locale
|--------------------------------------------------------------------------
| Example of locales: en (english), sv (swedish)
|
*/
$config['locale'] = 'en';

/*
|--------------------------------------------------------------------------
| Display mode
|--------------------------------------------------------------------------
| Availible options are full and boxed
| Full will take 100% width and boxed will take 12 columns width
*/
$config['layout'] = 'boxed';

/*
|--------------------------------------------------------------------------
| Theme
|--------------------------------------------------------------------------
| Availible options are standard, bootstrap4
| 
*/
$config['theme'] = 'bootstrap4';

/*
|--------------------------------------------------------------------------
| Default view for fullcalendar
|--------------------------------------------------------------------------
| Availible options are month, agendaWeek, agendaDay
*/
$config['default_view'] = 'month';

/*
|--------------------------------------------------------------------------
| Display week number
|--------------------------------------------------------------------------
| Availible options true/false
*/
$config['week_number'] = true;

/*
|--------------------------------------------------------------------------
| Time format
|--------------------------------------------------------------------------
| Use moment documentation for valid time format
*/
$config['time_format'] = 'H:mm';

/*
|--------------------------------------------------------------------------
| Timezone
|--------------------------------------------------------------------------
| Choose a appropriate timezone
*/
$config['calendar_timezone'] = '';

/*
|--------------------------------------------------------------------------
| Default calendar
|--------------------------------------------------------------------------
| Sets which calendar we should use to fetch events
*/
$config['default_calendar'] = '';

/*
|--------------------------------------------------------------------------
| OAUTH
|--------------------------------------------------------------------------
| Enter a valid APP ID, Password and Redirect URL
*/
$config['OAUTH_APP_ID'] = '';
$config['OAUTH_APP_PASSWORD'] = '';
$config['OAUTH_REDIRECT_URI'] = '';
$config['OAUTH_SCOPES'] = 'openid profile offline_access User.Read Calendars.Read Calendars.Read.Shared';
$config['OAUTH_AUTHORITY'] = 'https://login.microsoftonline.com/common';
$config['OAUTH_AUTHORIZE_ENDPOINT'] = '/oauth2/v2.0/authorize';
$config['OAUTH_TOKEN_ENDPOINT'] = '/oauth2/v2.0/token';

/*
|--------------------------------------------------------------------------
| API
|--------------------------------------------------------------------------
| API URL and Endpoints
*/
$config['API_URL'] = 'https://graph.microsoft.com/v1.0';
$config['API_CALENDARS_ENDPOINT'] = '/me/calendars';
$config['API_EVENTS_ENDPOINT'] = '/me/calendars/{id}';
$config['API_EVENTS_FILTER'] = '/calendarview?startDateTime={start_date}&endDateTime={end_date}&$top=500';

/*
|--------------------------------------------------------------------------
| Password
|--------------------------------------------------------------------------
| Password protect the configuration page
| This app should only be visible within your network
*/
$config['app_password'] = '';