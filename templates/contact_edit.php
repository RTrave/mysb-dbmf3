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
<div class="overlaySize1"
    data-overheight=""
    data-overwidth="440"></div>


<!--
<div id="dbmfContact">
-->

<form   action="index.php?mod=dbmf3&amp;tpl=contact_edit&amp;contact_id='.$contact->id.'"
        method="post"
        class="overlayed">

<div class="modalContent">

<div class="modalTitle">
  <a class="hidelayed col-1 t-center btn-danger"
     href="index.php?mod=dbmf3&amp;tpl=contact_del&amp;contact_id='.$contact->id.'&amp;dbmf_contact_delete=1&amp;dbmf_request_reuse=1"
     data-overconfirm="'.MySBUtil::str2strict(sprintf(_G('DBMF_confirm_contact_delete'),$contact->lastname, $contact->firstname )).'"
     title="'.sprintf(_G('DBMF_contact_delete'),$contact->lastname, $contact->firstname ).'">
    <img src="images/icons/user-trash.png" alt="">
  </a>
  <p class="col-auto">
    '.$contact->lastname.'<br>
    <small>'.$contact->firstname.'</small>
  </p>
</div>
';

echo '

<div class="modalBody">

<div class="content1 list1">';

include( _pathI('common_edition','dbmf3') );

$blocks = MySBDBMFBlockHelper::load();
foreach($blocks as $block) {
  $group_edit = MySBGroupHelper::getByID($block->groupedit_id);
  echo '
  <h2>
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
  <div class="row label" style1="'.$class_edit.' text-align: right;">';
      if($block->isEditable())
        echo $blockref->innerRow('blockref',$contact->$refname,true,_G($blockref->lname),$blockref->infos);
      else {
        if( $blockref->getType()=='tel' or $blockref->getType()=='url' )
          echo $blockref->htmlFormNonEditable('blockref',$contact->$refname,'('.$contact->lastname.' '.$contact->firstname.')');
        else
          echo $blockref->htmlFormNonEditable('blockref',$contact->$refname);
      }
      echo '
  </div>';
/*
      if( $blockref->getType()=='text1' ) {
        echo '
  <div class="row label" style1="'.$class_edit.' text-align: right;">
    <label class="col-sm-4" style1="float: left;"><b>'._G($blockref->lname).':</b><br>';
        if( $blockref->infos!='' )
          echo '<span class="help">'.$blockref->infos.'</span>';
        echo '
    </label>
    <div class="col-sm-8" style1="display: inline-block; margin: 0px 0px 0px auto;">';
      } else {
        echo '
  <div class="row label" style="'.$class_edit.'">';
        if( $blockref->getType()!='text1' ) {
          echo '
    <label class="col-sm-4" for="blockref'.$blockref->keyname.'">
    <b>'._G($blockref->lname).':</b>';
          if( $blockref->infos!='' )
            echo '<br><span class="help">'.$blockref->infos.'</span>';
          echo '
  </label>
  <div class="col-sm-8">';
        }
        if($block->isEditable())
          echo $blockref->htmlForm('blockref',$contact->$refname,'('.$contact->lastname.' '.$contact->firstname.')');
        else {
          if( $blockref->getType()=='tel' or $blockref->getType()=='url' )
            echo $blockref->htmlFormNonEditable('blockref',$contact->$refname,'('.$contact->lastname.' '.$contact->firstname.')');
          else
            echo $blockref->htmlFormNonEditable('blockref',$contact->$refname);
        }
        echo '
    </div>';
      }
      echo '
  </div>';
*/
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
if(isset($_POST['memento_delete'])) {
    echo '
<script>
$("#memento'.$_POST['memento_delete'].'").fadeOut(1000,"swing");
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
$("#newcontactselection").fadeOut(500);
$("#newcontactselection").promise().done(function(){
    $("#newcontactok").fadeIn(500);
});
</script>';
}
?>
