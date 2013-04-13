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
if($memento_id!=-1) $m_user = MySBUserHelper::getByID($memento->user_id);
else $m_user = $app->auth_user;
if($memento->group_id!=0) $m_group = MySBGroupHelper::getByID($memento->group_id);
else $m_group = null;

echo '
<div class="table_support">
<form action="?mod=dbmf3&amp;tpl=editcontact&amp;contact_id='.$contact->id.'" method="post">
<table style="width: 95%; font-size: 90%;"><tbody>
<tr>
    <td><b>'._G("DBMF_memento_owner").':</b></td>
    <td>';
echo '
        '.$m_user->lastname.' '.$m_user->firstname;
echo '
    </td>
</tr>
<tr>
    <td><b>'._G("DBMF_group_access").':</b></td>
    <td>
        <select name="memento_owner">';
echo '
            <option value="0">'._G("DBMF_memento_onlyowner").'</option>';
$mgroups = MySBDBMFGroupHelper::load();
foreach($mgroups as $mgroup) {
    if($app->auth_user->haveGroup($mgroup->id) and $mgroup->dbmf_priority!=0) echo '
        <option value="'.$mgroup->id.'" '.MySBUtil::form_isselected($memento->group_id,$mgroup->id).'>group "'.$mgroup->comments.'"</option>';
}
echo '
        </select>
        <input type="checkbox" name="memento_group_edition" '.MySBUtil::form_ischecked($memento->group_edition,1).'>'._G("DBMF_memento_groupcanedit").'
    </td>
</tr>
<tr>
    <td><b>'._G("DBMF_memento_type").':</b></td>
    <td>
        <select name="memento_type" onChange="hide(\'memtype0\');hide(\'memtype1\');show(this.options[this.selectedIndex].value);">
            <option value="memtype'.MYSB_DBMF_MEMENTO_TYPE_PUNCTUAL.'" '.MySBUtil::form_isselected($memento->type,MYSB_DBMF_MEMENTO_TYPE_PUNCTUAL).'>'._G("DBMF_memento_type_punctual").'</option>
            <option value="memtype'.MYSB_DBMF_MEMENTO_TYPE_MONTHOFYEAR.'" '.MySBUtil::form_isselected($memento->type,MYSB_DBMF_MEMENTO_TYPE_MONTHOFYEAR).'>'._G("DBMF_memento_type_monthofyear").'</option>
        </select>';
    if($memento->date_process!='') {
        $memento_process = new MySBDateTime($memento->date_process);
        echo '
        <small>'._G('DBMF_memento_process_last').': '.$memento_process->strEBY_l().'</small>';
    }
    echo '
    </td>
</tr>
<tr>
    <td><b>'._G("DBMF_memento_date").':</b></td>';
    if($memento->type==MYSB_DBMF_MEMENTO_TYPE_PUNCTUAL) {
        $style_t0 = '';
        $style_t1 = 'style="display: none;"';
    } elseif($memento->type==MYSB_DBMF_MEMENTO_TYPE_MONTHOFYEAR) {
        $style_t0 = 'style="display: none;"';
        $style_t1 = '';
    }
    echo '
    <td>
        <div id="memtype0" '.$style_t0.'>'.$memento_date->html_form('memento_date_',true).'</div>
        <div id="memtype1" '.$style_t1.'>
            <select name="memento_moy">
                <option value="1" '.MySBUtil::form_isselected($memento->monthofyear_memento,1).'>'._G('DBMF_memento_moy_1').'</option>
                <option value="2" '.MySBUtil::form_isselected($memento->monthofyear_memento,2).'>'._G('DBMF_memento_moy_2').'</option>
                <option value="3" '.MySBUtil::form_isselected($memento->monthofyear_memento,3).'>'._G('DBMF_memento_moy_3').'</option>
                <option value="4" '.MySBUtil::form_isselected($memento->monthofyear_memento,4).'>'._G('DBMF_memento_moy_4').'</option>
                <option value="5" '.MySBUtil::form_isselected($memento->monthofyear_memento,5).'>'._G('DBMF_memento_moy_5').'</option>
                <option value="6" '.MySBUtil::form_isselected($memento->monthofyear_memento,6).'>'._G('DBMF_memento_moy_6').'</option>
                <option value="7" '.MySBUtil::form_isselected($memento->monthofyear_memento,7).'>'._G('DBMF_memento_moy_7').'</option>
                <option value="8" '.MySBUtil::form_isselected($memento->monthofyear_memento,8).'>'._G('DBMF_memento_moy_8').'</option>
                <option value="9" '.MySBUtil::form_isselected($memento->monthofyear_memento,9).'>'._G('DBMF_memento_moy_9').'</option>
                <option value="10" '.MySBUtil::form_isselected($memento->monthofyear_memento,10).'>'._G('DBMF_memento_moy_10').'</option>
                <option value="11" '.MySBUtil::form_isselected($memento->monthofyear_memento,11).'>'._G('DBMF_memento_moy_11').'</option>
                <option value="12" '.MySBUtil::form_isselected($memento->monthofyear_memento,12).'>'._G('DBMF_memento_moy_12').'</option>
            </select>
        </div>
    </td>
</tr>';

MySBEditor::activate();
echo MySBEditor::initCode('simple');

echo '
<tr>
    <td><b>'._G("DBMF_memento_comments").':</b></td>
    <td><textarea name="memento_comments" cols="60" rows="3" class="mceEditor">'.$memento->comments.'</textarea></td>
</tr>
<tr>
    <td><b>'._G("DBMF_memento_comments2").':</b></td>
    <td><textarea name="memento_comments2" cols="60" rows="3" class="mceRichText">'.$memento->comments2.'</textarea></td>
</tr>
<tr>
    <td colspan="2" style="text-align: center;">';
    if($memento_id!=-1) echo '
        <input type="hidden" name="memento_modify" value="'.$memento_id.'">';
    else echo '
        <input type="hidden" name="memento_add" value="1">';
    echo '
        <input type="submit" value="'._G('DBMF_memento_edition_submit').'">
    </td>
</tr>
</tbody></table>
</form>';
    if($memento_id!=-1) echo '
<br>
<form   action="?mod=dbmf3&amp;tpl=editcontact&amp;contact_id='.$contact->id.'" method="post"
        OnSubmit="return mysb_confirm(\''.MySBUtil::str2strict(_G('DBMF_confirm_memento_delete')).'\')">
<table style="width: 95%;"><tbody>
<tr>
    <td colspan="2" style="text-align: center;">
        <input type="hidden" name="memento_delete" value="'.$memento_id.'">
        <input type="submit" value="'._G('DBMF_memento_edition_delete').'">
    </td>
</tr>
</tbody></table>
</form>';
    echo '
<br>
<table style="width: 95%;"><tbody>
<tr>
    <td colspan="2" style="text-align: center; padding: 2px;">
        <a href="?mod=dbmf3&amp;tpl=editcontact&amp;contact_id='.$contact->id.'" class="button">'._G("DBMF_memento_edition_return").'</a>
    </td>
</tr>
</tbody></table>
</div>
';




?>
