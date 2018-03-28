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


if( !MySBRoleHelper::checkAccess('dbmf_user') ) return;

$showcols_blockrefs = array();
$showcols = new MySBCSValues($app->auth_user->dbmf_showcols);
foreach( $showcols->values as $br_id ) {
    $showblockref = MySBDBMFBlockRefHelper::getByID( $br_id );
    if( isset($showblockref) and $showblockref->isActive() )
        $showcols_blockrefs[] = $showblockref;
}


if( isset($_GET['id']) )
    $contact = new MySBDBMFContact( $_GET['id'] );
else
    $contact = $app->tpl_dbmf_currentcontact;
$date_modif = new MySBDateTime($contact->date_modif);
$daysold = $date_modif->absDiff();

$pluginsDisplay = MySBPluginHelper::loadByType('DBMFDisplay');
?>

<div class="row contact_display bg-light">

<?php if( $contact->mail!='' ) { ?>
<a class="col-1 t-center btn-primary-light"
   href="mailto:<?= $contact->mail ?>"
   title="<?= _G('DBMF_mailto') ?> <?= $contact->lastname ?> <?= $contact->firstname ?>">
  <img src="images/icons/mail-unread.png" alt="mail-unread">
</a>
<?php } else { ?>
<a class="col-1 t-center inactive" href="javascript:void(0);">
  <img src="images/blank.png" alt="blank">
</a>
<?php } ?>
<a class="overlayed col-1 t-center btn-primary-light"
   href="index.php?mod=dbmf3&amp;tpl=contact_edit&amp;contact_id=<?= $contact->id ?>"
   title="<?= _G('DBMF_edit') ?> <?= $contact->lastname ?> <?= $contact->firstname ?> (<?= $contact->id ?>)">
  <img src="images/icons/text-editor.png" alt="text-editor"><br>
  <span class="daysold_text"><?= sprintf(_G('DBMF_days_old'),$daysold) ?></span>
</a>

<div class="col-auto">
  <p>
    <b><?= $contact->lastname ?></b><br><?= $contact->firstname ?><br>
<?php
    $as_textonlylist = MySBDBMFBlockRefHelper::loadAlwaysShown(MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_ASTEXTONLY);
    foreach($as_textonlylist as $sblockref) {
        $column_name = $sblockref->keyname;
        if( $contact->$column_name!='' and $contact->$column_name!=0 )
            echo '
    <span class="d-show-sm blockref_text">
            '.$sblockref->htmlFormNonEditable( '',
                                               $contact->$column_name,
                                               _G($sblockref->lname),
                                               false,
                                               true).'<br></span>';
        else echo '
    <span class="d-show-sm blockref_text">.<br></span>';
    }
    $as_textlist = MySBDBMFBlockRefHelper::loadAlwaysShown(MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_ASTEXT);
    foreach($as_textlist as $sblockref) {
        $column_name = $sblockref->keyname;
        if( $contact->$column_name!='' and $contact->$column_name!=0 )
            echo '
    <span class="d-show-md blockref_text">
            '.$sblockref->htmlFormNonEditable( '',
                                               $contact->$column_name,
                                               MySBUtil::str2abbrv(_G($sblockref->lname),4,4),
                                               false,
                                               true).'<br></span>';
        else echo '
    <span class="d-show-md blockref_text">.<br></span>';
    }
?>
  </p>
  <div style="right: 0; top: 0;">
    <div class="display_plugins t-right">
      <div class="plugins-int" style="">
<?php
/*
    foreach($as_textonlylist as $sblockref)
        echo $sblockref->displayPlugin( $contact, 'd-hide-sm' );
*/
    foreach($as_textlist as $sblockref)
        echo $sblockref->displayPlugin( $contact, 'd-hide-md' );
    $as_showlist = MySBDBMFBlockRefHelper::loadAlwaysShown(MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_ASPLUG);
    foreach($as_showlist as $sblockref)
        echo $sblockref->displayPlugin( $contact, 'd-show-sm' );
    foreach($showcols_blockrefs as $sblockref)
        echo $sblockref->displayPlugin( $contact, 'd-show-md' );
?>
      </div>
      <div class="plugins-ext d-show-md">
<?php
    foreach($pluginsDisplay as $plugin)
        echo $plugin->displayIcons(1,$contact);
?>
      </div>
    </div>
  </div>
</div>

<a class="hidelayed col-1 t-center btn-danger-light"
   href="index.php?mod=dbmf3&amp;inc=contact_del&amp;contact_delete=<?= $contact->id ?>"
   title="<?= sprintf(_G('DBMF_contact_delete'),$contact->lastname, $contact->firstname ) ?>"
   data-overconfirm="<?= MySBUtil::str2strict(sprintf(_G('DBMF_confirm_contact_delete'),$contact->lastname, $contact->firstname )) ?>">
  <img src="images/icons/user-trash.png"
       alt="user-trash">
</a>

</div>
