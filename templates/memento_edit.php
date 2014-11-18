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


if( isset($_GET['memento_id']) ) {
    $memento = new MySBDBMFMemento($_GET['memento_id']);
    $contact = new MySBDBMFContact($memento->contact_id);
    $memento_id = $memento->id;
} elseif( isset($_GET['contact_id']) ) {
    $contact = new MySBDBMFContact($_GET['contact_id']);
    $memento_id = -1;
    $memento = new MySBDBMFMemento(null,array("user_id"=>$contact->id));
}

echo '
<div class="overlaySize" 
    data-overheight=""
    data-overwidth="460"></div>

<div id="dbmfMemento">

<div class="overHead">';

if($memento_id!=-1) echo '
    <div style="float: left; margin-left: 0px;">
        <form action="index.php?mod=dbmf3&amp;tpl=contact_edit&amp;contact_id='.$contact->id.'" 
              method="post"
              class="overlayed"
              data-overconfirm="'.MySBUtil::str2strict(_G('DBMF_confirm_memento_delete')).'">
            <input type="hidden" name="memento_delete" value="'.$memento_id.'">
            <input src="images/icons/user-trash.png"
                   type="image"
                   alt="'._G('DBMF_memento_edition_delete').'"
                   title="'._G('DBMF_memento_edition_delete').'">
        </form>
    </div>';

echo '
<div style="float: left; margin-left: 5px;">
    <a  href="index.php?mod=dbmf3&amp;tpl=contact_edit&amp;contact_id='.$contact->id.'" 
        class="overlayed">
        <img    src="images/icons/text-editor.png" 
                alt="'._G("DBMF_memento_edition_return").'" 
                title="'._G('DBMF_contact_edition').': '.$contact->lastname.' '.$contact->firstname.' ('.$contact->id.')"
                style="width: 24px">
    </a>
</div>
'._G("DBMF_memento").'';
if($memento->date_process!='') {
    $memento_process = new MySBDateTime($memento->date_process);
    echo '
    <br><span class="help">'._G('DBMF_memento_process_last').': '.$memento_process->strEBY_l().'</span>';
}
echo '
</div>

<form   action="index.php?mod=dbmf3&amp;tpl=contact_edit&amp;contact_id='.$contact->id.'" 
        method="post"
        class="overlayed">

<div class="overBody">';

$memento_date = new MySBDateTime($memento->date_memento);
if($memento_id!=-1) $m_user = MySBUserHelper::getByID($memento->user_id);
else $m_user = $app->auth_user;

$area_id = 'editor_id_'.rand(1,999999);
$editor = new MySBEditor();

echo '
<div class="list_support" style="padding: 2px 4px;">';

if( $contact->mail!='' ) echo '
<div class="row" style="">
    <div style="float: right;"><a href="mailto:'.$contact->mail.'">
            <img src="images/icons/mail-unread.png" 
                 alt="'._G('DBMF_mailto').' '.$contact->id.'" 
                 title="'._G('DBMF_mailto').' '.$contact->lastname.' '.$contact->firstname.' ('.$contact->id.')"></a></div>
    <b>'.$contact->lastname.'<br><small>'.$contact->firstname.'</small></b>
</div>';

echo '
<div class="row" style="">
    <div class="right" style="">
    <select name="memento_category">';
$memcatgs = MySBDBMFMementoCatgHelper::loadAvailable();
foreach( $memcatgs as $memcatg ) {
    echo '
        <option value="'.$memcatg->id.'" '.MySBUtil::form_isselected($memento->memcatg_id,$memcatg->id).'>'.$memcatg->name.'</option>';
}
if($memento_id==-1) $onlyowner = '';
else $onlyowner = MySBUtil::form_isselected($memento->memcatg_id,0);
echo '
        <option value="0" '.$onlyowner.'>'._G("DBMF_memento_onlyowner").'</option>
    </select>';

if($memento_id==-1) $modifiable = ' checked="checked" ';
else $modifiable = MySBUtil::form_ischecked($memento->group_edition,1);
echo '
    <input type="checkbox" name="memento_group_edition" '.$modifiable.'>'._G("DBMF_memento_groupcanedit").'
    </div>
    <b>'._G("DBMF_memento_category").':</b>
</div>

<div class="row" style="">
    <div class="right" style=""><select name="memento_type" onChange="hide_instant(\'memtype0\');hide_instant(\'memtype1\');show(this.options[this.selectedIndex].value);">
            <option value="memtype'.MYSB_DBMF_MEMENTO_TYPE_PUNCTUAL.'" '.MySBUtil::form_isselected($memento->type,MYSB_DBMF_MEMENTO_TYPE_PUNCTUAL).'>'._G("DBMF_memento_type_punctual").'</option>
            <option value="memtype'.MYSB_DBMF_MEMENTO_TYPE_MONTHOFYEAR.'" '.MySBUtil::form_isselected($memento->type,MYSB_DBMF_MEMENTO_TYPE_MONTHOFYEAR).'>'._G("DBMF_memento_type_monthofyear").'</option>
        </select></div>
    <b>'._G("DBMF_memento_type").':</b>';

echo '
</div>';

    if($memento->type==MYSB_DBMF_MEMENTO_TYPE_PUNCTUAL) {
        $style_t0 = '';
        $style_t1 = 'style="display: none;"';
    } elseif($memento->type==MYSB_DBMF_MEMENTO_TYPE_MONTHOFYEAR) {
        $style_t0 = 'style="display: none;"';
        $style_t1 = '';
    }
    echo '<div class="row" style="">
    <div class="right" style="">
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
    </div>
    <b>'._G("DBMF_memento_date").':</b>
</div>

<div class="row" style="">
    <b>'._G("DBMF_memento_comments").':</b>
    <div><textarea name="memento_comments" cols="40" rows="3" class="mceEditor" id="'.$area_id.'">'.$memento->comments.'</textarea></div>
'.$editor->active($area_id).'
</div>

<div class="row" style="">
    <b>'._G("DBMF_memento_comments2").':</b>
    <div><textarea name="memento_comments2" cols="32" rows="3">'.$memento->comments2.'</textarea></div>
</div>

<div class="row" style="">
    <div class="right" style="">'.$m_user->lastname.' '.$m_user->firstname.'</div>
    <b>'._G("DBMF_memento_owner").':</b>
</div>

</div>

</div>

<div class="overFoot">';
if($memento_id!=-1) echo '
    <input type="hidden" name="memento_modify" value="'.$memento_id.'">';
else echo '
    <input type="hidden" name="memento_add" value="1">';
echo '
    <input type="submit" value="'._G('DBMF_memento_edition_submit').'">';
echo '
</div>

</form>

</div>';

?>
