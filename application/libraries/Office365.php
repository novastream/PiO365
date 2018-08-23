<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Office365
{
    /**
     * @var CI_Controller
    */
    private $ci;

    /**
     * Constructor.
    */
    public function __construct()
    {
        $this->ci = &get_instance();
    }

    /**
     * Gets all settings and returns them as a object
    */
    public function getSettings()
    {
        $settings = array(
            'locale' => $this->ci->config->item('locale'),
            'layout' => $this->ci->config->item('layout'),
            'theme' => $this->ci->config->item('theme'),
            'default_view' => $this->ci->config->item('default_view'),
            'week_number' => $this->ci->config->item('week_number'),
            'time_format' => $this->ci->config->item('time_format'),
            'calendar_timezone' => $this->ci->config->item('calendar_timezone'),
            'default_calendar' => $this->ci->config->item('default_calendar'),			
			'OAUTH_APP_ID' => $this->ci->config->item('OAUTH_APP_ID'),
			'OAUTH_APP_PASSWORD' => $this->ci->config->item('OAUTH_APP_PASSWORD'),
            'OAUTH_REDIRECT_URI' => $this->ci->config->item('OAUTH_REDIRECT_URI'),
            'OAUTH_SCOPES' => $this->ci->config->item('OAUTH_SCOPES'),
            'OAUTH_AUTHORITY' => $this->ci->config->item('OAUTH_AUTHORITY'),
            'OAUTH_AUTHORIZE_ENDPOINT' => $this->ci->config->item('OAUTH_AUTHORIZE_ENDPOINT'),
            'OAUTH_TOKEN_ENDPOINT' => $this->ci->config->item('OAUTH_TOKEN_ENDPOINT'),
			'API_URL' => $this->ci->config->item('API_URL'),
            'API_CALENDARS_ENDPOINT' => $this->ci->config->item('API_CALENDARS_ENDPOINT'),
            'API_EVENTS_ENDPOINT' => $this->ci->config->item('API_EVENTS_ENDPOINT'),
            'API_EVENTS_FILTER' => $this->ci->config->item('API_EVENTS_FILTER'),
			'app_password' => $this->ci->config->item('app_password')
        );
        
        return (object)$settings;
    }

    /**
     * Get all timezones in a array
    */
    public function getTimezones()
    {
        $zones_array = array();
        $timestamp = time();
        
        foreach(timezone_identifiers_list() as $key => $zone) 
        {
            date_default_timezone_set($zone);
            $zones_array[$key]['zone'] = $zone;
            $zones_array[$key]['diff_from_GMT'] = 'UTC/GMT ' . date('P', $timestamp);
        }
        
        return $zones_array;
        
    }

    /**
     * Checks if token is valid and refreshes the token otherwise
    */
    public function getAccessToken()
    {
        // current time + 5 minutes
		$currentTime = time() + 300;

		if (!$this->ci->session->access_token)
		{
			return false;
		}
		else 
		{
			// token is expired or soon to be expired, create a new one
			if ($this->ci->session->token_expires <= $currentTime)
			{
                // get settings
                $settings = array(
                    'OAUTH_APP_ID' => $this->ci->config->item('OAUTH_APP_ID'),
                    'OAUTH_APP_PASSWORD' => $this->ci->encryption->decrypt($this->ci->config->item('OAUTH_APP_PASSWORD')),
                    'OAUTH_REDIRECT_URI' => $this->ci->config->item('OAUTH_REDIRECT_URI'),
                    'OAUTH_SCOPES' => $this->ci->config->item('OAUTH_SCOPES'),
                    'OAUTH_AUTHORITY' => $this->ci->config->item('OAUTH_AUTHORITY'),
                    'OAUTH_AUTHORIZE_ENDPOINT' => $this->ci->config->item('OAUTH_AUTHORIZE_ENDPOINT'),
                    'OAUTH_TOKEN_ENDPOINT' => $this->ci->config->item('OAUTH_TOKEN_ENDPOINT'),
                );

                // generate new token
                $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
                    'clientId'                => $settings['OAUTH_APP_ID'],
                    'clientSecret'            => $settings['OAUTH_APP_PASSWORD'],
                    'redirectUri'             => $settings['OAUTH_REDIRECT_URI'],
                    'urlAuthorize'            => $settings['OAUTH_AUTHORITY'].$settings['OAUTH_AUTHORIZE_ENDPOINT'],
                    'urlAccessToken'          => $settings['OAUTH_AUTHORITY'].$settings['OAUTH_TOKEN_ENDPOINT'],
                    'urlResourceOwnerDetails' => '',
                    'scopes'                  => $settings['OAUTH_SCOPES']
                ]);

                try 
                {
                    $newToken = $oauthClient->getAccessToken('refresh_token', ['refresh_token' => $this->ci->session->refresh_token]);
                
                    $this->storeTokens($newToken->getToken(), $newToken->getRefreshToken(), $newToken->getExpires());

                    return $newToken->getToken();
                }
                catch (League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) 
                {
                    return false;
                }
            }
            else 
            {
                return $this->ci->session->access_token;
            }
        }
    }

    /**
     * Store tokens in session for later use
    */
    public function storeTokens($access_token, $refresh_token, $expires)
    {
        $this->ci->session->access_token = $access_token;
        $this->ci->session->refresh_token = $refresh_token;
        $this->ci->session->expires = $expires;
    }

    /**
     * Clear tokens
    */
    public function clearTokens()
    {
        unset($_SESSION['access_token']);
        unset($_SESSION['refresh_token']);
        unset($_SESSION['expires']);
    }
}
