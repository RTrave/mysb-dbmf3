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


echo '
<h1>'._G("DBMF_contact_edition").'</h1>
<h2>'.$contact->lastname.' '.$contact->firstname.' ('.$contact->id.')</h2>';

echo '
<form action="?mod=dbmf3&amp;tpl=editcontact&amp;contact_id='.$contact->id.'" method="post">

<div class="table_support" align="center">
<table><tbody>
';

_T('templates/common_edition.php','dbmf3');

$blocks = MySBDBMFBlockHelper::load();
foreach($blocks as $block) {
    $group_edit = MySBGroupHelper::getByID($block->groupedit_id);
    echo '
<tr class="title" >
    <td colspan="2">'.$block->lname.' <small><i>('.$group_edit->comments.')</i></small></td>
</tr>';
    foreach($block->blockrefs as $blockref) {
        if($blockref->isActive()) {
            if(!$block->isEditable())
                $class_edit = 'background: #cccccc;';
            else 
                $class_edit = '';
            $refname = 'br'.$blockref->id;
            echo '
<tr style="'.$class_edit.'">
    <td style="vertical-align: top; text-align: right;"><b>'.$blockref->lname.':</b></td>
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

echo '
<tr>
    <td colspan="2" align="center">
        <input type="hidden" name="contact_edit" value="1">
        <input type="submit" value="'._G('DBMF_contact_edition_submit').'" class="submit">
    </td>
</tr>

</tbody></table>
</div>
</form>
<br>
';

?>
