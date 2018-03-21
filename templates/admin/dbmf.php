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

$httpbase = 'index.php?tpl=admin/admin&amp;page=admin/dbmf&amp;module=dbmf3';
$dbmf_groups = MySBDBMFGroupHelper::load();



if(isset($_POST['dbmf_editexport'])) {

  $export = MySBDBMFExportHelper::getByID($_POST['dbmf_editexport']);
  echo '
<div class="content">

  <h1>'._G('DBMF_admin_exports').'</h1>

  <form action="'.$httpbase.'" method="post">
  <h2>'._G('DBMF_editexport').'</h2>
  <div class="row label">
    <label class="col-sm-6" for="export_name">
      <b>'._G('DBMF_export_name').'</b>
    </label>
    <div class="col-sm-6">
      <input type="text" name="export_name" id="export_name"
             value="'.$export->name.'">
    </div>
  </div>
  <div class="row label">
    <label class="col-sm-6" for="export_comments">
      <b>'._G('DBMF_export_comments').'</b>
    </label>
    <div class="col-sm-6">
      <input type="text" name="export_comments" id="export_comments"
             value="'.$export->comments.'">
    </div>
  </div>
  <div class="row">
    <p class="col-sm-6">
      <b>'._G('DBMF_export_type').'</b>
    </p>
    <p class="col-sm-6">';
    $pluginsExport = MySBPluginHelper::loadByType('DBMFExport');
    foreach($pluginsExport as $plugin)
        if($plugin->value0==$export->type) break;
    echo $plugin->value1.'
    </p>
  </div>
  <div class="row label">
    <label class="col-sm-6" for="export_groupid">
      <b>'._G('DBMF_export_group').'</b>
    </label>
    <div class="col-sm-6">
      <select name="export_groupid" id="export_groupid">';
    foreach($dbmf_groups as $group) {
        echo '
        <option value="'.$group->id.'" '.MySBUtil::form_isselected($group->id,$export->group_id).'>'.$group->comments.'</option>';
    }
    echo '
      </select>
    </div>
  </div>

  <h2 class="border-top">'._G('DBMF_export_config').'</h2>
  <div class="row">
    '.$export->htmlConfigForm().'
  </div>
  <div class="row">
    <div class="col-sm-3"></div>
    <div class="col-sm-6">
      <input type="hidden" name="dbmf_editexport_process" value="'.$export->id.'">
      <input type="submit" class="btn-primary"
             value="'._G('DBMF_editexport_submit').'">
    </div>
    <div class="col-sm-3"></div>
</div>

</form>
</div>';
  return;
}


echo '
<div class="content">
  <h1>'._G('DBMF_admin_dbmf').'</h1>';

if(isset($_POST['dbmf_editexport'])) {
    echo '
<div id="hide_1"><h2><a onClick="hide(\'hide_1\');show(\'hide_2\');"><u>'._G('DBMF_admin_groups').'</u></a></h2></div>
<div id="hide_2" style="display: none;">';
} else {
    echo '
<div>';
}
echo '
  <h2>'._G('DBMF_admin_groups').'</h2>

  <h3>'._G('DBMF_listgroups').'</h3>
</div>';
foreach($dbmf_groups as $group) {
    if($group->dbmf_priority=='' or $group->dbmf_priority==0)
        $style_group = ' style="background1: #bbbbbb;"';
    else
        $style_group = ' style="font-weight: bold;"';
    echo '
  <div class="row" '.$style_group.'>
    <div class="col-sm-4">
      '.$group->comments.'<br>
      <span class="help">'.$group->name.'</span>
    </div>
    <form action="'.$httpbase.'" method="post">
    <div class="col-sm-4">
      <select name="dbmf_priority">
        <option value="">'._G('DBMF_groups_unused').'</option>';
    $i = 1;
    while($i<=5) {
        echo '
        <option value="'.$i.'" '.MySBUtil::form_isselected($i,$group->dbmf_priority).'>'._G('DBMF_group_priority').' '.$i.'</option>';
        $i++;
    }
    echo '
      </select>
    </div>
    <div class="col-sm-4">
      <input type="hidden" name="group_id" value="'.$group->id.'">
      <input type="submit" class="btn-primary"
             value="'._G('DBMF_group_modify').'">
    </div>
    </form>

  </div>';
}

echo '
</div>';

echo '
<div class="content">
  <h1>'._G('DBMF_admin_exports').'</h1>

  <h2>'._G('DBMF_listexports').'</h2>';

$exports = MySBDBMFExportHelper::load();
foreach($exports as $export) {
    $group = MySBGroupHelper::getByID($export->group_id);
    echo '
  <div class="content list">
  <div class="row">
    <form class="col-1 btn btn-primary-light"
          action="'.$httpbase.'" method="post"
          title="'._G('DBMF_editexport').' '.$export->name.'">
      <input type="hidden" name="dbmf_editexport" value="'.$export->id.'">
      <input src="images/icons/text-editor.png"
             type="image" alt="">
    </form>
    <p class="col-sm-7">
      '.$export->name.'<br>
      <span class="help">'.$export->comments.'</span>
    </p>
    <p class="col-sm-7 t-right">
      '.$export->type.'<br>
      <span class="help">'.$group->comments.'</span>
    </p>
  </div>
  </div>
<!--
  <div class="row">
    <div class="right" style="text-align: right;">'.$export->type.'<br><span class="help">'.$group->comments.'</span></div>
    <div style="float: left;">
    <form action="'.$httpbase.'" method="post">
        <input type="hidden" name="dbmf_editexport" value="'.$export->id.'">
        <input src="images/icons/text-editor.png"
               type="image"
               alt="'._G('DBMF_editexport').' '.$export->name.'"
               title="'._G('DBMF_editexport').' '.$export->name.'">
    </form>
    </div>
    '.$export->name.'<br>
    <span class="help">'.$export->comments.'</span>
  </div>
-->';
}

echo '
<form action="'.$httpbase.'" method="post">

  <h2 class="border-top">'._G('DBMF_addexports').'</h2>

  <div class="row label">
    <label class="col-sm-4" for="export_name">
      <b>'._G('DBMF_export_name').'</b>
    </label>
    <div class="col-sm-8">
      <input type="text" name="export_name" id="export_name"
             value="">
    </div>
  </div>

  <div class="row label">
    <label class="col-sm-4" for="export_comments">
      <b>'._G('DBMF_export_comments').'</b>
    </label>
    <div class="col-sm-8">
      <input type="text" name="export_comments" id="export_comments"
             value="">
    </div>
  </div>

  <div class="row label">
    <label class="col-sm-4" for="export_type">
      <b>'._G('DBMF_export_type').'</b>
    </label>
    <div class="col-sm-8">
      <select name="export_type" id="export_type">';
$pluginsExport = MySBPluginHelper::loadByType('DBMFExport');
foreach($pluginsExport as $plugin)
    echo '
        <option value="'.$plugin->value0.'">'.$plugin->value1.'</option>';
echo '
      </select>
    </div>
  </div>

  <div class="row" label>
    <label class="col-sm-4" for="export_type">
      <b>'._G('DBMF_export_group').'</b>
    </label>
    <div class="col-sm-8">
      <select name="export_groupid">';
foreach($dbmf_groups as $group)
    echo '
        <option value="'.$group->id.'">'.$group->comments.'</option>';
echo '
      </select>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-3"></div>
    <div class="col-sm-6">
      <input type="hidden" name="dbmf_addexport" value="1">
      <input type="submit" class="btn-primary"
             value="'._G('DBMF_addexports').'">
    </div>
    <div class="col-sm-3"></div>
  </div>

</form>
</div>';

echo '
<div class="content">
  <h1 id="orphans">'._G('DBMF_orphans').'</h1>';

if( !isset($_POST['dbmf_orphans']) ) echo '
<form action="'.$httpbase.'#orphans" method="post">
  <div class="row">
    <div class="col-sm-3"></div>
    <div class="col-sm-6">
      <input type="hidden" name="dbmf_orphans" value="1">
      <input type="submit" class="btn-primary"
             value="'._G('DBMF_orphans_search').'">
    </div>
    <div class="col-sm-3"></div>
  </div>
</form>';
else {
    $editor = new MySBEditor();
    echo '
'.$editor->init("simple").'
<div id="contacts_results">';
    //$app->tpl_dbmf_searchresult = $app->dbmf_search_result;
    include( _pathI('contacts_sort_ctrl','dbmf3') );
    echo '
</div>';
}

echo '
</div>

<div class="content">
  <h1 id="autosubs">'._G('DBMF_autosubs_config').'</h1>

<form action="'.$httpbase.'#autosubs" method="post">
  <div class="row checkbox-list">';

$blockrefs = MySBDBMFBlockRefHelper::load();
foreach( $blockrefs as $blockref ) {
    echo '
    <label for="'.$blockref->keyname.'">
      <input type="checkbox" name="'.$blockref->keyname.'"
             "'.MySBUtil::form_ischecked($blockref->autosubs,"1").'" id="'.$blockref->keyname.'">
      <i>'._G($blockref->lname).'</i>
    </label>';
}
echo '
  </div>
  <div style="row">
    <div class="col-sm-3"></div>
    <div class="col-sm-6">
      <input type="hidden" name="dbmf_autosubs" value="1">
      <input type="submit" class="btn-primary"
             value="'._G('DBMF_autosubs_configsubmit').'">
    </div>
    <div class="col-sm-3"></div>
  </div>
</form>

</div>';

?>
