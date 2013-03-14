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
    <td colspan="2" align="center">
        <small>
        <b>'._G("DBMF_date_creat").':</b>'.$date_creat->strAEBY_l().' / 
        <b>'._G("DBMF_date_modif").':</b>'.$date_modif->strAEBY_l().'
        </small>
    </td>
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
echo '
<tr class="title" >
    <td colspan="2">'._G("DBMF_contact_mementos_infos").'</td>
</tr>
<tr>
    <td colspan="2">
    <table width="100%"><tbody>
';

$mementos = MySBDBMFMementoHelper::load($contact->id);
foreach($mementos as $memento) {
    //$memento_date = new MySBDateTime($memento->date_memento);
    if($memento->isActive()) $Active = true;
    else $Active = false;
    if($Active) $memclass = 'class="mem_active"';
    elseif(!$Active and $memento->date_process!='') $memclass = 'class="mem_processed"';
    else $memclass='';
    $m_user = MySBUserHelper::getByID($memento->user_id);
    if($memento->group_id!=0) $m_group = MySBGroupHelper::getByID($memento->group_id);
    else $m_group = null;
    echo '
    <tr>
        <td width="130px" '.$memclass.'>
            <small>';
    if($memento->isEditable())
        echo '
            <a  href="?mod=dbmf3&tpl=editmemento&amp;memento_id='.$memento->id.'"
                OnClick="return mysb_confirm(\''.MySBUtil::str2strict(_G('DBMF_confirm_memento_edition')).'\')">
                <b>'.$memento->getDate().'</b></a>';
    else echo '
            '.$memento->getDate().'';
    echo '<br>
            <small><i>'.$m_user->login.'('.$m_group->name.')</i></small>
            </small>
        </td>
        <td><small>'.$memento->comments.'</small></td>
        <td width="180px"><small>'.$memento->comments2.'</small></td>
    </tr>
';
}

echo '
    <tr>
        <td colspan="3" align="center"><small>
            <a  href="?mod=dbmf3&tpl=editmemento&contact_id='.$contact->id.'" class="button"
                OnClick="return mysb_confirm(\''.MySBUtil::str2strict(_G('DBMF_confirm_memento_edition')).'\')">
                '._G("DBMF_contact_mementos_create").'</a></small>
        </td>
    </tr>
    </tbody></table>
    </td>
</tr>
';


?>
