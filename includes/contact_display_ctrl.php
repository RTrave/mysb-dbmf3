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
?>

<?php if( $contact->mail!='' ) { ?>
<a class="col-1 t-center btn-light"
   href="mailto:<?= $contact->mail ?>"
   title="<?= _G('DBMF_mailto') ?> <?= $contact->lastname ?> <?= $contact->firstname ?>">
  <img src="images/icons/mail-unread.png" alt="mail-unread">
</a>
<?php } else { ?>
<a class="col-1 t-center inactive" href="javascript:void(0);">
  <img src="images/blank.png" alt="blank">
</a>
<?php } ?>

<a class="overlayed col-auto btn-light" style="color: black;"
   href="index.php?mod=dbmf3&amp;tpl=contact_edit&amp;contact_id=<?= $contact->id ?>"
   title="<?= _G('DBMF_edit') ?> <?= $contact->lastname ?> <?= $contact->firstname ?> (<?= $contact->id ?>)">
  <p>
    <b><?= $contact->lastname ?></b><br><?= $contact->firstname ?><br>
    <span class="help">
      <?= sprintf(_G('DBMF_days_old'),$daysold) ?>
    </span><br>
<?php
    $as_textonlylist = MySBDBMFBlockRefHelper::loadAlwaysShown(MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_ASTEXTONLY);
    foreach($as_textonlylist as $sblockref) {
        $column_name = $sblockref->keyname;
        if( $contact->$column_name!='' and $contact->$column_name!=0 )
            echo '
    <span class="d-show-sm help">
            '.$sblockref->htmlFormNonEditable( '',
                                               $contact->$column_name,
                                               _G($sblockref->lname),
                                               false,
                                               false).'</span><br class="d-show-sm">';
        else echo '
    <br class="d-show-sm">';
    }
    $as_textlist = MySBDBMFBlockRefHelper::loadAlwaysShown(MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_ASTEXT);
    foreach($as_textlist as $sblockref) {
        $column_name = $sblockref->keyname;
        if( $contact->$column_name!='' and $contact->$column_name!=0 )
            echo '
    <span class="d-show-md help">
            '.$sblockref->htmlFormNonEditable( '',
                                               $contact->$column_name,
                                               MySBUtil::str2abbrv(_G($sblockref->lname),4,4),
                                               false,
                                               false).'</span><br class="d-show-md">';
        else echo '
    <br class="d-show-md">';
    }
?>
  </p>
</a>
<div class="col-6 bg-primary-light">
  <div class="display_plugins t-right">
    <div class="plugins-int" style="">
<?php
    foreach($as_textonlylist as $sblockref)
        echo $sblockref->displayPlugin( $contact, 'd-hide-sm' );
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
    $pluginsDisplay = MySBPluginHelper::loadByType('DBMFDisplay');
    foreach($pluginsDisplay as $plugin)
        echo $plugin->displayIcons(1,$contact);
?>
    </div>
  </div>
</div>

<a class="hidelayed col-1 t-center btn-danger-light"
   href="index.php?mod=dbmf3&amp;tpl=contact_del&amp;contact_delete=<?= $contact->id ?>&amp;dbmf_request_reuse=1"
   title="<?= sprintf(_G('DBMF_contact_delete'),$contact->lastname, $contact->firstname ) ?>"
   data-overconfirm="<?= MySBUtil::str2strict(sprintf(_G('DBMF_confirm_contact_delete'),$contact->lastname, $contact->firstname )) ?>">
  <img src="images/icons/user-trash.png"
       alt="user-trash">
</a>

<?php
/*
$pluginsDisplay = MySBPluginHelper::loadByType('DBMFDisplay');
if(count($pluginsDisplay)!=0) {
  echo '
    </div>
    <div class="row contact_display bg-primary-light collapse">';
  foreach($pluginsDisplay as $plugin)
    echo $plugin->displayIcons(1,$contact);
  echo '';
}
*/
?>

<!--

</div>
<div>

<?php
    echo '
<div class="cell roundtop roundbottom" style="width: 100%;">
<table class="cell"><tbody>

<tr class="cell">
    <td style="width: 20px; text-align: left;">
        <a  class="overlayed"
            href="index.php?mod=dbmf3&amp;tpl=contact_edit&amp;contact_id='.$contact->id.'">
        <img    src="images/icons/text-editor.png"
                alt="Edition '.$contact->id.'"
                title="'._G('DBMF_edit').' '.$contact->lastname.' '.$contact->firstname.' ('.$contact->id.')"
                style="width1: 24px"></a>
    </td>
    <td style="width: 26px; text-align: left;">';
    if( $contact->mail!='' ) echo '
            <a href="mailto:'.$contact->mail.'">
            <img src="images/icons/mail-unread.png"
                 alt="'._G('DBMF_mailto').' '.$contact->id.'"
                 title="'._G('DBMF_mailto').' '.$contact->lastname.' '.$contact->firstname.'"></a>';

    echo '
    </td>
    <td colspan="2" class="cell_names" style="text-align: left;">
        <b>'.$contact->lastname.'</b><br>'.$contact->firstname.'
    </td>
    <td rowspan="2" class="cell_plugins">
        <div class="incell_hide">';

    $as_showlist = MySBDBMFBlockRefHelper::loadAlwaysShown(MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_ASPLUG);
    foreach($as_showlist as $sblockref)
        echo $sblockref->displayPlugin( $contact, 'incell_hide' );

    foreach($showcols_blockrefs as $sblockref)
        echo $sblockref->displayPlugin( $contact, 'incell_hide' );

    echo '
        </div>
    </td>
    <td style="width: 20px; height: 20px; text-align: right; vertical-align: top;">
        <form   action="index.php?mod=dbmf3&amp;tpl=contact_del&amp;contact_id='.$contact->id.'"
                method="post"
                class="hidelayed"
                data-overconfirm="'.MySBUtil::str2strict(sprintf(_G('DBMF_confirm_contact_delete'),$contact->lastname, $contact->firstname )).'">
            <input  type="hidden" name="dbmf_contact_delete" value="'.$contact->id.'">
            <input  type="hidden" name="dbmf_request_reuse" value="1">
            <input  src="images/icons/user-trash.png"
                    type="image"
                    alt="'._G('DBMF_contact_delete').'"
                    title="'.sprintf(_G('DBMF_contact_delete'),$contact->lastname, $contact->firstname ).'">
        </form>
    </td>
</tr>

<tr class="cell">
    <td colspan="3" rowspan="2" class="cell_infos">
        <div class="cell_hide"><small><i>'.sprintf(_G('DBMF_days_old'),$daysold).'</i></small></div>
        ';

    $as_textonlylist = MySBDBMFBlockRefHelper::loadAlwaysShown(MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_ASTEXTONLY);
    foreach($as_textonlylist as $sblockref) {
        $column_name = $sblockref->keyname;
        if( $contact->$column_name!='' and $contact->$column_name!=0 )
            echo '
            '.$sblockref->htmlFormNonEditable('',$contact->$column_name,_G($sblockref->lname)).'<br>';
        else echo '<br>';
    }
    echo '
        <div class="cell_hide">';
    $as_textlist = MySBDBMFBlockRefHelper::loadAlwaysShown(MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_ASTEXT);
    foreach($as_textlist as $sblockref) {
        $column_name = $sblockref->keyname;
        if( $contact->$column_name!='' and $contact->$column_name!=0 )
            echo '
            '.$sblockref->htmlFormNonEditable('',$contact->$column_name,MySBUtil::str2abbrv(_G($sblockref->lname),4,4)).'<br>';
        else echo '<br>';
    }

    echo '
        </div>
    </td>
    <td class="cell_ext">
    </td>
    <td>
    </td>
</tr>

<tr class="cell">
    <td colspan="3" style="" class="cell_icons">
        <div class="cell_hide">
';
    $pluginsDisplay = MySBPluginHelper::loadByType('DBMFDisplay');
    foreach($pluginsDisplay as $plugin)
        echo $plugin->displayIcons(1,$contact);
    echo '
        </div>
        ';

    $as_hidelist = MySBDBMFBlockRefHelper::loadAlwaysShown(MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_ASTEXT);
    $as_showlist = MySBDBMFBlockRefHelper::loadAlwaysShown(MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_ASPLUG);
    $size_cell = 50*( count($as_hidelist) + count($as_showlist) );
    echo '
        <div class="incell_hide" style="text-align: right; width: '.$size_cell.'px;">';

/*    foreach($as_hidelist as $sblockref)
        echo $sblockref->displayPlugin( $contact, 'incell_floatshow' );
    foreach($as_showlist as $sblockref)
        echo $sblockref->displayPlugin( $contact, '' );
*/

    echo '
        </div>
    </td>
</tr>

</tbody></table>
</div>

-->
';

?>
