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


$memento = $app->tpl_dbmf_currentmemento;
$contact = $app->tpl_dbmf_currentcontact;
    if($memento->isActive()) $Active = true;
    else $Active = false;
    if( $Active and $memento->id!=-1 ) $memclass = 'mem_active';
    elseif( !$Active and $memento->date_process!='' ) $memclass = 'mem_processed';
    else $memclass='mem_processed';

if(isset($_POST['memento_add'])) {
    if(!isset($_SESSION["dbmf_memento_lastfilter"]))
        $_SESSION["dbmf_memento_lastfilter"] = '';
    echo '
<script>
loadItem( "mementos_results", "index.php?mod=dbmf3&inc=mementos_sort&filter='.$_SESSION["dbmf_memento_lastfilter"].'" );
</script>';
} elseif( isset($_POST['memento_modify']) or isset($_GET['memento_process']) or isset($_GET['memento_unprocess']) ) {
    echo '
<script>
loadItem( "memento'.$memento->id.'", "index.php?mod=dbmf3&inc=memento_display&memento_id='.$memento->id.'" );
</script>';
    if(!isset($_GET['overlay']))
      return;
} elseif( isset($_GET['memento_delete']) ) {
    echo '
<script>
slide_hide( "memento'.$_GET['memento_id'].'" );
desactiveOverlay();
</script>';
    return;
}

echo '
<div class="overlaySize1"
    data-overheight=""
    data-overwidth="460"></div>

<!--
<div id="dbmfMemento">
-->

<form   action="index.php?mod=dbmf3&amp;tpl=memento_edit&amp;memento_id='.$memento->id.'"
        method="post"
        class="overlayed">

<div class="modalContent">

<div class="modalTitle '.$memclass.'">';

if($memento->id!=-1) {
    echo '
  <a href="index.php?mod=dbmf3&amp;tpl=memento_edit&amp;memento_id='.$memento->id.'&amp;memento_delete='.$memento->id.'"
     class="hidelayed col-1 t-center btn-danger"
     data-overconfirm="'.MySBUtil::str2strict(_G('DBMF_confirm_memento_delete')).'"
     title="'._G('DBMF_memento_edition_delete').'">
    <img src="images/icons/user-trash.png" alt="">
  </a>';
echo '
  <a href="index.php?mod=dbmf3&amp;tpl=contact_edit&amp;contact_id='.$contact->id.'"
     class="overlayed col-1 t-center btn-secondary"
     title="'._G('DBMF_memento_edition_return').'">
    <img src="images/icons/text-editor.png" alt="">
  </a>';
    if($Active) {
        echo '
  <a href="index.php?mod=dbmf3&tpl=memento_edit&amp;memento_id='.$memento->id.'&amp;memento_process='.$memento->id.'"
     class="overlayed col-1 t-center btn-light"
     title="'._G('DBMF_memento_process_submit').'">
    <img src="images/icons/emblem-system.png" alt="">
  </a>';
    } elseif(!$Active and $memento->date_process!='') {
        echo '
  <a href="index.php?mod=dbmf3&tpl=memento_edit&amp;memento_id='.$memento->id.'&amp;memento_unprocess='.$memento->id.'"
     class="overlayed col-1 t-center btn-light"
     title="'._G('DBMF_memento_unprocess_submit').'">
    <img src="images/icons/emblem-system-stop.png" alt="">
  </a>';
    }
}

echo '
  <p class="col-auto '.$memclass.'" style="color: black;">
    '._G('DBMF_memento').'<br>
    <span class="help">-';
if( !$Active and $memento->date_process!='' ) {
    $memento_process = new MySBDateTime($memento->date_process);
    echo ''._G('DBMF_memento_process_last').': '.$memento_process->strEBY_l().'';
}
echo '</span>
  </p>
</div>

<div class="modalBody memento-list">';

$memento_date = new MySBDateTime($memento->date_memento);
if($memento->id!=-1) $m_user = MySBUserHelper::getByID($memento->user_id);
else $m_user = $app->auth_user;

$area_id = 'editor_id_'.rand(1,999999);
$editor = new MySBEditor();
echo $editor->init($area_id,"simple");

echo '
<div class="row">
<div class="content list">
<div class="row">
  <p class="col-11">
    <b>'.$contact->lastname.'</b><br><small>'.$contact->firstname.'</small>
  </p>';

if( $contact->mail!='' ) echo '
  <a class="col-1 btn btn-primary-light"
     href="mailto:'.$contact->mail.'"
     title="'._G('DBMF_mailto').' '.$contact->lastname.' '.$contact->firstname.' ('.$contact->id.')">
    <img src="images/icons/mail-unread.png"
         alt="">
  </a>';
else echo '
  <div class="col-1">
  </div>
';
echo '
</div>
</div>
</div>';

echo '
<div class="row label">
  <label class="col-sm-3" for="memento_category">
    <b>'._G('DBMF_memento_category').'</b>:
  </label>
  <div class="col-sm-4">
    <select name="memento_category" id="memento_category">';
$memcatgs = MySBDBMFMementoCatgHelper::loadAvailable();
foreach( $memcatgs as $memcatg ) {
    echo '
        <option value="'.$memcatg->id.'" '.MySBUtil::form_isselected($memento->memcatg_id,$memcatg->id).'>'.$memcatg->name.'</option>';
}
if($memento->id==-1) $onlyowner = '';
else $onlyowner = MySBUtil::form_isselected($memento->memcatg_id,0);
echo '
        <option value="0" '.$onlyowner.'>'._G('DBMF_memento_onlyowner').'</option>
    </select>';

if($memento->id==-1) $modifiable = ' checked="checked" ';
else $modifiable = MySBUtil::form_ischecked($memento->group_edition,1);
echo '
  </div>
  <div class="col-dm-5 t-right">
    <label class="w-auto" for="memento_group_edition">'._G('DBMF_memento_groupcanedit').'</label>
    <input type="checkbox" name="memento_group_edition" id="memento_group_edition" '.$modifiable.'>
  </div>
</div>

<div class="row label">
  <label class="col-sm-3" for="memento_type">
    <b>'._G('DBMF_memento_type').'</b>:
  </label>
  <div class="col-sm-9">
      <select name="memento_type" id="memento_type"
              onChange="hide_instant(\'memtype0\');hide_instant(\'memtype1\');show(this.options[this.selectedIndex].value);">
            <option value="memtype'.MYSB_DBMF_MEMENTO_TYPE_PUNCTUAL.'" '.MySBUtil::form_isselected($memento->type,MYSB_DBMF_MEMENTO_TYPE_PUNCTUAL).'>'._G('DBMF_memento_type_punctual').'</option>
            <option value="memtype'.MYSB_DBMF_MEMENTO_TYPE_MONTHOFYEAR.'" '.MySBUtil::form_isselected($memento->type,MYSB_DBMF_MEMENTO_TYPE_MONTHOFYEAR).'>'._G('DBMF_memento_type_monthofyear').'</option>
        </select>
  </div>
</div>';

    if($memento->type==MYSB_DBMF_MEMENTO_TYPE_PUNCTUAL) {
        $style_t0 = '';
        $style_t1 = 'style="display: none;"';
    } elseif($memento->type==MYSB_DBMF_MEMENTO_TYPE_MONTHOFYEAR) {
        $style_t0 = 'style="display: none;"';
        $style_t1 = '';
    }
    echo '
<div class="row label">
  <p class="col-sm-3">
    <b>'._G('DBMF_memento_date').'</b>:
  </p>
  <div class="col-sm-9">
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
</div>

<div class="row label" style="">
  <label class="col-12" for="'.$area_id.'">
    <b>'._G('DBMF_memento_comments').'</b>:
  </label>
  <div class="col-12">
    <textarea name="memento_comments" cols="40" rows="3"
              class="mceEditor" id="'.$area_id.'">'.$memento->comments.'</textarea>
'.$editor->active($area_id).'
  </div>
</div>

<div class="row label" style="">
  <label class="col-md-4" for="memento_comments2">
    <b>'._G('DBMF_memento_comments2').'</b>:
  </label>
  <div class="col-md-8">
    <textarea name="memento_comments2" id="memento_comments2"
              cols="32" rows="3">'.$memento->comments2.'</textarea>
  </div>
</div>

<div class="row" style="">
  <p class="col-md-4">
    <b>'._G('DBMF_memento_owner').'</b>:
  </p>
  <p class="col-md-8 t-right">
    '.$m_user->lastname.' '.$m_user->firstname.'
  </p>
</div>

</div>

</div>



<div class="modalFoot">
  <div class="col-12 t-center">';
if($memento->id!=-1) echo '
    <input type="hidden" name="memento_modify" value="1">';
else echo '
    <input type="hidden" name="memento_add" value="'.$contact->id.'">';
echo '
    <input type="submit" class="btn-primary"
           value="'._G('DBMF_memento_edition_submit').'">';
echo '
  </div>
</div>


</form>

';

?>
