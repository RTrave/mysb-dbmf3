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
<h1 style="vertical-align: top; padding-top: 5px;">';
    if( $contact->mail!='' ) 
        echo '
        <a href="mailto:'.$contact->mail.'">
            <img    src="modules/dbmf3/images/mail_icon24.png" 
                    alt="'._G('DBMF_mailto').' '.$contact->id.'" 
                    title="'._G('DBMF_mailto').' '.$contact->lastname.' '.$contact->firstname.' ('.$contact->id.')"
                    style="width: 18px; margin: 0px;"></a>';

echo '
    '.$contact->lastname.' '.$contact->firstname.' ('.$contact->id.')
</h1>';

echo '
<form action="?mod=dbmf3&amp;tpl=editcontact&amp;contact_id='.$contact->id.'" method="post">

<div class="table_support">
<table id="rsvpContact"><tbody>
';

_T('templates/common_edition.php','dbmf3');

$blocks = MySBDBMFBlockHelper::load();
foreach($blocks as $block) {
    $group_edit = MySBGroupHelper::getByID($block->groupedit_id);
    echo '
<tr class="title" >
    <td colspan="2">'._G($block->lname).' <small><i>('.$group_edit->comments.')</i></small></td>
</tr>';
    foreach($block->blockrefs as $blockref) {
        if($blockref->isActive()) {
            if(!$block->isEditable())
                $class_edit = 'background: #cccccc;';
            else 
                $class_edit = '';
            $refname = $blockref->keyname;
            echo '
<tr style="'.$class_edit.'">
    <td style="vertical-align: top; text-align: right;">'._G($blockref->lname).':</td>
    <td>';
            if($block->isEditable()) 
                echo $blockref->htmlForm('blockref',$contact->$refname);
            else 
                echo $blockref->htmlFormNonEditable('blockref',$contact->$refname);
            echo '
    </td>
</tr>';
        }
    }
}

if(MySBRoleHelper::checkAccess('dbmf_editor',false)) echo '
<tr>
    <td colspan="2" style="text-align: center;">
        <input type="hidden" name="contact_edit" value="1">
        <input type="submit" value="'._G('DBMF_contact_edition_submit').'">
    </td>
</tr>';

echo '
</tbody></table>
</div>
</form>
<br>
';

echo '
<form   action="?mod=dbmf3&amp;tpl=editcontact" method="post"
        OnSubmit="return mysb_confirm(\''.MySBUtil::str2strict(sprintf(_G('DBMF_confirm_contact_delete'),$contact->lastname, $contact->firstname )).'\')">

<div class="table_support">
<table style="width: 95%;"><tbody>
<tr>
    <td style="text-align: center;">
        <input type="hidden" name="contact_delete" value="'.$contact->id.'">
        <input type="submit" value="'._G('DBMF_contact_delete_submit').'">
    </td>
</tr>
</tbody></table>
</div>
</form>
';

?>
