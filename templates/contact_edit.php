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
<div class="overlayWidth" data-overwidth="440"></div>
<div class="overlayHeight" data-overheight="600"></div>

<div id="dbmfContact">

<div class="overHead">

    <div style="float: left; margin-left: 5px;">
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
    </div>
    '.$contact->lastname.' '.$contact->firstname.'
</div>
';

echo '
<form   action="index.php?mod=dbmf3&amp;tpl=contact_edit&amp;contact_id='.$contact->id.'"
        method="post"
        class="overlayed">

<div class="overBody">

<div class="list_support" style="padding: 2px 2px;">';

_incI('common_edition','dbmf3');

$blocks = MySBDBMFBlockHelper::load();
foreach($blocks as $block) {
    $group_edit = MySBGroupHelper::getByID($block->groupedit_id);
    echo '
<div class="title">
    <b>'._G($block->lname).' <small><i>('.$group_edit->comments.')</i></small></b>
</div>';
    foreach($block->blockrefs as $blockref) {
        if($blockref->isActive()) {
            if(!$block->isEditable())
                $class_edit = 'background: #cccccc;';
            else 
                $class_edit = '';
            $refname = $blockref->keyname;
            if( $blockref->getType()=='text' ) {
                echo '
<div class="row" style="'.$class_edit.' text-align: right;">
    <div style="float: left;"><b>'._G($blockref->lname).':</b>';
                if( $blockref->infos!='' )
                    echo '<br><span class="help">'.$blockref->infos.'</span>';
                echo '</div>
    <div style="display: inline-block; margin: 0px 0px 0px auto;">';
            } else {
                echo '
<div class="row" style="'.$class_edit.'">
    <div class="right">';
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
            if( $blockref->getType()!='text' ) {
                echo '
    <b>'._G($blockref->lname).':</b>';
                if( $blockref->infos!='' )
                    echo '<br><span class="help">'.$blockref->infos.'</span>';
            }
            echo '
</div>';
        }
    }
}
echo '

</div>

</div>
<div class="overFoot">
 ';

if(MySBRoleHelper::checkAccess('dbmf_editor',false)) echo '
    <input type="hidden" name="contact_edit" value="1">
    <input type="submit" value="'._G('DBMF_contact_edition_submit').'">';

echo '
</div>

</form>

</div>';

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
