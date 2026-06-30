<?php
/***************************************************************************
 *
 *   phpMySandBox/DBMF3 module - TRoman<abadcafe@free.fr> - 2026
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License', or
 *   ('at your option) any later version.
 *
 ***************************************************************************/

// No direct access.
defined('_MySBEXEC') or die;

global $app;

if (!MySBRoleHelper::checkAccess('dbmf_editor'))
    return;


$area_id = 'editor_id_' . rand(1, 999999);
$editor = new MySBEditor();
echo $editor->init($area_id, "simple");

echo '
<div class="overlaySize1"
    data-overheight=""
    data-overwidth="860"></div>

<div class="modalContent">';

if (isset($_POST['mementos_sel_submit'])) {
    echo '
<div class="modalTitle">
  <p class="col-auto" style="color1: black;">';
    if ($_POST["mementos_sel_action_type"] == "process")
        echo _G('DBMF_memento_actiontype_process') . '
    <input type="hidden" name="mementos_sel_action_type" value="process">';
    elseif ($_POST["mementos_sel_action_type"] == "unprocess")
        echo _G('DBMF_memento_actiontype_unprocess') . '
    <input type="hidden" name="mementos_sel_action_type" value="unprocess">';
    elseif ($_POST["mementos_sel_action_type"] == "delete")
        echo _G('DBMF_memento_actiontype_delete') . '
    <input type="hidden" name="mementos_sel_action_type" value="delete">';
    echo '
  </p>
</div>

<div class="modalBody mementos-sel" id="dbmfMemento">

<div class="row">
  <h2 class="col-12" style="text-align: center;">
   Mementos dones: ' . ($memento_curnb - 1) . '
  </h2>
</div>

</div>
</div>

';
    foreach ($mementos_sel as $memento) {
        // MySBDBMFMementoHelper::loadContactInfos($memento);
        // $memento->getContactInfos();
        if ($_POST["mementos_sel_action_type"] == "process" || $_POST["mementos_sel_action_type"] == "unprocess") {
            echo '
<script>
loadItem( "memento' . $memento->id . '", "index.php?mod=dbmf3&inc=memento_display&memento_id=' . $memento->id . '" );
</script>';
        } elseif ($_POST["mementos_sel_action_type"] == "delete") {
            echo '
<script>
slide_hide("memento' . $memento->id . '");
</script>';
            MySBDBMFMementoHelper::delete($memento->id);
        }
    }
    return;
}


echo '
<form   action="index.php?mod=dbmf3&amp;tpl=mementos_sel"
        method="post"
        class="overlayed">

<div class="modalTitle">
  <p class="col-auto" style="color1: black;">';
if ($_POST["memsel_action"] == "process")
    echo _G('DBMF_memento_actiontype_process') . '
    <input type="hidden" name="mementos_sel_action_type" value="process">';
elseif ($_POST["memsel_action"] == "unprocess")
    echo _G('DBMF_memento_actiontype_unprocess') . '
    <input type="hidden" name="mementos_sel_action_type" value="unprocess">';
elseif ($_POST["memsel_action"] == "delete")
    echo _G('DBMF_memento_actiontype_delete') . '
    <input type="hidden" name="mementos_sel_action_type" value="delete">';
echo '
  </p>
</div>

<div class="modalBody mementos-sel" id="dbmfMemento">
';
if ($_POST["memsel_action"] != "delete") {
    $memento_date = new MySBDateTime('');
    echo '
<div class="row label">
  <label class="col-sm-3" for="memento_type">
    <b>' . _G('DBMF_memento_type') . '</b>:
  </label>
  <div class="col-sm-9">
      <select name="memento_type" id="memento_type"
              onChange="slide_hide(\'memtype0\');
                        slide_hide(\'memtype1\');
                        slide_hide(\'memtype2\');
                        var value=this.options[this.selectedIndex].value;
                        setTimeout(function(){ slide_show(value); },500);">
            <option value="memtype' . MYSB_DBMF_MEMENTO_TYPE_PUNCTUAL . '">' . _G('DBMF_memento_type_punctual') . '</option>
            <option value="memtype' . MYSB_DBMF_MEMENTO_TYPE_MONTHOFYEAR . '">' . _G('DBMF_memento_type_monthofyear') . '</option>
            <option value="memtype2" selected="selected">Select a date (optional)</option>
        </select>
  </div>
</div>
<div class="row label">
  <p class="col-sm-3">
    <b>'._G('DBMF_memento_date').'</b>:
  </p>
  <div class="col-sm-9">
        <div id="memtype0" class="slide">'.$memento_date->html_form('memento_date_',true).'</div>
        <div id="memtype1" class="slide">
            <select name="memento_moy">
                <option value="1">'._G('DBMF_memento_moy_1').'</option>
                <option value="2">'._G('DBMF_memento_moy_2').'</option>
                <option value="3">'._G('DBMF_memento_moy_3').'</option>
                <option value="4">'._G('DBMF_memento_moy_4').'</option>
                <option value="5">'._G('DBMF_memento_moy_5').'</option>
                <option value="6">'._G('DBMF_memento_moy_6').'</option>
                <option value="7">'._G('DBMF_memento_moy_7').'</option>
                <option value="8">'._G('DBMF_memento_moy_8').'</option>
                <option value="9">'._G('DBMF_memento_moy_9').'</option>
                <option value="10">'._G('DBMF_memento_moy_10').'</option>
                <option value="11">'._G('DBMF_memento_moy_11').'</option>
                <option value="12">'._G('DBMF_memento_moy_12').'</option>
            </select>
        </div>
        <div id="memtype2" >-</div>
  </div>
</div>
<div class="row">
  <h2 class="col-12" style="color1: black;">
    ' . _G('DBMF_mementos_sel_textadd') . '
  </h2>
</div>
<div class="row">
  <div class="col-12">
    <textarea name="memento_comments" cols="40" rows="3"
              class="mceEditor" id="' . $area_id . '"></textarea>
' . $editor->active($area_id) . '
  </div>
</div>';
}
$memento_nb = 1;
foreach ($mementos_sel as $memento) {
    $contact = new MySBDBMFContact($memento->contact_id);
    echo '
<input type="hidden" name="mementos_sel_mem' . $memento_nb . '" value="' . $memento->id . '">
<div class="row">
  <div class="col-12">' . $memento_nb . '- Memento ' . $memento->id . ' (' . $contact->lastname . ')
  </div>
</div>';
    $memento_nb++;
}
echo '

</div>

<div class="modalFoot">
  <div class="col-12 t-center">
    <input type="hidden" name="mementos_sel_submit" value="1">
    <input type="submit" class="btn-primary"
           value="' . _G('DBMF_memento_actiontype_execute') . '">
</div>

</form

</div>

';
