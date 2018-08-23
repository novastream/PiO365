<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Assets
{
    /**
     * @var
     */
    private $assets;

    /**
     * @var CI_Controller
     */
    private $ci;

    /**
     * Asset constructor.
     */
    public function __construct()
    {
        $this->ci = &get_instance();
    }

    /**
     * @param $config
     * @return $this
     */
    public function init($config = array())
    {
        // set public dir if missing
        if (!isset($config['public_dir']))
        {
            $public_dir = FCPATH;
            $public_dir = rtrim($public_dir, '/');            
            $config['public_dir'] = $public_dir;
        }
        
        // set js dir if missing
        if (!isset($config['js_dir']))
        {
            $config['js_dir'] = config_item('assets_js_dir');
        }

        // set css dir if missing
        if (!isset($config['css_dir']))
        {
            $config['css_dir'] = config_item('assets_css_dir');
        }

        // set pipeline dir if missing
        if (!isset($config['pipeline_dir']))
        {
            $config['pipeline_dir'] = config_item('assets_static_dir');
        }

        // set gzip compression if missing
        if (!isset($config['pipeline_gzip']))
        {
            $config['pipeline_gzip'] = config_item('pipeline_gzip');
        }

        // set fetch command if missing
        if (!isset($config['fetch_command']))
        {
            $config['fetch_command'] = function ($asset) {

                $content = file_get_contents($asset);
                $prefix = str_replace(base_url() . '/', '', dirname($asset)) . '/';
        
                $regex = "/\burl\(\s?[\'\"]?(([^';]+)\.(jpg|eot|jpg|jpeg|gif|png|ttf|woff|svg).*?)[\'\"]?\s?\)/";
        
                $filter = function ($match) use ($prefix) {
        
                    // Do not process absolute URLs
                    if('http://' === substr($match[1], 0, 7) or 'https://' === substr($match[1], 0, 8) or '//' === substr($match[1], 0, 2))
                    {
                        return $match[0];
                    }
        
                    // Add your filter logic here
                    return 'url(\''. $prefix.$match[1] . '\')';
                };
        
                // Apply filter
                return preg_replace_callback($regex, $filter, $content);
            };
        }

        // create a new instance
        $this->assets = new \Stolz\Assets\Manager($config);
        return $this;
    }

    /**
     * @param $asset
     * @return $this
     */
    public function add($asset)
    {
        $this->assets->add($asset);
        return $this;
    }

    /**
     * @return $this
     */
    public function css()
    {
        return $this->assets->css();
    }

    /**
     * @return $this
     */
    public function js()
    {
        return $this->assets->js();
    }
}
