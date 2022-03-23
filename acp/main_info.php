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
 * Filter by country ACP module info.
 */
class main_info
{
	public function module()
	{
		return array(
			'filename'	=> 'ppfilip\regfilter\acp\main_module',
			'title'		=> 'ACP_REGFILTER',
			'modes'		=> array(
				'settings'	=> array(
					'title'	=> 'ACP_REGFILTER_TITLE_SHORT',
					'auth'	=> 'ext_ppfilip/regfilter && acl_a_board',
					'cat'	=> array('ACP_REGFILTER')
				),
			),
		);
	}
}
