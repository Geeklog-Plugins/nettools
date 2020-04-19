<?php

// +---------------------------------------------------------------------------+
// | Network Tools Plugin 1.0 for Geeklog - The Ultimate Weblog                |
// | Network Tools Plugin 2.0 for Geeklog - Updated to phpWhois 4.1.2          |
// +---------------------------------------------------------------------------+
// | whois.php                                                                 |
// |                                                                           |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2003 by the following authors:                              |
// |                                                                           |
// | Authors: Tom Willett        - twillett@users.sourceforge.net              |
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

require_once '../lib-common.php';
include_once './whois.main.php';
include_once './whois.utils.php';

// Only let Net users access this page
if ((!SEC_hasRights('Whois.view')) && (!SEC_inGroup('Root'))) {
    // Someone is trying to illegally access this page
    $uid = isset($_USER['uid']) ? $_USER['uid'] : 0;
    $userName = isset($_USER['username']) ? $_USER['username'] : '';
    COM_errorLog("Someone has tried to illegally access the Whois page.  User id: {$uid}, Username: {$userName}, IP: {$_SERVER['REMOTE_ADDR']}",1);
    $content = COM_startBlock($LANG_NT00['access_denied'])
        . $LANG_NT00['access_denied_msg']
        . COM_endBlock();
    $display = COM_createHTMLDocument($content, array('what' => 'menu'));
    COM_output($display);
    exit;
}

/* 
* Main Function
*/

$content = COM_startBlock($LANG_NT00['whois']);
$domain = trim(Geeklog\Input::fGetOrPost('domain', ''));

if ($domain !== '') {
    $whois = new Whois();
    // uncomment the following line to get faster but less acurate results
    // $whois->deep_whois = false;
    // To use special whois servers (see README)	
    //$whois->UseServer('uk','whois.nic.uk:1043?{hname} {ip} {query}');
    //$whois->UseServer('au','whois-check.ausregistry.net.au');
	
    // uncomment the following line to add support for non ICANN tld's
    // $whois->non_icann = true;
	
    $result = $whois->Lookup($domain);
	
    $T = COM_newTemplate(CTL_plugin_templatePath('nettools'));
    $T->set_file('page', 'net2.thtml');
    $T->set_var('img_src',$_CONF['site_url'] . '/nettools/net.gif');
    $T->set_var('site_url',$_CONF['site_url']);
    if (!empty($result['rawdata'])) {
        $utils = new utils;
        $pre = $utils->showHTML($result);
    } else {
        if (isset($whois->Query['errstr'])) {
            $pre = implode("\n<br></br>", $whois->Query['errstr']);
        } else {
            $pre = 'Unexpected error';
        }
    }

    $T->set_var('form_output', $pre . 'something');
} else {
    $T = COM_newTemplate(CTL_plugin_templatePath('nettools'));
    $T->set_file('page', 'nettools.thtml');
    $T->set_block('page','frmquery','ABlock');
    $T->set_var('img_src',$_CONF['site_url'] . '/nettools/net.gif');
    $T->set_var('site_url',$_CONF['site_url']);
    $T->set_var('query_txt',$LANG_NT00['whois'] . ' domain = ' . $domain);
    $T->set_var('query_page','whois.php');
    $T->set_var('input','domain');
    $T->set_var('in_type','hidden');
    $T->set_var('sub_name','submit');
    $T->set_var('sub_value',$LANG_NT00['submit']);
    $T->parse('ABlock','frmquery',true);
}

$T->set_var('logo', '<img src="whois2-icon.gif">');
$T->parse('output', 'page');
$content .= $T->finish($T->get_var('output'))
    . COM_endBlock();
$display = COM_createHTMLDocument($content, array('what' => 'menu'));
COM_output($display);
