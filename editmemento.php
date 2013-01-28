<?php 
/***************************************************************************
 *
 *   phpMySandBox/DBMF3 module - TRoman<abadcafe@free.fr> - 2013
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License', or
 *   ('at your option) any later version.
 *
***************************************************************************/

// No direct access.
defined('_MySBEXEC') or die;

global $app;

if( !MySBRoleHelper::checkAccess('dbmf_editor') ) return;

if(isset($_GET['memento_id'])) {
    $memento = new MySBDBMFMemento($_GET['memento_id']);
    $contact = new MySBDBMFContact($memento->contact_id);
    $memento_id = $memento->id;
} elseif(isset($_GET['contact_id'])) {
    $contact = new MySBDBMFContact($_GET['contact_id']);
    $memento_id = -1;
}

echo '
<h1>'._G("DBMF_memento_edition").'</h1>
<h2>'.$contact->lastname.' '.$contact->firstname.' ('.$contact->id.')</h2>';

$memento_date = new MySBDateTime($memento->date_memento);
    echo '
<div class="table_support" align="center">
<form action="?mod=dbmf3&amp;tpl=editcontact&amp;contact_id='.$contact->id.'" method="post">
<table width="95%"><tbody>
<tr>
    <td><b>'._G("DBMF_memento_owner").':</b></td>
    <td>'.$app->auth_user->login.'<input type="hidden" name="memento_user" value="'.$app->auth_user->id.'"></td>
</tr>
<tr>
    <td><b>'._G("DBMF_memento_type").':</b></td>
    <td>'.MYSB_DBMF_MEMENTO_TYPE_UNIQUE.'<input type="hidden" name="memento_type" value="'.MYSB_DBMF_MEMENTO_TYPE_UNIQUE.'"></td>
</tr>
<tr>
    <td><b>'._G("DBMF_memento_date").':</b></td>
    <td>'.$memento_date->html_form('memento_date_',true).'</td>
</tr>
<tr>
    <td><b>'._G("DBMF_memento_comments").':</b></td>
    <td><textarea name="memento_comments" cols="60" rows="3">'.$memento->comments.'</textarea></td>
</tr>
<tr>
    <td colspan="2" align="center">';
    if($memento_id!=-1) echo '
        <input type="hidden" name="memento_modify" value="'.$memento_id.'">';
    else echo '
        <input type="hidden" name="memento_add" value="1">';
    echo '
        <input type="submit" value="'._G('DBMF_memento_edition_submit').'" class="submit">
    </td>
</tr>
</tbody></table>
</form>';
    if($memento_id!=-1) echo '
<br>
<form   action="?mod=dbmf3&amp;tpl=editcontact&amp;contact_id='.$contact->id.'" method="post"
        OnSubmit="return mysb_confirm(\''.MySBUtil::str2strict(_G('DBMF_confirm_memento_delete')).'\')">
<table width="95%"><tbody>
<tr>
    <td colspan="2" align="center">
        <input type="hidden" name="memento_delete" value="'.$memento_id.'">
        <input type="submit" value="'._G('DBMF_memento_edition_delete').'" class="submit">
    </td>
</tr>
</tbody></table>
</form>';
    echo '
<br>
<table width="95%"><tbody>
<tr>
    <td colspan="2" align="center">
        <small><a href="?mod=dbmf3&tpl=editcontact&amp;contact_id='.$contact->id.'" class="button">'._G("DBMF_memento_edition_return").'</a></small>
    </td>
</tr>
</tbody></table>
</div>
<br>
';




?>
