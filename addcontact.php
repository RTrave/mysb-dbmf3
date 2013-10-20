<?php 
/***************************************************************************
 *
 *   phpMySandBox/DBMF3 module - TRoman<abadcafe@free.fr> - 2012
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License', or
 *   ('at your option) any later version.
 *
***************************************************************************/

// No direct access.
defined('_MySBEXEC') or die;

global $app;

echo '
<h1>'._G('DBMF_addcontact').'</h1>
<p>';

if( isset($app->dbmf_req_wcheck) and $app->dbmf_req_wcheck!='' ) {

    while($data_wcheck = MySBDB::fetch_array($app->dbmf_req_wcheck)) {
        echo '
    '._G('DBMF_addcontact_editentry').$data_wcheck['id'].': 
    <a href="javascript:editwinopen(\'index_wom.php?mod=dbmf3&amp;tpl=editcontact&amp;contact_id='.$data_wcheck['id'].'\',\'contactinfos\')"><b>'.$data_wcheck['lastname'].'</b> '.$data_wcheck['firstname'].' &lt;'.$data_wcheck['mail'].'&gt;</a><br>';
    }
    $lastname = str_replace('"', '\'', $_POST['lastname']);
    $firstname = str_replace('"', '\'', $_POST['firstname']);
    $mail = str_replace('"', '\'', $_POST['mail']);
    echo '
    <br>
    <form action="index.php?mod=dbmf3&amp;tpl=editcontact&amp;contact_id=-1" method="post">
        <input type="hidden" name="lastname" value="'.$lastname.'">
        <input type="hidden" name="firstname" value="'.$firstname.'">
        <input type="hidden" name="mail" value="'.$mail.'">
        <input  type="submit" 
                value="'._G('DBMF_addcontact_newentry').': '.$lastname.' '.$firstname.' <'.$mail.'>"
                style="font-size: 130%;">
    </form>
</p>';

} else {

    echo '
<form action="index.php?mod=dbmf3&amp;tpl=addcontact" method="post">
<p>
<input type="hidden" name="add_status" value="1">
<b>'._G('DBMF_common_lastname').':</b>
<input type="text" name="lastname" size="24" maxlength="64" value=""><br>
<b>'._G('DBMF_common_firstname').':</b>
<input type="text" name="firstname" size="24" maxlength="64" value=""><br>
<b>'._G('DBMF_common_mail').':</b>
<input type="email" name="mail" size="24" maxlength="64" value=""><br>
<br>
<input type="submit" value="'._G('DBMF_addcontact_verify').'">
</p>
</form>';

}
?>
