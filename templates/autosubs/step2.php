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

// Process id
if(isset($_GET['pid']))
    $pid = $_GET['pid'];
else
    $pid = '';

echo '
<div id="dbmfAutosubs">';

if( !isset($_POST['autosubs_modifs']) )
  echo '
<form action="index.php?mod=dbmf3&amp;tpl=autosubs/step2"
      method="post">';

$classdisabled = '';
if( isset($_POST['autosubs_modifs']) )
  $classdisabled = ' disabled-elems';
echo '
<div class="col-md-8 col-unique">

<div class="content'.$classdisabled.'">

  <h1 class="bg-primary">'.MySBConfigHelper::Value('website_name').'</h1>
';
$autosubs_id = '';
$bordertop = '';

while($data_wcheck = MySBDB::fetch_array($app->dbmf_req_wcheck)) {

  $br_locked = false;
  $br_locked_txt = '';
  $brlock = MySBConfigHelper::Value('dbmf_autosubs_blockreflock','dbmf3');
  if( $brlock!='' and $data_wcheck[$brlock]!=0 ) {
    $br_locked = true;
    $br_locked_txt = ' <i>(read-only)</i>';
  } else {
    if($autosubs_id!='') {
      $autosubs_id .= ',';
      //$bordertop = 'border-top';
    }
    $autosubs_id .= $data_wcheck['id'];
  }
  $mails = str_replace(',','<br>',$data_wcheck['mail']);
  if( $br_locked or isset($_POST['autosubs_modifs']) )
    $isDisabled = 'disabled="disabled"';
  else $isDisabled = '';

  echo '
  <h2 class="border-top" id="contact'.$data_wcheck['id'].'">'.$mails.$br_locked_txt.'</h2>

  <div class="row label">
    <label class="col-sm-4" for="'.$data_wcheck['id'].'lastname">
      '._G('DBMF_common_lastname').'';
  if(MySBConfigHelper::Value('dbmf_ln_infos','dbmf3')!='')
    echo '<br>
      <span class="help">'.MySBConfigHelper::Value('dbmf_ln_infos','dbmf3').'</span>';
  echo '
    </label>
    <div class="col-sm-8">
      <input type="text" name="'.$data_wcheck['id'].'lastname"
             id="'.$data_wcheck['id'].'lastname" '.$isDisabled.'
             maxlength="64" value="'.$data_wcheck['lastname'].'">
    </div>
  </div>

  <div class="row label">
    <label class="col-sm-4" for="'.$data_wcheck['id'].'firstname">
      '._G('DBMF_common_firstname').'';
  if(MySBConfigHelper::Value('dbmf_fn_infos','dbmf3')!='')
    echo '<br>
      <span class="help">'.MySBConfigHelper::Value('dbmf_fn_infos','dbmf3').'</span>';
  echo '
    </label>
    <div class="col-sm-8">
      <input type="text" name="'.$data_wcheck['id'].'firstname"
             id="'.$data_wcheck['id'].'firstname" '.$isDisabled.'
             maxlength="64" value="'.$data_wcheck['firstname'].'">
    </div>
  </div>';

  $blockrefs = MySBDBMFBlockRefHelper::load();
  foreach( $blockrefs as $blockref ) {
    if($blockref->autosubs==1 and !$br_locked) {
      echo '
  <div class="row label">';
      if( !isset($_POST['autosubs_modifs']) )
        echo $blockref->innerRow( $data_wcheck['id'].'blockref',
                                  $data_wcheck[$blockref->keyname],
                                  true,
                                  _G($blockref->lname),
                                  $blockref->infos );
      else
        echo $blockref->innerRow( $data_wcheck['id'].'blockref',
                                  $data_wcheck[$blockref->keyname],
                                  true,
                                  _G($blockref->lname),
                                  $blockref->infos,
                                  true );
      echo '
  </div>';
    }
  }
}
echo '
</div>
</div>';

if( !isset($_POST['autosubs_modifs']) )
    echo '
<div class="actions">
  <div style="text-align1: center; float1: right;">
    <input type="hidden" name="autosubs_modifs" value="'.$autosubs_id.'">
    <input type="hidden" name="email" value="'.$_POST['email'.$pid].'">
    <input  type="submit" class="btn-success"
            value="'._G('DBMF_autosubs_modifsubmit').'">
  </div>
</div>
</form>';

if( isset($_POST['autosubs_modifs']) )
    echo '
<div class="actions">
  <form action="index.php?mod=dbmf3&amp;tpl=autosubs/step2"
        method="post">
  <div style="text-align: center; float: right;">
    <input type="hidden" name="new_email" value="'.$_POST['email'.$pid].'">
    <input type="submit" class="btn-primary"
           value="'._G('DBMF_autosubs_submitadd').'">
  </div>
  </form>
  <div style="text-align1: center; float1: right; width: 100%;">
    <a href="index.php?mod=dbmf3&amp;tpl=autosubs/step1"
       class="btn btn-success">'._G('DBMF_autosubs_restart').'
    </a>
  </div>
</div>';

echo '
</div>';

$app->view_menu(false);

?>
