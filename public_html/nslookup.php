<?php

// +---------------------------------------------------------------------------+
// | Network Tools Plugin 1.0 for Geeklog - The Ultimate Weblog                |
// +---------------------------------------------------------------------------+
// | nslookup.php                                                              |
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
//
// $Id: 

require_once('../lib-common.php');

// Only let Net users access this page
if ((!SEC_hasRights('NSLookup.view')) && (!SEC_inGroup('Root'))) {
    // Someone is trying to illegally access this page
    COM_errorLog("Someone has tried to illegally access the nslookup page.  User id: {$_USER['uid']}, Username: {$_USER['username']}, IP: $REMOTE_ADDR",1);
    $display = COM_siteHeader();
    $display .= COM_startBlock($LANG_NT00['access_denied']);
    $display .= $LANG_NT00['access_denied_msg'];
    $display .= COM_endBlock();
    $display .= COM_siteFooter(true);
    echo $display;
    exit;
}

/* 
* Main Function
*/

$display = COM_siteHeader();
$display .= COM_startBlock($LANG_NT00['nslookup']);

if(isset($_REQUEST['domain'])) {
	$domain = $_REQUEST['domain'];
} else {
    $domain = '';
}

if ($domain <> '') {
    $T = new Template($_CONF['path'] . 'plugins/nettools/templates');
    $T->set_file('page', 'net2.thtml');
    $T->set_block('page','Arow','ABlock');
    $T->set_var('img_src',$_CONF['site_url'] . '/nettools/net.gif');
    $T->set_var('site_url',$_CONF['site_url']);
    if (is_numeric(substr($domain,0,3))) {
        $hostname = gethostbyaddr($domain);
        $T->set_var('row_name', $LANG_NT00['name']);
        $T->set_var('row_data', $hostname);
        $T->parse('ABlock','Arow',true);
        $T->set_var('row_name', $LANG_NT00['ip']);
        $T->set_var('row_data', $domain);
        $T->parse('ABlock','Arow',true);
    } else {
        $ips = gethostbynamel($domain);
        $T->set_var('row_name',$LANG_NT00['name']);
        $T->set_var('row_data', $domain);
        $T->parse('ABlock','Arow',true);
        foreach($ips as $ip) {
            $T->set_var('row_name', $LANG_NT00['ip']);
            $T->set_var('row_data', $ip);
            $T->parse('ABlock','Arow',true);
        }
    }
    if (function_exists('checkdnsrr')) {
        $mxarray = array();
        getmxrr($domain,$mxarray);
        foreach($mxarray as $mx) {
            $T->set_var('row_name', $LANG_NT00['mx']);
            $T->set_var('row_data', $mx);
            $T->parse('ABlock','Arow',true);
        }
    }
} else {
    $T = new Template($_CONF['path'] . 'plugins/nettools/templates');
    $T->set_file('page', 'nettools.thtml');
    $T->set_block('page','frmquery','ABlock');
    $T->set_var('img_src',$_CONF['site_url'] . '/nettools/net.gif');
    $T->set_var('site_url',$_CONF['site_url']);
    $T->set_var('query_txt',$LANG_NT00['nslookup']);
    $T->set_var('query_page','nslookup.php');
    $T->set_var('input','domain');
    $T->set_var('in_type','hidden');
    $T->set_var('sub_name','submit');
    $T->set_var('sub_value',$LANG_NT00['submit']);
    $T->parse('ABlock','frmquery',true);
}
$T->set_var('form_output','');
$T->set_var('logo','');
$T->parse('output','page');
$display .= $T->finish($T->get_var('output'));
$display .= COM_endBlock();
$display .= COM_siteFooter();

echo $display;
?>