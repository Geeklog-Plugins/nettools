<?php
//
// +---------------------------------------------------------------------------+
// | Nettools  Plugin 1.0 for Geeklog - The Ultimate Weblog                    |
// +---------------------------------------------------------------------------+
// | functions.inc                                                             |
// | This file does two things: 1) it implements the necessary Geeklog Plugin  |
// | API method and 2) implements all the common code needed by the {plugin}   |
// | PHP files.                                                                |
// |                                                                           |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2002 by the following authors:                              |
// |                                                                           |
// | Author:                                                                   |
// | Tom Willett                 -    twillett@users.sourceforge.net           |
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
//

/**
* Language file Include
*/
$langfile = $_CONF['path'] . 'plugins/nettools/language/' . $_CONF['language'] . '.php';

if (file_exists ($langfile)) 
{
    require_once ($langfile);
}
else 
{
    require_once ($_CONF['path'] . 'plugins/nettools/language/english.php');
}

/*
* Include nettools config file
*/
require_once ($_CONF['path'] . 'plugins/nettools/config.php');

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
* Removes the datastructures for this plugin from the Geeklog database.
* This routine will get called from the Plugin install program if user select De-Install or if Delete is used in the Plugin Editor.
* The Plugin Installer will also call this routine upon and install error to remove anything it has created.
* The Plugin installer will pass the optional parameter which will then double check that plugin has first been disabled. 
* 
* For this plugin, this routine will also remove the Block definition.
* 
* Returns True if all Plugin related data is removed without error
*
* @param    string   $installCheck     Defult is blank but if set, check if plugin is disabled first
* 
* @return   boolean True if successful false otherwise
*
*/  
function plugin_uninstall_nettools($installCheck='')
{
    global $LANG_NT00;

    // The nettoolss need to be changed to the name of your plugin
    //
    $pi_name='nettools';
    $FEATURES = array ( 'Whois.view', 'Tracert.view', 'Ping.view', 'NSLookup.view');
    
    // Check and see if plugin is still enabled - if so display warning and exit
    if ($installCheck != '' && DB_getItem($_TABLES['plugins'],'pi_enabled', 'pi_name = "' .$pi_name. '"')) {
	    COM_errorLog("Plugin is installed and enabled. Disable first if you want to de-install it",1);
		$display .= COM_startBlock($LANG_NT00['warning']);
		$display .= $LANG_NT00['enabled'];
		$display .= COM_endBlock();
		echo $display;
		return false;
		exit;
	}
    	
	// Ok to proceed and delete plugin

    // Unregister the plugin with Geeklog
    COM_errorLog('Attempting to unregister the nettools plugin from Geeklog',1);
    DB_query("DELETE FROM {$_TABLES['plugins']} WHERE pi_name = 'nettools'",1);

    // Remove Security for this plugin
    $grp_id = DB_getItem($_TABLES['vars'], 'value', "name = '{$pi_name}_gid'");
    
    COM_errorLog("Removing $pi_name Admin Group", 1);
    DB_query("DELETE FROM {$_TABLES['groups']} WHERE grp_id = $grp_id",1);
    DB_query("DELETE FROM {$_TABLES['vars']} WHERE name = '{$pi_name}_gid'");
    COM_errorLog("Removing root users from admin of $pi_name");
    DB_query("DELETE FROM {$_TABLES['group_assignments']} WHERE ug_main_grp_id = $grp_id",1);
    
    // Remove all the associated features -- access rights
    foreach ($FEATURES as $feature) {
        COM_errorLog("Removing $feature feature and rights to it",1);
        $feat_id = DB_getItem($_TABLES['features'], 'ft_id', "ft_name = '$feature'");
        DB_query("DELETE FROM {$_TABLES['access']} WHERE acc_ft_id = $feat_id",1);
        DB_query("DELETE FROM {$_TABLES['features']} WHERE ft_id = $feat_id",1);
    }
        
    COM_errorLog('...success',1);
    return true;
}

?>