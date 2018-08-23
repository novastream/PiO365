<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Authentication extends CI_Controller {
	
	// constructor
	public function __construct() {
		parent::__construct();
	}

	// default
	public function index()
	{
		
	}

	// signin and get code
	public function signin($referer = null)
	{
		// get referer if any
		$referer = $this->security->xss_clean($referer);

		// set referer
		if (empty($referer))
		{
			$this->session->set_flashdata('referer', 'calendar');
		}
		else 
		{
			$this->session->set_flashdata('referer', $referer);
		}

		// get settings
		$settings = $this->office365->getSettings();

		// init OAuth client
		$oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
			'clientId'                => $settings->OAUTH_APP_ID,
			'clientSecret'            => $this->encryption->decrypt($settings->OAUTH_APP_PASSWORD),
			'redirectUri'             => $settings->OAUTH_REDIRECT_URI,
			'urlAuthorize'            => $settings->OAUTH_AUTHORITY.$settings->OAUTH_AUTHORIZE_ENDPOINT,
			'urlAccessToken'          => $settings->OAUTH_AUTHORITY.$settings->OAUTH_TOKEN_ENDPOINT,
			'urlResourceOwnerDetails' => '',
			'scopes'                  => $settings->OAUTH_SCOPES
		]);

		// generate url
		$authorizationUrl = $oauthClient->getAuthorizationUrl();

		// save client state
		$this->session->oauth_state = $oauthClient->getState();

		// redirect user
		redirect($authorizationUrl);
		exit;
	}

	/**
	 * Authorize
	*/
	public function authorize()
	{
		// get params
		$code = $this->input->get('code', true);
		$state = $this->input->get('state', true);
		$error = $this->input->get('error', true);
		$error_description = $this->input->get('error_description', true);

		if (!empty($code))
		{
			if (empty($state) || ($state !== $this->session->oauth_state)) 
			{
				$response = array(
					'status' => 'error',
					'message' => 'State provided in redirect does not match expected value.',
					'data' => ''
				);
			}
			else 
			{
				unset($_SESSION['oauth_state']);

				// get settings
				$settings = $this->office365->getSettings();

				// init OAuth client
				$oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
					'clientId'                => $settings->OAUTH_APP_ID,
					'clientSecret'            => $this->encryption->decrypt($settings->OAUTH_APP_PASSWORD),
					'redirectUri'             => $settings->OAUTH_REDIRECT_URI,
					'urlAuthorize'            => $settings->OAUTH_AUTHORITY.$settings->OAUTH_AUTHORIZE_ENDPOINT,
					'urlAccessToken'          => $settings->OAUTH_AUTHORITY.$settings->OAUTH_TOKEN_ENDPOINT,
					'urlResourceOwnerDetails' => '',
					'scopes'                  => $settings->OAUTH_SCOPES
				]);
	
				try 
				{
					// access token
					$accessToken = $oauthClient->getAccessToken('authorization_code', ['code' => $code]);

					// store access token
					$this->office365->storeTokens($accessToken->getToken(), $accessToken->getRefreshToken(), $accessToken->getExpires());

					// set response if needed
					$response = array(
						'status' => 'success',
						'message' => '',
						'data' => array(
							'accessToken' => $accessToken->getToken()
						)
					);
				}
				catch (League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) 
				{
					$response = array(
						'status' => 'error',
						'message' => 'ERROR getting tokens: '.$e->getMessage(),
						'data' => ''
					);
				}
			}
		}
		elseif (!empty($error))
		{
			$response = array(
				'status' => 'error',
				'message' => 'ERROR: '.$error.' - '.$error_description,
				'data' => ''
			);
		}
		else 
		{
			$response = array(
				'status' => 'error',
				'message' => 'Code is missing.',
				'data' => ''
			);
		}

		// store response as flashdata
		$this->session->set_flashdata('response', $response);

		if ($this->session->flashdata('referer') == 'calendar')
		{
			redirect(site_url());		
			exit;
		}
		else 
		{
			redirect(site_url('config'));		
			exit;
		}
	}
}
