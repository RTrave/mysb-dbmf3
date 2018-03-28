<?php
/***************************************************************************
 *
 *   phpMySandBox/RSVP module - TRoman<abadcafe@free.fr> - 2012
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License', or
 *   ('at your option) any later version.
 *
***************************************************************************/

// No direct access.
defined('_MySBEXEC') or die;

global $app;

if(!MySBRoleHelper::checkAccess('dbmf_autosubs')) return;

// Process id
if(isset($_GET['pid']))
    $pid = $_GET['pid'];
else
    $pid = '';

if( isset($_POST['autosubs_modifs']) ) {
    $today = getdate();
    $today_date = $today['year'].'-'.$today['mon'].'-'.$today['mday'].' '.
                  $today['hours'].':'.$today['minutes'].':'.$today['seconds'];
    $autosubs_ids = explode(',',$_POST['autosubs_modifs']);
    $blockrefs = MySBDBMFBlockRefHelper::load();
    foreach($autosubs_ids as $as_id) {

        $contact = new MySBDBMFContact($as_id);
        //echo $contact->id.'/';
        $contact_datas = array(
        'lastname' => $_POST[$as_id.'lastname'],
        'firstname' => $_POST[$as_id.'firstname'],
        'date_modif' => $today_date );
        foreach($blockrefs as $blockref) {
            if($blockref->autosubs==1)
                $contact_datas[$blockref->keyname] = $blockref->htmlProcessValue($as_id.'blockref');
        }
        $bradd = MySBConfigHelper::Value('dbmf_autosubs_blockref','dbmf3');
        if( $bradd!='' )
            $contact_datas[$bradd] = 1;
        $contact->update($contact_datas);
        $app->pushMessage(_G('DBMF_contact_modified'));
    }
}

if( isset($_POST['email'.$pid]) ) {
if( empty($_POST['email'.$pid]) ) {
    $app->pushMessage(_G('DBMF_addcontact_lastname_required'));
    echo '<html>
<head>
<title>Redir</title>
<meta http-equiv="refresh" content="0; URL=index.php?mod=dbmf3&amp;tpl=autosubs/step1">
</head>
<body>
</body>
</html>';
    die;
} else {
    $sql_wcheck = 'SELECT * from '.MySB_DBPREFIX.'dbmfcontacts '.
        'WHERE ';
    $sql_wcheck_cond = '';
    if( $_POST['email'.$pid]!='' ) {
        $sql_wcheck_cond .= 'mail RLIKE \''.MySBUtil::str2whereclause($_POST['email'.$pid]).'\' ';
    }
    $app->dbmf_req_wcheck = MySBDB::query($sql_wcheck.$sql_wcheck_cond.
        ' ORDER by id DESC;',
        "autosubs2_ctrl.php",
        true, "dbmf3");

    if(MySBDB::num_rows($app->dbmf_req_wcheck)==0) {
        $contact = MySBDBMFContactHelper::create('', '', $_POST['email'.$pid]);
        $app->dbmf_req_wcheck = MySBDB::query($sql_wcheck.$sql_wcheck_cond.
        ' ORDER by lastname;',
        "autosubs2_ctrl.php",
        true, "dbmf3");
    }
}
}

if( isset($_POST['new_email'] ) ) {
    $sql_wcheck = 'SELECT * from '.MySB_DBPREFIX.'dbmfcontacts '.
        'WHERE ';
    $sql_wcheck_cond = '';
    if( $_POST['new_email']!='' ) {
        $sql_wcheck_cond .= 'mail RLIKE \''.MySBUtil::str2whereclause($_POST['new_email']).'\' ';
    }
    $contact = MySBDBMFContactHelper::create('', '', $_POST['new_email']);
    $app->dbmf_req_wcheck = MySBDB::query($sql_wcheck.$sql_wcheck_cond.
        ' ORDER by lastname;',
        "autosubs2_ctrl.php",
        true, "dbmf3");
    $_POST['email'.$pid] = $_POST['new_email'];
}

include( _pathT('autosubs/step2','dbmf3') );

?>
