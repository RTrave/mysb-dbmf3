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
<div class="overlayWidth" data-overwidth="400" data-overheight="5"></div>
<div id="dbmfContact">

<h1 style="vertical-align: top; padding-top: 5px;" class="roundtop">

<div style="float: right; margin-right: 15px;">
        <form   action="blank.php?mod=dbmf3&amp;tpl=delcontact&amp;contact_id='.$contact->id.'" 
                method="post" 
                class="overlayed"
                data-overconfirm="'.MySBUtil::str2strict(sprintf(_G('DBMF_confirm_contact_delete'),$contact->lastname, $contact->firstname )).'">
            <input  type="hidden" name="dbmf_contact_delete" value="'.$contact->id.'">
            <input  type="hidden" name="dbmf_request_reuse" value="1">
            <input  src="images/icons/user-trash.png"
                    type="image"
                    alt="'._G('DBMF_contact_delete').'"
                    title="'.sprintf(_G('DBMF_contact_delete'),$contact->lastname, $contact->firstname ).'">
        </form>
</div>
    '.$contact->lastname.' '.$contact->firstname.'
</h1>
';

echo '
<form   action="blank.php?mod=dbmf3&amp;tpl=editcontact&amp;contact_id='.$contact->id.'"
        method="post"
        class="overlayed">


<div class="cform">

<div class="table_support1">
<table class="cform"><tbody>
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
    <td style="height: 18px; vertical-align: top; text-align: right;">'._G($blockref->lname).':</td>
    <td>';
            if($block->isEditable()) 
                echo $blockref->htmlForm('blockref',$contact->$refname,'('.$contact->lastname.' '.$contact->firstname.')');
            else {
                if( $blockref->getType()=='tel' or $blockref->getType()=='url' ) 
                    echo $blockref->htmlFormNonEditable('blockref',$contact->$refname,'('.$contact->lastname.' '.$contact->firstname.')');
                else
                    echo $blockref->htmlFormNonEditable('blockref',$contact->$refname);
            }
            echo '
    </td>
</tr>';
        }
    }
}

if(MySBRoleHelper::checkAccess('dbmf_editor',false)) echo '
</tbody></table>
</div>
</div>


<div class="foot roundbottom">
        <input type="hidden" name="contact_edit" value="1">
        <input type="submit" value="'._G('DBMF_contact_edition_submit').'">
</div>


</form>

</div>
';

echo '
</div>
';

if(isset($_POST['memento_delete'])) {
    echo '
<script>
$("#memento'.$_POST['memento_delete'].'").fadeOut(1000,"swing");
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
