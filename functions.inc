<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Nettools Plugin 2.1 for Geeklog                                           |
// +---------------------------------------------------------------------------+
// | functions.inc                                                             |
// | This file does two things: 1) it implements the necessary Geeklog Plugin  |
// | API method and 2) implements all the common code needed by the {plugin}   |
// | PHP files.                                                                |
// |                                                                           |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2002-2010 by the following authors:                         |
// |                                                                           |
// | Authors: Tom Willett      - twillett AT users DOT sourceforge DOT net     |
// +---------------------------------------------------------------------------+
// |                                                                           |
// | This program is free software; you can redistribute it and/or             |
// | modify it under the terms of the GNU General Public License               |
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
// | GNU General Public License for more details.                              |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
// |                                                                           |
// +---------------------------------------------------------------------------+

/**
* Language file Include
*/
$langfile = $_CONF['path'] . 'plugins/nettools/language/' . $_CONF['language'] . '.php';

if (file_exists($langfile)) {
    require_once $langfile;
} else {
    require_once $_CONF['path'] . 'plugins/nettools/language/english.php';
}


// +---------------------------------------------------------------------------+
// | Geeklog Plugin API Implementations                                        |
// +---------------------------------------------------------------------------+

/**
* Returns the items for this plugin that should appear on the main menu
*
* NOTE: this MUST return the url/value pairs in the following format
* $<arrayname>[<label>] = <url>
*
* 
* @return    array   Associative array of menu items to be added to menu
*
*/
function plugin_getmenuitems_nettools()
{
    $menuitems = array();
    return $menuitems;
}

/**
* Check to see if this Plugin uses comments
*
* Geeklog is checking to see if this plugin supports comments, tell it no!
* NOTE: to support comments you must use the same date/time based ID for your
* widget.  In other words, to make primary keys for your plugin you should call
* makesid().  Comments are keyed off of that...it is a limitation on how geeklog
* does comments.
*
* @return   boolean true or false
*
*/
function plugin_commentsupport_nettools() 
{
    return false;
}

/**
* Shows the statistics for the plugin on stats.php.  If
* $showsitestats is 1 then we are to only print the overall stats in the 'site
* statistics' box otherwise we show the detailed stats for the plugin
*
* @param    int showsitestate   Flag to let us know which stats to get
* @return   string  returns formatted HTML to insert in stats page
*
*/
function plugin_showstats_nettools($showsitestats) 
{
    $retval='';
    return $retval;
}

/**
* This will put an option for the plugin in the command and control block on moderation.php
*
* Add the plugin name, icon and link to the command and control block in moderation.php
*
* @return   array   Array containing (plugin name, admin url, url of plugin icon)
*
*/
function plugin_cclabel_nettools() 
{
    global $_CONF;

    $retval = array();
    if ((SEC_hasRights('Tracert.view')) || (SEC_hasRights('Ping.view')) || (SEC_hasRights('NSLookup.view')) || (SEC_hasRights('Whois.view'))) {
        $retval = array('NetTools',$_CONF['site_url'] . '/nettools/nettools.php',$_CONF['site_url'] . '/nettools/net.gif');
    }
    return $retval;
}

/**
* Returns the administrative option for this plugin
*
* Adds the plugin to the Admin menu
*
* Universal Plugin:  Modify/Uncomment use it
*
* @return   array   Array containing (plugin name, plugin admin url, # of items in plugin or '')
*
*/
function plugin_getadminoption_nettools() 
{
    return array();
}

/**
* Returns the user option for this plugin
*
* Adds the plugin to the user menu
*
* Universal Plugin:  modify/uncomment use it
*
* @return   array   Array containing (plugin name, plugin user url, 0)
*
*/
function plugin_getuseroption_nettools() 
{
    global $_CONF;

    if ((SEC_hasRights('Tracert.view')) || (SEC_hasRights('Ping.view')) || (SEC_hasRights('NSLookup.view')) || (SEC_hasRights('Whois.view'))) {
        return array('NetTools', $_CONF['site_url'] . '/nettools/nettools.php');
    }
}

/**
* Automatic uninstall function for plugins
*
* @return   array
*
* This code is automatically uninstalling the plugin.
* It passes an array to the core code function that removes
* tables, groups, features and php blocks from the tables.
* Additionally, this code can perform special actions that cannot be
* foreseen by the core code (interactions with other plugins for example)
*
*/
function plugin_autouninstall_nettools ()
{
    $out = array (
        // give the name of the tables, without $_TABLES[]
        'tables' => array(),
        // give the full name of the group, as in the db
        'groups' => array('Nettools Admin'),
        // give the full name of the feature, as in the db
        'features' => array('Whois.view', 'NSLookup.view', 'Ping.view',
                            'Tracert.view'),
        // give the full name of the block, including 'phpblock_', etc
        'php_blocks' => array(),
        // only present in Nettools 1.0 and 2.0
        'vars'=> array('nettools_gid')
    );

    return $out;
}

/**
* Return the current version of code.
* Used in the Plugin Editor to show the registered version and code version
*/
function plugin_chkVersion_nettools()
{
    global $_CONF;

    require_once $_CONF['path'] . 'plugins/nettools/autoinstall.php';

    $inst_parms = plugin_autoinstall_nettools('nettools');

    return $inst_parms['info']['pi_version'];
}

/**
* Update the Nettools plugin
*
* @return   int     Number of message to display (true = generic success msg)
*
*/
function plugin_upgrade_nettools()
{
    global $_CONF, $_TABLES;

    $installed_version = DB_getItem($_TABLES['plugins'], 'pi_version',
                                    "pi_name = 'nettools'");
    $code_version = plugin_chkVersion_nettools();
    if ($installed_version == $code_version) {
        // nothing to do
        return true;
    }

    require_once $_CONF['path'] . 'plugins/nettools/autoinstall.php';

    $inst_parms = plugin_autoinstall_nettools('nettools');
    $pi_gl_version = $inst_parms['info']['pi_gl_version'];

    $current_version = $installed_version;
    $done = false;
    while (! $done) {
        switch ($current_version) {
        case '1.0':
        case '2.0':
        case '2.1.0':
            // no changes required - just update the version number
            $current_version = '2.1.1';
            break;

        default:
            $done = true;
            break;
        }
    }

    DB_query("UPDATE {$_TABLES['plugins']} SET pi_version = '$code_version', pi_gl_version = '$pi_gl_version' WHERE pi_name = 'nettools'");

    return true;
}

?>
