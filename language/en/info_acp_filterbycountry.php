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

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// » “ “ …
//

$lang = array_merge($lang, array(
	'ACP_REGFILTER'					=> 'Filter registrations by country',
	'ACP_REGFILTER_TITLE'			=> 'Filter registrations by country settings',
	'ACP_REGFILTER_TITLE_EXPLAIN'	=> 'This extension allows you to filter registrations to your board based on country associated with user IP address. This product uses GeoLite2 database by MaxMind, available from <a href="https://www.maxmind.com" target="_blank">https://www.maxmind.com</a>.',
	'ACP_REGFILTER_TITLE_SHORT'		=> 'Settings',

    'ACP_REGFILTER_ACCOUNT_ID'				=> 'MaxMind account id',
    'ACP_REGFILTER_ACCOUNT_ID_EXPLAIN'		=> 'To use MaxMind’s GeoLite2 country code database, you must <a href="https://dev.maxmind.com/geoip/geoip2/geolite2/" target="_blank">acquire an account id</a>.',
    'ACP_REGFILTER_LICENSE_KEY'				=> 'MaxMind license key',
    'ACP_REGFILTER_LICENSE_KEY_EXPLAIN'		=> 'To use MaxMind’s GeoLite2 country code database, you must <a href="https://dev.maxmind.com/geoip/geoip2/geolite2/" target="_blank">acquire a license key</a>. You do <em>not</em> need to purchase a license. Enter the 16 character license key here. You must register on their site to acquire a license key.',
    'ACP_REGFILTER_ENDPOINT'				=> 'MaxMind endpoint',
    'ACP_REGFILTER_ENDPOINT_EXPLAIN'		=> 'Select appropriate endpoint (geoip2, geolite)',
    'ACP_REGFILTER_ADD_DETECTED_SPAMMERS_TO_GROUP'				=> 'Add spammy registrations to group',
    'ACP_REGFILTER_ADD_DETECTED_SPAMMERS_TO_GROUP_EXPLAIN'		=> 'Select a group to add to. Filtering is disabled if no group is selected.',
    'ACP_REGFILTER_DONT_ADD_REGISTERING_SPAMMERS_TO_GROUP'				=> 'Disable extension',
    'ACP_REGFILTER_LOG_ACCESS_ERRORS'			=> 'Log access errors',
    'ACP_REGFILTER_LOG_ACCESS_ERRORS_EXPLAIN'	=> 'If yes, any restricted IPs are logged in the admin log. This can result in very long logs.',
    'ACP_REGFILTER_IP_NOT_FOUND_ALLOW'			=> 'Allow or restrict unknown IPs',
    'ACP_REGFILTER_IP_NOT_FOUND_ALLOW_EXPLAIN'	=> 'If set to restrict, IPs originating from unknown countries will be added to group above.',
    'ACP_REGFILTER_ALLOW_RESTRICT'			=> 'Allow or restrict the selected countries',
    'ACP_REGFILTER_ALLOW_RESTRICT_EXPLAIN'	=> 'If set to allow, only connections originating from the countries you select are allowed. If set to restrict, connections from all countries are allowed <em>except</em> those you select.',
    'ACP_REGFILTER_COUNTRIES'				=> 'Select one or more country codes',
    'ACP_REGFILTER_COUNTRIES_EXPLAIN'		=> 'Hold the Ctrl down (Command on a Mac) to select multiple country codes. You can select a range of countries by holding down the Shift key when selecting the last country in the range.',

    'ACP_REGFILTER_ALLOW'					=> 'Allow',
    'ACP_REGFILTER_COUNTRY_NAME'			=> 'Country name',
    'ACP_REGFILTER_EFFECTIVELY_DISABLED'	=> 'To avoid locking down your board, all traffic is currently allowed. This can occur if no countries were selected. Please change your settings. If you want to do this permanently, please disable this extension.',
    'ACP_REGFILTER_IGNORE'					=> 'Ignore',
    'ACP_REGFILTER_INVALID_LICENSE_KEY'		=> 'Your license key is invalid. Enter a valid MaxMind license key.',
    'ACP_REGFILTER_MAXMIND_ERROR'			=> 'A call to the MaxMind country code database triggered an error. The database is most likely corrupt. You might want to inform the webmaster.',
    'ACP_REGFILTER_RESTRICT'				=> 'Restrict',
    'ACP_REGFILTER_SETTING_SAVED'			=> 'Settings have been saved successfully!',
    'ACP_REGFILTER_UNSELECT_ALL'			=> 'Clear country selection.',

    'LOG_ACP_REGFILTER_BAD_ACCESS'				=> '<strong>Filter registrations: %1$s was moved to group %2$s</strong><br />Registration IP %3$s matches forbidden country %4$s.',
	'LOG_ACP_REGFILTER_DEBUG'					=> '<strong>%1$s</strong>',
	'LOG_ACP_REGFILTER_FILTERBYCOUNTRY_SETTINGS'=> '<strong>Filter registrations by country settings updated</strong>',
    'LOG_REGFILTER_SPAMMER_GROUP_REMOVED'			=> '<strong>Filter registrations: Group %1$s was deleted</strong><br />Module will no longer add new spammy registrations to a group'

));
