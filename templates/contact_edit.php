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

if(isset($_POST['contact_delete'])) return;

echo '
<style>
#mysbModal.modal { max-width: 540px; }
</style>

<form   action="index.php?mod=dbmf3&amp;tpl=contact_edit&amp;contact_id='.$contact->id.'"
        method="post"
        class="overlayed">

<div class="modalContent">

<div class="modalTitle">
  <a class="hidelayed col-1 t-center btn-danger"
     href="index.php?mod=dbmf3&amp;inc=contact_del&amp;contact_delete='.$contact->id.'"
     data-overconfirm="'.MySBUtil::str2strict(sprintf(_G('DBMF_confirm_contact_delete'),$contact->lastname, $contact->firstname )).'"
     title="'.sprintf(_G('DBMF_contact_delete'),$contact->lastname, $contact->firstname ).'">
    <img src="images/icons/user-trash.png" alt="">
  </a>
  <p class="col-auto">
    '.$contact->lastname.'&nbsp;<br>
    <small>'.$contact->firstname.'&nbsp;</small>
  </p>
</div>
';

echo '

<div class="modalBody">

<div id="dbmfContact">';

include( _pathI('common_edition','dbmf3') );

$blocks = MySBDBMFBlockHelper::load();
foreach($blocks as $block) {
  $group_edit = MySBGroupHelper::getByID($block->groupedit_id);
  echo '
  <h2 class="border-top">
    '._G($block->lname).'
    <small><i>('.$group_edit->comments.')</i></small>
  </h2>';
  foreach($block->blockrefs as $blockref) {
    if($blockref->isActive()) {
      if(!$block->isEditable())
        $class_edit = 'background: #cccccc;';
      else
        $class_edit = '';
      $refname = $blockref->keyname;

      echo '
  <div class="row label" style1="'.$class_edit.'">';
      if( $block->isEditable() ) {
        if( $blockref->type==MYSB_VALUE_TYPE_DATE or
            $blockref->type==MYSB_VALUE_TYPE_DATETIME ) {
          $blockref->parameter = explode(',',$blockref->params);
        }
        echo $blockref->innerRow( 'blockref',
                                  $contact->$refname,
                                  true,
                                  _G($blockref->lname),
                                  $blockref->infos );
      } else
        echo $blockref->innerRow( 'blockref',
                                  $contact->$refname,
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

</div>
</div>
<div class="modalFoot">
  <div class="col-12 t-center">';

if(MySBRoleHelper::checkAccess('dbmf_editor',false)) echo '
    <input type="hidden" name="contact_edit" value="1">
    <input type="submit" class="btn-primary"
           value="'._G('DBMF_contact_edition_submit').'" class="action" style="width: 100%;">';

echo '
  </div>
</div>

</form>

';

if(isset($_POST['contact_edit'])) {
    echo '
<script>
loadItem("contact'.$contact->id.'","index.php?mod=dbmf3&inc=contact_display&id='.$contact->id.'");
</script>';
}
if(isset($_POST['memento_delete'])) { //TODO : pas utilise
    echo '
<script>
slide_hide("memento'.$_POST['memento_delete'].'");
</script>';
}
if(isset($_POST['memento_add']) or isset($_POST['memento_modify'])) {
    echo '
<script>
desactiveOverlay();
loadItem( "mementos_results", "index.php?mod=dbmf3&inc=mementos_sort&filter='.$_SESSION["dbmf_memento_lastfilter"].'" );
</script>';
}
if($_GET['contact_id']==-1) {
    echo '
<script>
slide_hide("newcontactselection");
slide_show("newcontactok");
</script>';
}
?>
