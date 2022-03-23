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

namespace ppfilip\regfilter\controller;

use ppfilip\regfilter\constants\constants;

/**
 * Filter by country ACP controller.
 */
class acp_controller
{

	protected $config;
	protected $config_text;
	protected $helper;
	protected $language;
	protected $log;
	protected $phpbb_root_path;
	protected $phpEx;
	protected $request;
	protected $template;
	protected $user;
	protected $u_action;
	protected $db;
	protected $groups_table;
	protected $group_helper;

	/**
	 * Constructor.
	 *
	 * @param \phpbb\config\config				$config				Config object
	 * @param \phpbb\language\language			$language			Language object
	 * @param \phpbb\log\log					$log				Log object
	 * @param \phpbb\request\request			$request			Request object
	 * @param \phpbb\template\template			$template			Template object
	 * @param \phpbb\user						$user				User object
	 * @param \phpbb\config\db_text				$config_text		The config text object
	 * @param \ppfilip\regfilter\core\common 	$helper				Extension's helper object
	 * @param \phpbb\db\driver\driver_interface $db                 Database driver
	 * @param \phpbb\group\helper               $group_helper       Group helper object
	 * @param string							$phpbb_root_path	Relative path to phpBB root
	 * @param string                   			$php_ext         	PHP file suffix
	 * @param string                            $groups_table
	 */
	public function __construct
    (
        \phpbb\config\config $config,
        \phpbb\language\language $language,
        \phpbb\log\log $log,
        \phpbb\request\request $request,
        \phpbb\template\template $template,
        \phpbb\user $user,
        \phpbb\config\db_text $config_text,
        \ppfilip\regfilter\core\common $helper,
        \phpbb\db\driver\driver_interface $db,
        \phpbb\group\helper $group_helper,
        $phpbb_root_path,
        $php_ext,
        $groups_table
    )
	{

		$this->config	= $config;
		$this->config_text = $config_text;
		$this->helper 	= $helper;
		$this->language	= $language;
		$this->log		= $log;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->phpEx 	= $php_ext;
		$this->request	= $request;
		$this->template	= $template;
		$this->user		= $user;
		$this->db = $db;
		$this->groups_table = $groups_table;
		$this->group_helper = $group_helper;

	}

	/**
	 * Display the options a user can configure for this extension.
	 *
	 * @return void
	 */
	public function display_options($mode)
	{

		$this->language->add_lang('common', 'ppfilip/regfilter');

		// Create a form key for preventing CSRF attacks
		add_form_key('ppfilip_regfilter_acp');

		// Create an array to collect errors that will be output to the user
		$errors = array();

		// Is the form being submitted to us?
		if ($this->request->is_set_post('submit'))
		{

			// Test if the submitted form is valid
			if (!check_form_key('ppfilip_regfilter_acp'))
			{
				$errors[] = $this->language->lang('FORM_INVALID');
			}

			// If no errors, process the form data
			if (empty($errors))
			{
				if ($mode == 'settings')
				{
					// Save the setting for the user id
					$this->config->set('ppfilip_regfilter_account_id', $this->request->variable('ppfilip_regfilter_account_id', ''));

                    // Save the setting for the license key
					$this->config->set('ppfilip_regfilter_license_key', $this->request->variable('ppfilip_regfilter_license_key', ''));

                    // Save the setting for endpoint
					$this->config->set('ppfilip_regfilter_endpoint', $this->request->variable('ppfilip_regfilter_endpoint', 'https://geolite.info/geoip/v2.1/country/'));

					// Save the setting for selected countries to be either allowed or restricted
					$this->config->set('ppfilip_regfilter_allow', $this->request->variable('ppfilip_regfilter_allow', 0));

					// Save the setting for whether IPs without a known country should be either allowed or restricted
					$this->config->set('ppfilip_regfilter_ip_not_found_allow', $this->request->variable('ppfilip_regfilter_ip_not_found_allow', 0));

					// Group to move spammers to (0 - disable checks)
					$this->config->set('ppfilip_regfilter_add_blocked_country_to_group', $this->request->variable('ppfilip_regfilter_add_blocked_country_to_group', 0));

					// Save the setting on whether to log access errors
					$this->config->set('ppfilip_regfilter_log_access_errors', $this->request->variable('ppfilip_regfilter_log_access_errors', 0));

					// Save any selected country codes to the database. To save space they will be saved as a string in the phpbb_config_text table. Since there are hundreds of
					// country codes, the phpbb_config_text table is used since we may need more than 254 characters stored.
					$country_codes = $this->request->variable('ppfilip_regfilter_country_codes', array('' => ''));
					$country_codes_str = (!empty($country_codes)) ? implode(',', $country_codes) : '';
					$this->config_text->set('ppfilip_regfilter_country_codes', $country_codes_str);

					// Add option settings change action to the admin log
					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_ACP_REGFILTER_FILTERBYCOUNTRY_SETTINGS');

					trigger_error($this->language->lang('ACP_REGFILTER_SETTING_SAVED') . adm_back_link($this->u_action));
				}
			}
		}

		$s_errors = !empty($errors);

		// Set output variables for display in the template
		if ($mode == 'settings')
		{
			// Populate the settings page fields

			if (strlen(trim($this->config['ppfilip_regfilter_license_key'])) !== 16)
			{
				$errors[] = $this->language->lang('ACP_REGFILTER_INVALID_LICENSE_KEY');
				$s_errors = true;
			}

			$this->template->assign_vars(array(
				'COUNTRY_CODES'                 => $this->config_text->get('ppfilip_regfilter_country_codes'),
				'ERROR_MSG'     				=> $s_errors ? implode('<br>', $errors) : '',
				'REGFILTER_ACCOUNT_ID'			=> $this->config['ppfilip_regfilter_account_id'],
				'REGFILTER_LICENSE_KEY'			=> $this->config['ppfilip_regfilter_license_key'],
				'REGFILTER_ENDPOINT'			=> $this->config['ppfilip_regfilter_endpoint'],
				'REGFILTER_ALLOW_RESTRICT'		=> (bool) $this->config['ppfilip_regfilter_allow'],
				'REGFILTER_IP_NOT_FOUND_ALLOW'	=> (bool) $this->config['ppfilip_regfilter_ip_not_found_allow'],
				'REGFILTER_LOG_ACCESS_ERRORS'	=> (bool) $this->config['ppfilip_regfilter_log_access_errors'],

				'S_GROUP_LIST'  => $this->group_select_options($this->config['ppfilip_regfilter_add_blocked_country_to_group']),
				'S_ERROR'       => $s_errors,
				'S_SETTINGS'    => true,
				'U_ACTION'      => $this->u_action,
			));

			// Populate the options list with a list of countries, in the user's language.
			foreach (constants::REGFILTER_COUNTRY_CODES as $key => $value)
			{
				$this->template->assign_block_vars('country', array(
					'CODE'	=> $value,
					'SELECTED' => $this->helper->str_contains($this->config_text->get('ppfilip_regfilter_country_codes'), $value),
				));
			}

		}

	}


	/**
	 * Generate list of groups for selection
	 * Taken from Akismet extension by Jakub Senko
	 *
	 * @param       integer $selected_group_id      Group ID to mark as selected
	 * @return      string  List of groups in HTML format
	 */
    protected function group_select_options($selected_group_id = 0)
    {
        // Adapted from global function group_select_options in core file functions_admin.php and adapted.
        $sql = 'SELECT group_id, group_type, group_name
	        FROM ' . $this->groups_table . '
	        WHERE (group_type <> ' . GROUP_SPECIAL . " OR group_name = 'NEWLY_REGISTERED') ";
        $result = $this->db->sql_query($sql);

        $s_group_options = '';

        while ($row = $this->db->sql_fetchrow($result))
        {
            $selected = ($row['group_id'] == $selected_group_id) ? ' selected="selected"' : '';
            $s_group_options .= '<option' . (($row['group_type'] == GROUP_SPECIAL) ? ' class="sep"' : '') . ' value="' . $row['group_id'] . '"' . $selected . '>' . $this->group_helper->get_name($row['group_name']) . '</option>';
        }
        $this->db->sql_freeresult($result);

        return $s_group_options;
    }


	/**
	 * Set custom form action.
	 *
	 * @param string	$u_action	Custom form action
	 * @return void
	 */
	public function set_page_url($u_action)
    {
        $this->u_action = $u_action;
    }

}
