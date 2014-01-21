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


echo MySBEditor::init("simple");

echo '
<h1>'._G('DBMF_addcontact').'</h1>
<p>
<div id="newcontactselection">';

if( isset($app->dbmf_req_wcheck) and $app->dbmf_req_wcheck!='' ) {

    while($data_wcheck = MySBDB::fetch_array($app->dbmf_req_wcheck)) {
        echo '
    <div id="contact'.$data_wcheck['id'].'">
    '._G('DBMF_addcontact_editentry').$data_wcheck['id'].': 
    <a href="index.php?mod=dbmf3&amp;tpl=contact_edit&amp;contact_id='.$data_wcheck['id'].'"
       class="overlayed">
        <b>'.$data_wcheck['lastname'].'</b> '.$data_wcheck['firstname'].' &lt;'.$data_wcheck['mail'].'&gt;</a>
    </div>';
    }
    $lastname = str_replace('"', '\'', $_POST['lastname']);
    $firstname = str_replace('"', '\'', $_POST['firstname']);
    $mail = str_replace('"', '\'', $_POST['mail']);
    echo '
    <br>
    <form action="index.php?mod=dbmf3&amp;tpl=contact_edit&amp;contact_id=-1" 
          method="post"
          class="overlayed">
        <input type="hidden" name="lastname" value="'.$lastname.'">
        <input type="hidden" name="firstname" value="'.$firstname.'">
        <input type="hidden" name="mail" value="'.$mail.'">
        <input  type="submit" 
                value="'._G('DBMF_addcontact_newentry').': '.$lastname.' '.$firstname.' <'.$mail.'>"
                style="font-size: 130%;">
    </form>
</div>
<div id="newcontactok" style="display: none;">
    '._G('DBMF_addcontact_newentry').' OK!
</div>
</p>';

} else {

    echo '
<form action="index.php?mod=dbmf3&amp;tpl=contact_add" method="post">
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
</form>
</div>';

}
?>
