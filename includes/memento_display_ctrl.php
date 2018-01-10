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
    if($memcatg!=null) $m_catgname = $memcatg->name;
    else $m_catgname = '<i>'.$m_user->login.'</i>';
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
            <td style="min-width: 20%;">'.$memento->comments2.'</td>
        </tr></tbody></table>
    </td>
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
