<?php
/**
 *
 * Filter registrations by country. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2022, PPFilip, https://github.com/PPFilip
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * This work is heavily based on Filter by country by Mark D. Hamill, https://www.phpbbservices.com
 */

namespace ppfilip\regfilter\core;

class common
{

	/**
	 * Constructor
	 */

	protected $config;
	protected $language;
	protected $phpbb_log;
	protected $phpbb_root_path;
	protected $request;
	protected $user;

	/**
	 * Constructor.
	 *
	 * @param \phpbb\language\language 	$language 			Language object
	 * @param string					$phpbb_root_path	Relative path to phpBB root
	 * @param \phpbb\config\config 		$config 			The config
	 * @param \phpbb\log\log 			$phpbb_log 			phpBB log object
	 * @param \phpbb\user				$user				User object
	 * @param \phpbb\request\request	$request			Request object
	 *
	 */

	public function __construct
    (
        \phpbb\language\language $language,
        $phpbb_root_path,
        \phpbb\config\config $config,
        \phpbb\log\log $phpbb_log,
        \phpbb\user $user,
        \phpbb\request\request $request
    )
	{
		$this->config = $config;
		$this->language = $language;
		$this->phpbb_log = $phpbb_log;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->request	= $request;
		$this->user	= $user;
	}


	public function get_country_name ($country_code)
	{
		// Gets the name of the country in the user's language. What's returned by MaxMind is the country's name in English.
		$this->language->add_lang('common', 'ppfilip/regfilter');

		$country_name = (isset($this->user->lang['ACP_REGFILTER_COUNTRY_' . $country_code])) ? $this->user->lang['ACP_REGFILTER_COUNTRY_' . $country_code] : $this->language->lang('ACP_REGFILTER_UNKNOWN');

		return $country_name;
	}


    public function str_contains($haystack, $needle)
    {
        if (!function_exists('str_contains'))
        {
            return $needle !== '' && mb_strpos($haystack, $needle) !== false;
        }
        else
        {
            return str_contains($haystack, $needle);
        }
    }


}
