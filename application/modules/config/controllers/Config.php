<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Config extends CI_Controller {
	
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
		// is page password protected?
		if (!empty($this->config->item('app_password')))
		{
			if (!isset($this->session->loggedin))
			{
				redirect(site_url('config/login'));
				exit;
			}
		}

		// show help
		$showHelp = '';

		// assets
		$this->assets->init(array('pipeline' => false));
		$this->assets->add(array(
			base_url('assets/bootstrap/dist/css/bootstrap.min.css'),
			base_url('assets/font-awesome/web-fonts-with-css/css/fontawesome-all.min.css'),
			base_url('assets/select2/dist/css/select2.min.css'),
			base_url('assets/app.css')
		));
		$this->assets->add(array(
			base_url('assets/jquery/dist/jquery.min.js'),
			base_url('assets/popper.js/dist/umd/popper.min.js'),
			base_url('assets/bootstrap/dist/js/bootstrap.min.js'),
			base_url('assets/select2/dist/js/select2.min.js')
		));

		// settings
		$settings = $this->office365->getSettings();

		// timeszones
		$timezones = $this->office365->getTimezones();

		// calendar object
		$calendars = (object)array('value' => '');

		// check for valid token
		if ($this->office365->getAccessToken() != false)
		{
			try 
			{
				$client = new GuzzleHttp\Client();

				$res = $client->request('GET', $settings->API_URL.$settings->API_CALENDARS_ENDPOINT, [
					'headers' => [
						'Accept' 			=> 'application/json;odata.metadata=minimal;odata.streaming=true',
						'Content-type' 		=> 'application/json',
						'Authorization'     => 'Bearer ' . $this->office365->getAccessToken()
					]
				]);

				$calendars = json_decode($res->getBody()->getContents());
			}
			catch(Exception $e)
			{
				
			}
		}

		// check if OAUTH settings is set
		if (!empty($settings->OAUTH_APP_ID) && !empty($settings->OAUTH_APP_PASSWORD) && !empty($settings->OAUTH_REDIRECT_URI))
		{
			if ($this->office365->getAccessToken() == false)
			{
				$showHelp = 'signin';
			}
		}

		//get response
		$response = $this->session->flashdata('response');

		// set post
		if (isset($response) && isset($response['data']))
		{
			$_POST = $response['data'];
		}

		// view data
		$view_data = array(
			'appName' => 'PiO365',
			'pageTitle' => 'Configuration',
			'settings' => $settings,
			'response' => $response,
			'calendars' => $calendars->value,
			'timezones' => $timezones,
			'showHelp' => $showHelp,
			'site_url' => site_url(),
			'base_url' => base_url(),
			'css' => $this->assets->css(),
			'js' => $this->assets->js()
		);

		// load up blade engine
		$this->theme->display('config.show', $view_data);
	}

	/**
	 * Save configuration
	*/
	public function save()
	{
		// prepare rules
		$this->form_validation->set_rules('layout', 'Layout', 'required|max_length[5]|trim');
		$this->form_validation->set_rules('theme', 'Theme', 'required|max_length[10]|trim');
		$this->form_validation->set_rules('locale', 'Locale', 'required|max_length[2]|trim');
		$this->form_validation->set_rules('default_view', 'Default view', 'required|max_length[10]|trim');
		$this->form_validation->set_rules('week_number', 'Show week numbers', 'required|max_length[5]|trim');
		$this->form_validation->set_rules('time_format', 'Time format', 'required|max_length[20]|trim');
		$this->form_validation->set_rules('calendar_timezone', 'Timezone', 'required|max_length[100]|trim');
		$this->form_validation->set_rules('oauth_app_id', 'Oauth App ID', 'max_length[255]|trim');
		$this->form_validation->set_rules('oauth_app_password', 'Oauth App Password', 'max_length[255]|trim');
		$this->form_validation->set_rules('oauth_redirect_uri', 'Oauth Redirect Uri', 'valid_url|trim');
		$this->form_validation->set_rules('app_password', 'Password', 'min_length[8]|trim');
		$this->form_validation->set_rules('calendar', 'Calendar', 'trim');

		// run validation
		if ($this->form_validation->run() == FALSE)
		{
			// set response
			$response = array(
				'status' => 'error',
				'message' => $this->form_validation->error_array(),
				'data' => $this->input->post()
			);

			// store response as flashdata
			$this->session->set_flashdata('response', $response);

			// redirect
			redirect('config');
			exit;
		}
		else 
		{
			// prepare variables
			$layout = $this->input->post('layout', true);
			$theme = $this->input->post('theme', true);
			$locale = $this->input->post('locale', true);
			$default_view = $this->input->post('default_view', true);
			$week_number = $this->input->post('week_number', true);
			$time_format = $this->input->post('time_format', true);
			$calendar_timezone = $this->input->post('calendar_timezone', true);
			$oauth_app_id = $this->input->post('oauth_app_id', true);
			$oauth_app_password = $this->input->post('oauth_app_password', true);
			$oauth_redirect_uri = $this->input->post('oauth_redirect_uri', true);
			$app_password = $this->input->post('app_password', true);
			$calendar = $this->input->post('calendar', true);

			// get writer instance
			$writer = $this->config_writer->get_instance(APPPATH.'config/pio365.php');
			
			// write to file
			$writer->write('layout', $layout);
			$writer->write('theme', $theme);
			$writer->write('locale', $locale);
			$writer->write('default_view', $default_view);

			if ($week_number == 'true')
			{
				$writer->write('week_number', true);
			}
			else 
			{
				$writer->write('week_number', false);
			}

			$writer->write('time_format', $time_format);
			$writer->write('calendar_timezone', $calendar_timezone);
			$writer->write('OAUTH_APP_ID', $oauth_app_id);

			// only write if oauth pass provided
			if (!empty($oauth_app_password))
			{
				$writer->write('OAUTH_APP_PASSWORD', $this->encryption->encrypt($oauth_app_password));
			}
			
			$writer->write('OAUTH_REDIRECT_URI', $oauth_redirect_uri);

			// only write password if password is provided
			if (!empty($app_password))
			{
				$writer->write('app_password', $this->encryption->encrypt($app_password));
			}

			$writer->write('default_calendar', $calendar);

			// set response
			$response = array(
				'status' => 'success',
				'message' => 'Settings have been updated',
				'data' => ''
			);

			// store response as flashdata
			$this->session->set_flashdata('response', $response);

			// redirect
			redirect('config');
			exit;

		}
	}

	/**
	 * Login
	*/
	public function login()
	{
		// assets
		$this->assets->init(array('pipeline' => false));
		$this->assets->add(array(
			base_url('assets/bootstrap/dist/css/bootstrap.min.css'),
			base_url('assets/font-awesome/web-fonts-with-css/css/fontawesome-all.min.css'),
			base_url('assets/app.css')
		));
		$this->assets->add(array(
			base_url('assets/jquery/dist/jquery.min.js'),
			base_url('assets/popper.js/dist/umd/popper.min.js'),
			base_url('assets/bootstrap/dist/js/bootstrap.min.js')
		));

		//get response
		$response = $this->session->flashdata('response');

		// view data
		$view_data = array(
			'appName' => 'PiO365',
			'pageTitle' => 'Login',			
			'response' => $response,
			'site_url' => site_url(),
			'base_url' => base_url(),
			'css' => $this->assets->css(),
			'js' => $this->assets->js()
		);

		// load up blade engine
		$this->theme->display('config.login', $view_data);
	}

	/**
	 * Perform login
	*/
	public function doLogin()
	{
		// prepare rules
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[8]|trim');

		// run validation
		if ($this->form_validation->run() == FALSE)
		{
			// set response
			$response = array(
				'status' => 'error',
				'message' => $this->form_validation->error_array(),
				'data' => $this->input->post()
			);

			// store response as flashdata
			$this->session->set_flashdata('response', $response);

			// redirect
			redirect('config/login');
			exit;
		}
		else 
		{
			// get the password provided and encrypt it
			$password = $this->input->post('password', true);

			if ($password == $this->encryption->decrypt($this->config->item('app_password')))
			{
				$this->session->loggedin = true;
				redirect(site_url('config'));
				exit;
			}
			else 
			{
				$response = array(
					'status' => 'error',
					'message' => 'Unsuccessful login. Please try again.',
					'data' => ''
				);

				// store response as flashdata
				$this->session->set_flashdata('response', $response);

				// redirect
				redirect('config/login');
				exit;

			}
		}
	}

	/**
	 * Perform logout
	*/
	public function logout()
	{
		unset($_SESSION['loggedin']);
		redirect(site_url());
		exit;
	}

	/**
	 * Perform dissconnect
	*/
	public function dissconnect()
	{
		// clear tokens
		$this->office365->clearTokens();

		// set response
		$response = array(
			'status' => 'success',
			'message' => 'You successfully dissconnected from the Office 365 API service.',
			'data' => ''
		);

		// store response as flashdata
		$this->session->set_flashdata('response', $response);

		// redirect
		redirect('config');
		exit;
	}
}
