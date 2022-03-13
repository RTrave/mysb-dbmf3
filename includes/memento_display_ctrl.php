<?php
/***************************************************************************
 *
 *   phpMySandBox/DBMF3 module - TRoman<roman.trave@abadcafe.org> - 2022
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License', or
 *   ('at your option) any later version.
 *
***************************************************************************/

// No direct access.
defined('_MySBEXEC') or die;

global $app;


if( !MySBRoleHelper::checkAccess('dbmf_user') ) return;

$crit_infos = '';
if( isset($_GET['memento_id']) ) {
    $memento = new MySBDBMFMemento($_GET['memento_id']);
    $contact = new MySBDBMFContact($memento->contact_id);
} else {
    $memento = $app->tpl_dbmf_currentmemento;
    if(isset($memento->crit_infos))
      $crit_infos = '<span style="font-size: 80%;"><b>'.$memento->crit_infos.'</b></span><br>';
    $contact = MySBDBMFMementoHelper::getContactInfos($memento->contact_id);
}


    $m_user = MySBUserHelper::getByID($memento->user_id);
    if($memento->memcatg_id!=0) $memcatg = MySBDBMFMementoCatgHelper::getByID($memento->memcatg_id);
    else $memcatg = null;
    if($memento->isActive()) $Active = true;
    else $Active = false;

    if($Active) 
      $memclass = 'mem_active';
    elseif(!$Active and $memento->date_process!='' and $memento->type!=1) 
      $memclass = 'mem_processed';
    else 
      $memclass='mem_inactive';

    if($memcatg!=null) $m_catgname = $memcatg->name;
    else $m_catgname = '<i>'.$m_user->login.'</i>';

if(isset($_GET['memento_id'])) {
  echo '
    <div class="content list slide slide-toggled" id="memento'.$memento->id.'">';
}

?>

<div class="row <?= $memclass ?>" style="border-spacing: 0;">

<?php if( $Active ) { ?>
<a class="hidelayed col-1 t-center btn-light"
   href="index.php?mod=dbmf3&tpl=memento_edit&amp;memento_id=<?= $memento->id ?>&amp;memento_process=1"
   title="<?= _G('DBMF_memento_process_submit') ?>">
  <img src="images/icons/emblem-system.png" alt="">
</a>
<?php } elseif(!$Active and $memento->date_process!='') { ?>
<a class="hidelayed col-1 t-center btn-light"
   href="index.php?mod=dbmf3&tpl=memento_edit&amp;memento_id=<?= $memento->id ?>&amp;memento_unprocess=1"
   title="<?= _G('DBMF_memento_unprocess_submit') ?>">
  <img src="images/icons/emblem-system-stop.png" alt="">
</a>
<?php } else { ?>
<a class="col-1 t-center inactive"
   href="#"
   title="">
  <img src="images/blank.png" alt="">
</a>
<?php } ?>

<?php if($memento->isEditable()) { ?>
<a class="overlayed col-auto btn-light"
   href="index.php?mod=dbmf3&amp;tpl=memento_edit&amp;memento_id=<?= $memento->id ?>"
   title="">
<?php } else { ?>
<div class="col-auto">
<?php } ?>
  <div class="date">
    <b><?= $memento->getDate() ?></b><br>
    <span class="help"><?= $m_catgname ?></span><br>
    <?= $contact->lastname ?>
  </div>
  <div class="infos">
    <?= $crit_infos ?>
    <?= $memento->comments ?>
  </div>
<?php if( $memento->comments2!='' ) { ?>
  <div class="tooltip d-show-md">
    <img src="images/icons/mail-attachment.png" alt="">
    <span class="left"><?= $memento->comments2 ?></span>
  </div>
<?php } ?>
<?php if($memento->isEditable()) { ?>
</a>
<?php } else { ?>
</div>
<?php } ?>

<a class="overlayed col-1 t-center btn-secondary-light d-show-sm"
   href="index.php?mod=dbmf3&amp;tpl=contact_edit&amp;contact_id=<?= $contact->id ?>"
   title="<?= _G('DBMF_contact_edit') ?>">
  <img src="images/icons/text-editor.png" alt="">
</a>

<a class="hidelayed col-1 t-center btn-danger-light d-show-md"
   href="index.php?mod=dbmf3&amp;tpl=memento_edit&amp;memento_id=<?= $memento->id ?>&amp;memento_delete=1"
   title="<?= _G('DBMF_memento_edition_delete') ?>"
   data-overconfirm="<?= MySBUtil::str2strict(_G('DBMF_confirm_memento_delete')) ?>">
  <img src="images/icons/user-trash.png" alt="">
</a>

</div>

<?php
if(isset($_GET['memento_id'])) {
  echo '
    </div>

<script>
slide_show("mementos_new_title");
</script>
';
}
?>

