<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Theme Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Novastream AB
 * @link		http://www.novastream.se
 */

if(!function_exists('theme_url'))
{
    /**
     * Get theme url for Blade instance
     * @param string
     * @return string
     */
    function theme_url($url)
    {
        $ci = &get_instance();
        $url = ltrim($url,'/');
        return $ci->theme->getThemeUrl().$url;
    }
}
