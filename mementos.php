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

if(!MySBRoleHelper::checkAccess('dbmf_user')) return;

echo '
<h1>'._G('DBMF_mementos_summary').'</h1>
';

$mementos_p = MySBDBMFMementoHelper::loadByUserID($app->auth_user->id);

echo '
<h2>'._G('DBMF_mementos_all').' ('.count($mementos_p).')</h2>
<div class="table_support">
<table width="100%" style="font-size: 85%;"><tbody>
<tr class="title">
    <td width="120px">'._G("DBMF_memento_date").'</td>
    <td width="28px"></td>
    <td>'._G('DBMF_mementos_details').'</td>
    <td width="150px"></td>
</tr>';

foreach($mementos_p as $memento) {
    $contact = new MySBDBMFContact($memento->contact_id);
    $memento_date = new MySBDateTime($memento->date_memento);
    if($memento->isActive()) $Active='class="memento_active"';
    else $Active='';
    echo '
<tr '.$Active.'>
    <td><small>('.$memento->id.')</small> '.$memento->getDate().'</td>
    <td>
        <a  name="contact'.$anchor_nb.'"
            href="javascript:editwinopen(\'index_wom.php?mod=dbmf3&amp;tpl=editcontact&amp;contact_id='.$contact->id.'&amp;mode=screen\',\'contactinfos\')">
        <img src="modules/dbmf3/images/edit_icon24.png" alt="Edition '.$contact->id.'" title="'._G('DBMF_edit').' '.$contact->lastname.' '.$contact->firstname.' ('.$contact->id.')">
        </a>
    </td>
    <td>
        <b>'.$contact->lastname.'</b> '.$contact->firstname.'<br>
        '.$memento->comments.'
    </td>
    <td align="center">';
    if($Active!='') {
        echo '
        <form action="?mod=dbmf3&amp;tpl=mementos" method="post">
            <input type="hidden" name="memento_process" value="'.$memento->id.'">
            <input type="submit" value="'._G('DBMF_memento_process_submit').'" class="submit">
        </form>';
    } elseif($memento->date_process!='') {
        $memento_process = new MySBDateTime($memento->date_process);
        echo '
        <small>'._G('DBMF_memento_process_last').':<br>'.$memento_process->strEBY_l().'</small>';
    }
    echo '
    </td>
</tr>';
}

echo '
</tbody></table>
</div>';



?>
