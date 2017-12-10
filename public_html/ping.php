<?php
// +---------------------------------------------------------------------------+
// | Network Tools Plugin 1.0 for Geeklog - The Ultimate Weblog                |
// +---------------------------------------------------------------------------+
// | ping.php                                                                  |
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

$max_count = 10; //maximum count for ping command

require_once '../lib-common.php';
require_once './config.php';
require_once './common.php';

// Only let Net users access this page
if ((!SEC_hasRights('Ping.view')) && (!SEC_inGroup('Root'))) {
    // Someone is trying to illegally access this page
    $uid = isset($_USER['uid']) ? $_USER['uid'] : 0;
    $userName = isset($_USER['username']) ? $_USER['username'] : '';
    COM_errorLog("Someone has tried to illegally access the Ping page.  User id: {$uid}, Username: {$userName}, IP: {$_SERVER['REMOTE_ADDR']}",1);
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

$content = COM_startBlock($LANG_NT00['ping']);

if (Geeklog\Input::post('submit') === $LANG_NT00['ping']) {
    $host = Geeklog\Input::post('host', '');
    $count = (int) Geeklog\Input::fPost('count', 0);

    // over count ?
    if ($count > $max_count) {
        $count = $max_count;
    }

    // try to make it safe
    $host = preg_replace ('/[^A-Za-z0-9._-]/', '', $host);
    $host = escapeshellcmd($host);
    $T = new Template($_CONF['path'] . 'plugins/nettools/templates');
    $T->set_file(array(
        'page' => 'net2.thtml',
        'row'  => 'row.thtml',
    ));
    $T->set_var('img_src', $_CONF['site_url'] . '/nettools/net.gif');
    $T->set_var('site_url', $_CONF['site_url']);
    $T->set_var('row_name', $LANG_NT00['ping_out']);
    $T->set_var('row_data', '');
    $T->parse('rows', 'row', true);

    //check target IP or domain
    if ($_NT_linux) {
        $result = shell_exec("ping -c$count -w$count $host");
        shell_exec("killall ping");// kill all ping processes in case there are some stalled ones
    } else {
        $result = shell_exec("ping -n $count $host");
    }

    $result = NETTOOLS_formatResult($result);
    $T->set_var('form_output', $result);
} else {
    $T = new Template($_CONF['path'] . 'plugins/nettools/templates');
    $T->set_file('page', 'nettools.thtml');
    $T->set_block('page', 'frmquery', 'ABlock');
    $T->set_var('img_src', $_CONF['site_url'] . '/nettools/net.gif');
    $T->set_var('site_url', $_CONF['site_url']);
    $T->set_var('query_txt', $LANG_NT00['ping']);
    $T->set_var('query_page', 'ping.php');
    $T->set_var('input', 'host');
    $T->set_var('in_type', 'text');
    $T->set_var('in2_msg', 'Count');
    $T->set_var('sub_name', 'submit');
    $T->set_var('sub_value', $LANG_NT00['ping']);
    $T->parse('ABlock', 'frmquery', true);
    $T->set_var('form_output', '');
}

$T->set_var('logo', '');
$T->parse('output', 'page');
$content .= $T->finish($T->get_var('output'))
    . COM_endBlock();
$display = COM_createHTMLDocument($content, array('what' => 'menu'));
COM_output($display);
