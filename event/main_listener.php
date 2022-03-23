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

namespace ppfilip\regfilter\event;

/**
 * @ignore
 */

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ppfilip\regfilter\constants\constants;

/**
 * Filter by country Event listener.
 */
class main_listener implements EventSubscriberInterface
{
	public static function getSubscribedEvents()
	{
		return array(
			'core.user_add_after'	=> 'filter_registration_by_country',
            'core.delete_group_after' => 'group_deleted',
        );
	}

	protected $config;
	protected $config_text;
	protected $helper;
	protected $language;
	protected $log;
	protected $phpbb_root_path;
	protected $phpEx;
	protected $request;
	protected $user;

	/**
	 * Constructor
	 *
	 * @param \phpbb\language\language 						$language        	Language object
	 * @param \phpbb\request\request   						$request         	The request object
	 * @param string                   						$phpbb_root_path 	Relative path to phpBB root
	 * @param string                   						$php_ext         	PHP file suffix
	 * @param \phpbb\config\config     						$config          	The config
	 * @param \phpbb\log\log           						$log             	Log object
	 * @param \phpbb\user              						$user            	User object
	 * @param \phpbb\config\db_text							$config_text		The config text
	 * @param \ppfilip\regfilter\core\common 	$helper				Extension's helper object
	 *
	 */

	public function __construct
    (
        \phpbb\language\language $language,
        \phpbb\request\request $request,
        $phpbb_root_path,
        $php_ext,
        \phpbb\config\config $config,
        \phpbb\log\log $log,
        \phpbb\user $user,
        \phpbb\config\db_text $config_text,
        \ppfilip\regfilter\core\common $helper
    )
	{

		$this->config = $config;
		$this->config_text = $config_text;
		$this->helper = $helper;
		$this->language = $language;
		$this->log = $log;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->phpEx = $php_ext;
		$this->request = $request;
		$this->user = $user;
	}

    /**
     * Execute code at the end of user setup
     *
     * @event core.user_setup_after
     * @since 3.1.6-RC1
     */

    /**
     * Check a new user registration for blacklisted country.
     *
     * @param \phpbb\event\data $event
     */
    public function filter_registration_by_country($event)
    {
        if ($group_id = $this->config['ppfilip_regfilter_add_blocked_country_to_group'])
        {
            $user_ip = $this->user->ip;
            $user_id = $event['user_id']; // Can't use $this->user->data['user_id'] as there isn't an actual user logged in during registration, of course.
            $user_row = $event['user_row'];

            $iso_code = $this->get_country($user_ip);

            $is_on_blacklist = (
                ($this->config['ppfilip_regfilter_allow'] xor $this->helper->str_contains($this->config_text->get('ppfilip_regfilter_country_codes'), $iso_code))
                or
                (not($this->config['ppfilip_regfilter_ip_not_found_allow']) and $iso_code == constants::ACP_REGFILTER_COUNTRY_NOT_FOUND)
            );

            if ($is_on_blacklist)
            {
                if ($this->config['ppfilip_regfilter_log_access_errors'])
                {
                    $log_message = 'LOG_ACP_REGFILTER_BAD_ACCESS';
                    $this->log->add('mod', $user_id, $this->user->ip, $log_message, false, array($user_row['username'], $group_id, $this->user->ip, $iso_code));
                }
                $this->group_user_add($group_id, $user_id);
            }
        }
    }

    /**
     * If someone deletes a group we're configured to add users to, update
     * our configuration. Should avoid problems.
     * Taken from Akismet extension by Jakub Senko
     *
     * @param \phpbb\event\data $event
     */
    public function group_deleted($event)
    {
        if ($event['group_id'] == $this->config['ppfilip_regfilter_add_blocked_country_to_group']) {
            $this->config->set('ppfilip_regfilter_add_blocked_country_to_group', 0);
            $this->log_disable_group_add($event['group_name']);
        }
    }

     /**
     * Add user to a group. Load phpBB function when needed.
     * Taken from Akismet extension by Jakub Senko
     * @param int $group_id
     * @param int $user_id
     */
    protected function group_user_add($group_id, $user_id)
    {
        if (!function_exists('group_user_add'))
        {
            include $this->phpbb_root_path . 'includes/functions_user.' . $this->php_ext;
        }

        group_user_add($group_id, $user_id);
    }

    /**
     * Log situation when we stop adding new potential spammers to designated group
     * because it was removed.
     * Taken from Akismet extension by Jakub Senko
     *
     * @param	string	$group_name	Group name
     */
    protected function log_disable_group_add($group_name)
    {
        $this->log->add('mod', $this->user->data['user_id'], $this->user->ip, 'LOG_REGFILTER_SPAMMER_GROUP_REMOVED', false, array($group_name));
    }

    /**
     * Call Maxmind API and see if country is blacklisted
     *
     * @param string $params
     * @return      boolean   Result
     */
    protected function get_country($user_ip)
    {
        try
        {
            $api_id = $this->config['ppfilip_regfilter_account_id'];
            $api_key = $this->config['ppfilip_regfilter_license_key'];
            $api_url = $this->config['ppfilip_regfilter_endpoint'] . $user_ip;

            $ch = curl_init($api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_USERPWD, $api_id . ":" . $api_key);
            $data = curl_exec($ch);
            curl_close($ch);

            if (isset($data))
            {
                $decoded = json_decode($data, 1);
                if (isset($decoded) and array_key_exists('country', $decoded) and array_key_exists('iso_code', $decoded['country']))
                {
                    $iso_code = $decoded['country']['iso_code'];
                    return $iso_code;
                }
                else
                {
                    error_log("Error decoding maxmind data: " . $data);
                    return constants::ACP_REGFILTER_COUNTRY_NOT_FOUND;
                }
            }
            else
            {
                error_log("Received empty maxmind data");
                return constants::ACP_REGFILTER_COUNTRY_NOT_FOUND;
            }

        }
        catch (Exception $e)
        {
            error_log($e);
            return constants::ACP_REGFILTER_COUNTRY_NOT_FOUND;
        }
    }



}
