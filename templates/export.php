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


if( isset($_POST['dbmf_export_process']) ) {
  echo '
<div class="col-md-10 col-unique">
  <div class="content list" id="search">
    <h1>'._G('DBMF_search_results').'</h1>';
  echo $app->dbmf_export_plugin->htmlResultOutput();
  echo '
  </div>
</div>';
    return;
}

echo '
<div class="col-md-8 col-unique">
<form enctype="multipart/form-data" action="index.php?mod=dbmf3&amp;tpl=export" method="post">
<div class="content">

  <h1>'._G('DBMF_export').'</h1>

  <div class="row label">

    <label class="col-sm-8" for="export_plug">
      '._G('DBMF_export_select_type').'
    </label>
    <div class="col-sm-4">';
echo '
      <select name="export_plug" id="export_plug"
              onChange="';
$exports = MySBDBMFExportHelper::load();
foreach($exports as $export)
    echo 'slide_hide(\'export_plug'.$export->id.'\');';
echo 'slide_show(this.options[this.selectedIndex].value);">';
foreach($exports as $export)
    echo '
        <option value="export_plug'.$export->id.'">'._G($export->name).'</option>';

echo '
      </select>
    </div>
  </div>
  <div class="row">
    <div class="col-md-3"></div>
    <a id="params_show" class="col-md-6 btn btn-primary-light t-left"
       onClick="slide_toggle(\'params\');">
      <img src="images/icons/go-down.png" alt="go-down"
           style="position: absolute; right: 0; top: 0;">'._G('DBMF_export_showparams').'
    </a>
    <div class="col-md-3"></div>
  </div>
  <div id="params" class="row slide" style1="display: none;">';

$hide_flag = 'class="slide slide-toggled"';
foreach($exports as $export) {
  echo '
  <div id="export_plug'.$export->id.'" '.$hide_flag.' >
    <h2>'._G('DBMF_export_param_for').' '._G($export->name).' ('._G($export->comments).')</h2>
    '.$export->htmlParamForm().'

  </div>';
  if($hide_flag=='class="slide slide-toggled"')
    $hide_flag = 'class="slide" style1="display: none;"';
}

echo '
  </div>
</div>



<div class="content" id="rsvpCriteria">

  <h1>'._G('DBMF_export_blockscriteria').'</h1>';

$blocks = MySBDBMFBlockHelper::load();
$blockn_flag = 0;
foreach($blocks as $block) {
  $group_edit = MySBGroupHelper::getByID($block->groupedit_id);
  if($blockn_flag==0) $blockn_flag = 1;
  else {
    echo '
  <div class="row border-top border-bottom" style1="text-align: center; background: transparent; border: 0px; padding-bottom: 0px;">
    <div class="col-sm-4"></div>
    <div class="col-sm-4">
      <select name="block_andorflag_'.$block->id.'">
        <option value="or">OR</option>
        <option value="and">AND</option>
      </select>
    </div>
    <div class="col-sm-4"></div>
  </div>';
  }
  echo '
  <div class="row btn-primary-light">
    <div class="col-1 t-left">
      '.$block->htmlFormWhereClause('b').'
    </div>
    <a class="col-11 btn t-left" href="javascript:void(0)"
       onClick="slide_toggle(\'block_select_'.$block->id.'\');">
      <b>'._G($block->lname).'</b>
      <small><i>('.$group_edit->comments.')</i></small>
      <img src="images/icons/go-down.png" alt="go-down"
           style="position: absolute; right: 0; top: 0;">
    </a>
  </div>
  <div id="block_select_'.$block->id.'" class="slide" style="display1: none; width: 100%;">
    <div class="row" style1="text-align: center;">
      <p class="col-12 t-center">
        '._G('DBMF_request_blockref_and_or').'
        <input type="radio" name="blockref_andorflag_'.$block->id.'" value="or" checked>OR
        <input type="radio" name="blockref_andorflag_'.$block->id.'" value="and">AND
      </p>
    </div>';
  foreach($block->blockrefs as $blockref) {
    if($blockref->isActive()) {
      echo '
    <div class="row label">
      '.$blockref->innerRowWhereClause('br',_G($blockref->lname)).'
    </div>';
    }
  }
  echo '
  </div>';
}

echo '
  <div class="row border-top">
    <div class="col-sm-3"></div>
    <div class="col-sm-6">
      <input type="hidden" name="dbmf_export_process" value="1">
      <input type="submit" class="btn-primary"
             value="'._G('DBMF_search_submit').'">
    </div>
    <div class="col-sm-3"></div>
  </div>
</div>
</form>
</div>';

?>
