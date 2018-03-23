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


if( isset($_GET['memento_id']) ) {
    $memento = new MySBDBMFMemento($_GET['memento_id']);
    $contact = new MySBDBMFContact($memento->contact_id);
} else {
    $memento = $app->tpl_dbmf_currentmemento;
    $contact = MySBDBMFMementoHelper::getContactInfos($memento->contact_id);
}


    $m_user = MySBUserHelper::getByID($memento->user_id);
    if($memento->memcatg_id!=0) $memcatg = MySBDBMFMementoCatgHelper::getByID($memento->memcatg_id);
    else $memcatg = null;
    if($memento->isActive()) $Active = true;
    else $Active = false;
    //if($Active) $memclass = 'mem_active';
    //elseif(!$Active and $memento->date_process!='') $memclass = 'mem_processed';
    //else $memclass='';
    //$anchor_nb++;
    if($Active) $memclass = 'mem_active';
    elseif(!$Active and $memento->date_process!='') $memclass = 'mem_processed';
    else $memclass='mem_processed';

    if($memcatg!=null) $m_catgname = $memcatg->name;
    else $m_catgname = '<i>'.$m_user->login.'</i>';
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
    <?= $memento->comments ?>
  </div>
<?php if( $memento->comments2!='' ) { ?>
  <div class="tooltip">
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
   title="<?= _G('DBMF_edit').' '.$contact->lastname.' '.$contact->firstname.' (memento '.$memento->id ?>)">
  <img src="images/icons/text-editor.png" alt="">
</a>

<a class="hidelayed col-1 t-center btn-danger-light d-show-md"
   href="index.php?mod=dbmf3&amp;tpl=memento_edit&amp;memento_id=<?= $memento->id ?>&amp;memento_delete=1"
   title="<?= _G('DBMF_memento_edition_delete') ?>"
   data-overconfirm="<?= MySBUtil::str2strict(_G('DBMF_confirm_memento_delete')) ?>">
  <img src="images/icons/user-trash.png" alt="">
</a>

</div>

<!--
<?php
    echo '
<table style="width: 100%; background-color: transparent;" class="roundtop roundbottom '.$memclass.'"><tbody>
<tr>
    <td class="infos" style="background-color1: yellow;" class="roundtop roundbottom">
        <div class="date floatingcell">';
    if($memento->isEditable()) echo '
        <a  href="index.php?mod=dbmf3&amp;tpl=memento_edit&amp;memento_id='.$memento->id.'"
            class="overlayed"><b>'.$memento->getDate().'</b></a>';
    else echo '
        <b>'.$memento->getDate().'</b>';
    echo '<br>
        <span class="cell_hidem"><small>'.$m_catgname.'</small></span>
        </div>
        <div class="name floatingcell">
        <div style="float: left;">
        <a  href="index.php?mod=dbmf3&amp;tpl=contact_edit&amp;contact_id='.$contact->id.'"
            class="overlayed">
            <img    src="images/icons/text-editor.png"
                    alt="Edition '.$contact->id.'"
                    title="'._G('DBMF_edit').' '.$contact->lastname.' '.$contact->firstname.' (memento '.$memento->id.')">
        </a>
        </div>
        <b>'.MySBUtil::str2abbrv($contact->lastname,10,10).'</b><br>
        '.MySBUtil::str2abbrv($contact->firstname,10,10).'
        </div>
    </td>
    <td class="comments">
        <table style="width: 100%; background-color: transparent;"><tbody><tr>
            <td style="min-width: 20%;"><div class="mem_maxh">'.$memento->comments.'</div></td>
        </tr></tbody></table>
    </td>';
    if( $memento->comments2!='' ) {
        echo '
    <td class="actions" style="vertical-align: top;">
        <div class="tooltip">
        <img src="images/icons/mail-attachment.png"
             alt="'._G('DBMF_memento_edition_delete').'">
        <span>'.$memento->comments2.'
        </div>
    </td>';
    }
    echo '
    <td class="actions" style="vertical-align: top;">';
    if($Active) {
        echo '
        <form action="index.php?mod=dbmf3&tpl=memento_edit&amp;memento_id='.$memento->id.'"
              method="post"
              class="hidelayed">
            <input type="hidden" name="memento_process" value="'.$memento->id.'">
            <input src="images/icons/emblem-system.png"
                   type="image"
                   alt="'._G('DBMF_memento_process_submit').'"
                   title="'._G('DBMF_memento_process_submit').'">
        </form>';
    } elseif(!$Active and $memento->date_process!='') {
        echo '
        <form action="index.php?mod=dbmf3&tpl=memento_edit&amp;memento_id='.$memento->id.'"
              method="post"
              class="hidelayed">
            <input type="hidden" name="memento_unprocess" value="'.$memento->id.'">
            <input src="images/icons/emblem-system-stop.png"
                   type="image"
                   alt="'._G('DBMF_memento_unprocess_submit').'"
                   title="'._G('DBMF_memento_unprocess_submit').'">
        </form>';
    }
    echo '
    </td>
    <td class="actions" style="vertical-align: top;">
        <form action="index.php?mod=dbmf3&amp;tpl=memento_edit&amp;memento_id='.$memento->id.'"
              method="post"
              class="hidelayed"
              data-overconfirm="'.MySBUtil::str2strict(_G('DBMF_confirm_memento_delete')).'">
    <div class="action first">
            <input type="hidden" name="memento_delete" value="'.$memento->id.'">
            <input src="images/icons/user-trash.png"
                   type="image"
                   alt="'._G('DBMF_memento_edition_delete').'"
                   title="'._G('DBMF_memento_edition_delete').'">
    </div>
        </form>
    </td>
</tr>
</tbody></table>';

?>

-->
