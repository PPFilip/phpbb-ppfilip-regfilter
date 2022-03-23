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

namespace ppfilip\regfilter\acp;

/**
 * Filter by country ACP module.
 */
class main_module
{
	public $page_title;
	public $tpl_name;
	public $u_action;

	/**
	 * Main ACP module
	 *
	 * @param int    $id   The module ID
	 * @param string $mode The module mode (for example: manage or settings)
	 * @throws \Exception
	 */
	public function main($id, $mode)
	{
		global $phpbb_container;

		/** @var \ppfilip\regfilter\controller\acp_controller $acp_controller */
		$acp_controller = $phpbb_container->get('ppfilip.regfilter.controller.acp');

		/** @var \phpbb\language\language $language */
		$language = $phpbb_container->get('language');

		/** @param \phpbb\request\request	$request	Request object */
		$request = $phpbb_container->get('request');

		// Load a template from adm/style for our ACP page
		$this->tpl_name = 'acp_regfilter_body';

		// Get the mode
		$mode = $request->variable('mode', 'settings');

		// Set the page title for our ACP page
		if ($mode == 'settings')
		{
			$this->page_title = $language->lang('ACP_REGFILTER_TITLE_SHORT');
		}

		// Make the $u_action url available in our ACP controller
		$acp_controller->set_page_url($this->u_action);
		// Load the display options handle in our ACP controller
		$acp_controller->display_options($mode);
	}
}
