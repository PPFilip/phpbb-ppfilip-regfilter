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

namespace ppfilip\regfilter\migrations;

class install_acp_module extends \phpbb\db\migration\container_aware_migration
{

	public function effectively_installed()
	{
		$sql = 'SELECT module_id
			FROM ' . $this->table_prefix . "modules
			WHERE module_class = 'acp'
				AND module_langname = 'ACP_REGFILTER_TITLE'";
		$result = $this->db->sql_query($sql);
		$module_id = $this->db->sql_fetchfield('module_id');
		$this->db->sql_freeresult($result);
		return $module_id !== false;
	}

	public static function depends_on()
	{
		return array('\phpbb\db\migration\data\v330\v330');
	}

	public function update_data()
	{
		return array(
			array('config.add', array('ppfilip_regfilter_account_id', '')),
			array('config.add', array('ppfilip_regfilter_license_key', '')),
			array('config.add', array('ppfilip_regfilter_endpoint', 'https://geolite.info/geoip/v2.1/country/')),
			array('config.add', array('ppfilip_regfilter_allow', 1)),
			array('config.add', array('ppfilip_regfilter_ip_not_found_allow', 1)),
            array('config.add', array('ppfilip_regfilter_add_blocked_country_to_group', 0)),
			array('config.add', array('ppfilip_regfilter_log_access_errors', 0)),
			array('config_text.add', array('ppfilip_regfilter_country_codes', '')),

			array('module.add', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_REGFILTER'
			)),
			array('module.add', array(
				'acp',
				'ACP_REGFILTER',
				array(
					'module_basename'	=> '\ppfilip\regfilter\acp\main_module',
					'modes'				=> array('settings'),
				),
			)),
		);
	}

}
