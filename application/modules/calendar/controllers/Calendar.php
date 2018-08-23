<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Calendar extends CI_Controller {
	
	/**
	 * Constructor
	*/
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Default
	*/
	public function index()
	{
		// show login
		$showHelp = '';

		// settings
		$settings = $this->office365->getSettings();

		// check if OAUTH settings is set
		if (empty($settings->OAUTH_APP_ID) || empty($settings->OAUTH_APP_PASSWORD) || empty($settings->OAUTH_REDIRECT_URI))
		{
			$showHelp = 'config';
		}
		else 
		{
			if ($this->office365->getAccessToken() == false)
			{
				$showHelp = 'signin';
			}
		}
		
		// assets
		$this->assets->init(array('pipeline' => false));
		$this->assets->add(array(
			base_url('assets/bootstrap/dist/css/bootstrap.min.css'),
			base_url('assets/fullcalendar/dist/fullcalendar.min.css'),
			base_url('assets/font-awesome/web-fonts-with-css/css/fontawesome-all.min.css'),
			base_url('assets/app.css')
		));
		$this->assets->add(array(
			base_url('assets/jquery/dist/jquery.min.js'),
			base_url('assets/popper.js/dist/umd/popper.min.js'),
			base_url('assets/bootstrap/dist/js/bootstrap.min.js'),
			base_url('assets/moment/min/moment-with-locales.min.js'),
			base_url('assets/fullcalendar/dist/fullcalendar.min.js')
		));

		//get response
		$response = $this->session->flashdata('response');

		// view data
		$view_data = array(
			'appName' => 'PiO365',
			'pageTitle' => 'Calendar',
			'settings' => $settings,
			'response' => $response,
			'showHelp' => $showHelp,
			'site_url' => site_url(),
			'base_url' => base_url(),
			'css' => $this->assets->css(),
			'js' => $this->assets->js()
		);

		// load up blade engine
		$this->theme->display('calendar.show', $view_data);
	}

	/**
	 * Get calendar events
	 * @AJAX ONLY
	 */
	public function get()
	{
		if (!$this->input->is_ajax_request())
        {
            $this->output->set_status_header('400');
            $this->output->set_content_type('text/plain');
            $this->output->set_output('Bad request');
        }
        else 
        {
			// start and end
			$start_date = $this->input->get('start', true);
			$end_date = $this->input->get('end', true);

			// settings
			$settings = $this->office365->getSettings();

			// calendar object
			$events = (object)array('value' => '');

			// check for valid token
			if ($this->office365->getAccessToken() != false)
			{
				try 
				{
					$client = new GuzzleHttp\Client();

					$api_events_endpoint = str_replace('{id}', $settings->default_calendar, $settings->API_EVENTS_ENDPOINT);
					$api_events_filter = str_replace('{start_date}', $start_date, $settings->API_EVENTS_FILTER);
					$api_events_filter = str_replace('{end_date}', $end_date, $api_events_filter);

					$res = $client->request('GET', $settings->API_URL.$api_events_endpoint.$api_events_filter, [
						'headers' => [
							'Accept' 			=> 'application/json;odata.metadata=minimal;odata.streaming=true',
							'Content-type' 		=> 'application/json',
							'Authorization'     => 'Bearer ' . $this->office365->getAccessToken(),
							'Prefer'	=> 'outlook.timezone="'.$settings->calendar_timezone.'"'
						]
					]);

					$event_src = json_decode($res->getBody()->getContents());

					$events = array();

					if (isset($event_src->value) && is_array($event_src->value))
					{
						foreach ($event_src->value as $event)
						{
							$title = '';

							if (!empty($event->location->displayName))
							{
								$title = $event->subject.', '.$event->location->displayName;
							}
							else 
							{
								$title = $event->subject;
							}

							$events[] = array(
								'id' => $event->id,
								'timezone' => $event->start->timeZone,
								'start' => $event->start->dateTime,
								'end' => $event->end->dateTime,
								'title' => $title
							);
						}
					}

					$response = array(
						'status' => 'success',
						'message' => '',
						'data' => $events					
					);
				}
				catch(Exception $e)
				{
					$response = array(
						'status' => 'error',
						'message' => $e->getMessage(),
						'data' => ''
					);
				}
			}
			else 
			{
				$response = array(
					'status' => 'error',
					'message' => 'Not authenticated',
					'data' => ''
				);
			}

			/* Use CI3 output class to display the results */
			$_output = json_encode($response);
			$this->output->set_content_type('application/json');
			$this->output->set_status_header('200');
			$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
			$this->output->set_header('Pragma: no-cache');
			$this->output->set_header('Access-Control-Allow-Origin: ' . base_url());
			$this->output->set_header('Content-Length: '. strlen($_output));
			$this->output->set_output($_output);
		}
	}
}
