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

$contact = $app->tpl_currentcontact;
$date_creat = new MySBDateTime($contact->date_creat);
$date_modif = new MySBDateTime($contact->date_modif);


echo '
<tr class="title" >
    <td colspan="2">'._G("DBMF_contact_contact_infos").'</td>
</tr>
<tr>
    <td><b>'._G("DBMF_date_creat").':</b></td>
    <td>'.$date_creat->strAEBY_l().'</td>
</tr>
<tr>
    <td><b>'._G("DBMF_date_modif").':</b></td>
    <td>'.$date_modif->strAEBY_l().'</td>
</tr>
<tr>
    <td><b>'._G("DBMF_common_lastname").':</b></td>';
if(MySBRoleHelper::checkAccess('dbmf_editor',false)) echo '
    <td><input type="text" name="lastname" size="24" maxlength="64" value="'.$contact->lastname.'"></td>';
else echo '
    <td>'.$contact->lastname.'</td>';
echo '
</tr>
<tr>
    <td><b>'._G("DBMF_common_firstname").':</b></td>';
if(MySBRoleHelper::checkAccess('dbmf_editor',false)) echo '
    <td><input type="text" name="firstname" size="24" maxlength="64" value="'.$contact->firstname.'"></td>';
else echo '
    <td>'.$contact->firstname.'</td>';
echo '
</tr>
';

?>
