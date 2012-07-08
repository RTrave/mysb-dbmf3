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

if($app->dbmf_req_wcheck!='') {

    while($data_wcheck = MySBDB::fetch_array($app->dbmf_req_wcheck)) {
        echo $data_wcheck[0].' <a href="modif.php?id='.$data_wcheck['0'].'&amp;mode=screen" ONCLICK="editwinopen(); return true;" TARGET="editwin"><b>'.$data_wcheck[1].'</b></a> '.$data_wcheck[2].'<br>';
    }
    echo '
<br>
<a href="index.php?mod=dbmf3&amp;tpl=editcontact&amp;contact_id=-1&amp;lastname='.$_POST['lastname'].'&amp;firstname='.$_POST['firstname'].'"><b>'._G('DBMF_addcontact_newentry').'</b></a>
(<b>'.$_POST['lastname'].'</b> '.$_POST['firstname'].')<br>
</p>';

} else {

    echo '
<form action="index.php?mod=dbmf3&amp;tpl=addcontact" method="post">
<p>
<input type="hidden" name="add_status" value="1">
<b>'._G('DBMF_lastname').':</b>
<input type="text" name="lastname" size="24" maxlength="64" value="'.$data_x['lastname'].'"><br>
<b>'._G('DBMF_firstname').':</b>
<input type="text" name="firstname" size="24" maxlength="64" value="'.$data_x['firstname'].'"><br>
<br>
<input type="submit" value="'._G('DBMF_addcontact_verify').'" class="submit">
</p>
</form>';

}
?>
