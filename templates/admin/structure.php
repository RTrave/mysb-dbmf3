<?php
/***************************************************************************
 *
 *   phpMySandBox/DBMF3 module - TRoman<abadcafe@free.fr> - 2012
 *   blockref program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License', or
 *   ('at your option) any later version.
 *
***************************************************************************/

// No direct access.
defined('_MySBEXEC') or die;

global $app;

echo '
<div id="mysbMenuLevel">
<ul>
    <li class="first"><a href="index.php?mod=dbmf3&amp;tpl=admin/structure">'._G('DBMF_blocks_config').'</a></li>
    <li class="last"><a href="index.php?mod=dbmf3&amp;tpl=admin/memento">'._G('DBMF_mementos_config').'</a></li>
</ul>
</div>

<h1>'._G('DBMF_config').': '._G('DBMF_contacts_config').'</h1>';


if( isset($_POST['blockref_edit']) or
    isset($_POST['blockref_edit_process']) or
    isset($_POST['blockref_add']) ) {

$blockref = $app->tpl_blockref_edit;
echo '
<div class="list_support">

<div class="boxed">
<form action="index.php?mod=dbmf3&amp;tpl=admin/structure" method="post">
<div class="title roundtop">
    '.$blockref->keyname.' - <b>'._G($blockref->lname).'</b><br>
    <small><i>'._G('DBMF_blockref_type').':</i> '.$blockref->getType().'</small>
</div>
<div class="row">
    <div class="right"><input type="text" name="lname" value="'.$blockref->lname.'"></div>
    '._G('DBMF_blockref_lname').'
</div>
<div class="row">
    <div class="right">
    <select name="switchorderby">
        <option value="0">'._G('DBMF_blockref_orderby_no').'</option>
        <option value="1" '.MySBUtil::form_isselected($blockref->orderby,1).'>'._G('DBMF_blockref_orderby_ok').'</option>
    </select>
    </div>
    '._G('DBMF_blockref_option_orderby').'
</div>
<div class="row">
    <div class="right">
        <select name="blockref_alwaysshown">
            <option value="'.MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_NO.'"
                    '.MySBUtil::form_isselected($blockref->alwaysshown,MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_NO).'>'._G('DBMF_blockref_alwaysshown_no').'</option>
            <option value="'.MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_ASTEXT.'"
                    '.MySBUtil::form_isselected($blockref->alwaysshown,MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_ASTEXT).'>'._G('DBMF_blockref_alwaysshown_bottom').'</option>
            <option value="'.MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_ASPLUG.'"
                    '.MySBUtil::form_isselected($blockref->alwaysshown,MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_ASPLUG).'>'._G('DBMF_blockref_alwaysshown_plugins').'</option>
            <option value="'.MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_ASTEXTONLY.'"
                    '.MySBUtil::form_isselected($blockref->alwaysshown,MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_ASTEXTONLY).'>'._G('DBMF_blockref_alwaysshown_txtonly').'</option>
        </select>
    </div>
    '._G('DBMF_blockref_option_alwaysshown').'
</div>
<div class="row" style=" text-align: right;">
    <div style="float: left;">'._G('DBMF_blockref_infos').'</div>
    <div style="display: inline-block; margin: 0px 0px 0px auto;"><textarea name="infos" cols="28" rows="3">'.$blockref->infos.'</textarea>
    </div>
</div>
<div class="row" style="text-align: center;">
    <input type="hidden" name="blockref_edit_process" value="'.$blockref->id.'">
    <input type="submit" value="'._G('DBMF_blockref_edition_process').'">
</div>
</form>';

if($blockref->type==MYSB_VALUE_TYPE_VARCHAR64_SELECT) {
    echo '
<div class="title">Options</div>';
    $req_options = MySBDB::query("SELECT * from ".MySB_DBPREFIX."valueoptions ".
        "WHERE value_keyname='".$blockref->grp."-".$blockref->keyname."' ".
        "ORDER BY value0",
        "MySBDBMFBlockRef::htmlOptionFormTR()");
    while($option = MySBDB::fetch_array($req_options)) {
        echo '
<div class="row" style="border: 0px;">
    <form action="index.php?mod=dbmf3&amp;tpl=admin/structure" method="post">
    <div class="right">
        <input type="hidden" name="blockref_del_option" value="'.$option['value1'].'">
        <input type="hidden" name="blockref_edit" value="'.$blockref->id.'">
        <input type="hidden" name="blockref_option_id" value="'.$option['value0'].'">
        <input type="submit" value="'._G('DBMF_blockref_del_option').'">
        </div>
    </form>
    Option '.$option['value0'].'
</div>
<div class="row">
    <form action="index.php?mod=dbmf3&amp;tpl=admin/structure" method="post">
    <div class="right">
        <input type="hidden" name="blockref_option_id" value="'.$option['value0'].'">
        <input type="hidden" name="blockref_edit" value="'.$blockref->id.'">
        <input type="submit" value="'._G('DBMF_blockref_mod_option').'">
    </div>
    <input type="text" name="blockref_mod_option" value="'.$option['value1'].'">
    </form>
</div>';
    }
    echo '
<div class="row" style="border: 0px;">
    '._G('DBMF_blockref_newoption').'
</div>
<div class="row" style="border: 0px;">
    <form action="index.php?mod=dbmf3&amp;tpl=admin/structure" method="post">
    <div class="right">
        <input type="hidden" name="blockref_edit" value="'.$blockref->id.'">
        <input type="submit" value="'._G('DBMF_blockref_add_option').'">
    </div>
    <input type="text" name="blockref_new_option" value="">
    </form>
</div>';
}
echo '
</div>
</div>';
}


echo '
<h2>'._G('DBMF_currentblocks').'</h2>
<div class="list_support">';

$blocks = MySBDBMFBlockHelper::load();
foreach($blocks as $block) {

    $group_edit = MySBGroupHelper::getByID($block->groupedit_id);

    echo '
<div class="boxed" style="width: 600px; margin-top: 10px;" id="a_block'.$block->id.'">
    <div class="title roundtop">
        <div style="float: left; width: 100px;">
        <div style="display: inline-block; width: 40px;">
        <form action="index.php?mod=dbmf3&amp;tpl=admin/structure#a_block'.$block->id.'" method="post">
        <input type="hidden" name="block_orderdown" value="'.$block->id.'">
        <input type="submit" value="&darr;">
        </form>
        </div>
        <div style="display: inline-block; width: 40px;">
        <form action="index.php?mod=dbmf3&amp;tpl=admin/structure#a_block'.$block->id.'" method="post">
        <input type="hidden" name="block_orderup" value="'.$block->id.'">
        <input type="submit" value="&uarr;">
        </form>
        </div>
        </div>
        <div style="float: right;">
        <form action="index.php?mod=dbmf3&amp;tpl=admin/structure#a_block'.$block->id.'" method="post"
        OnSubmit="return mysb_confirm(\''.MySBUtil::str2strict(sprintf(_G('DBMF_confirm_block_delete'), $block->lname, $group_edit->comments )).'\')">
        <input type="hidden" name="block_del" value="'.$block->id.'">
        <input  src="images/icons/user-trash.png"
                    type="image"
                    alt="'._G('DBMF_block_delete').'"
                    title="'._G('DBMF_block_delete').'">
        </form>
        </div>
        <b>'._G($block->lname).'</b> <small><i>(ID:'.$block->id.')</i></small>
    </div>

    <form action="index.php?mod=dbmf3&amp;tpl=admin/structure" method="post">
    <div class="row">
        <div class="right"><input type="text" name="lname" value="'.$block->lname.'"></div>
        '._G('DBMF_block_lname').'
    </div>
    <div class="row">
        <div class="right"><select name="group_id">';
        $groups = MySBDBMFGroupHelper::load();
        foreach($groups as $group)
            if($group->dbmf_priority>0)
                echo '
            <option value="'.$group->id.'" '.MySBUtil::form_isselected($group->id,$group_edit->id).'>'.$group->comments.'</option>';
        echo '
        </select></div>
        '._G('DBMF_block_groupedit').'
    </div>
    <div class="row" style="text-align: center;">
        <input type="hidden" name="block_edition" value="'.$block->id.'">
        <input type="submit" value="'._G('DBMF_block_edition').'">
    </div>
    </form>
    <div class="row" style="min-height: 0px; padding: 0px;">
    </div>
    ';

    foreach($block->blockrefs as $blockref) {
        if($blockref->status==MYSB_DBMF_BLOCKREF_STATUS_ACTIVE)
            $class_bref = ' background: #ffffff;';
        else $class_bref = ' background: #eeeeee;';
        echo '
    <div class="row" style="'.$class_bref.'">
        <div style="float: left;">
        <form action="index.php?mod=dbmf3&amp;tpl=admin/structure" method="post">
        <input type="hidden" name="blockref_edit" value="'.$blockref->id.'">
        <input  src="images/icons/text-editor.png"
                    type="image"
                    alt="'._G('DBMF_blockref_edition').'"
                    title="'._G('DBMF_blockref_edition').'">
        </form>
        </div>
        <div style="float: right;">
        <form action="index.php?mod=dbmf3&amp;tpl=admin/structure#a_block'.$block->id.'" method="post"
        OnSubmit="return mysb_confirm(\''.MySBUtil::str2strict(sprintf(_G('DBMF_confirm_blockref_delete'), $blockref->lname, $blockref->keyname )).'\')">
        <input type="hidden" name="block_id" value="'.$block->id.'">
        <input type="hidden" name="blockref_del" value="'.$blockref->id.'">
        <input  src="images/icons/user-trash.png"
                    type="image"
                    alt="'._G('DBMF_blockref_delete').'"
                    title="'._G('DBMF_blockref_delete').'">
        </form>
        </div>
        <div style="float: right;">
        <form action="index.php?mod=dbmf3&amp;tpl=admin/structure#a_block'.$block->id.'" method="post">
        <input type="hidden" name="blockref_switchactive" value="'.$blockref->id.'">';

            if($blockref->status==MYSB_DBMF_BLOCKREF_STATUS_ACTIVE)
                echo '
        <input type="submit" value="'._G('DBMF_blockref_desactive').'">';
            else
                echo '
        <input type="submit" value="'._G('DBMF_blockref_active').'">';

            echo '
        </form>
        </div>
        <div style="float: left; width: 80px; text-align: center;">
        <div style="display: inline-block;">
        <form action="index.php?mod=dbmf3&amp;tpl=admin/structure#a_block'.$block->id.'" method="post">
        <input type="hidden" name="blockref_orderdown" value="'.$blockref->id.'">
        <input  src="images/icons/go-down.png"
                    type="image"
                    alt="&darr;"
                    title="&darr;">
        </form>
        </div>
        <div style="display: inline-block;">
        <form action="index.php?mod=dbmf3&amp;tpl=admin/structure#a_block'.$block->id.'" method="post">
        <input type="hidden" name="blockref_orderup" value="'.$blockref->id.'">
        <input  src="images/icons/go-up.png"
                    type="image"
                    alt="&uarr;"
                    title="&uarr;">
        </form>
        </div>
        </div>
        '.$blockref->keyname.'<span class="cell_show"><br></span> <b>'._G($blockref->lname).'</b><br>
         <small>'.$blockref->getType().'</small>
    </div>';
    }
    echo '
    <form action="index.php?mod=dbmf3&amp;tpl=admin/structure#a_block'.$block->id.'" method="post">
    <div class="row" style="border-bottom: 0px;">
    <div class="right"><input type="text" name="lname" value=""></div>
    '._G('DBMF_blockref_name').'
    </div>
    <div class="row" style="border-bottom: 0px;">
    <div class="right">
        <select name="type">
            <option value="'.MYSB_VALUE_TYPE_INT.'" >int</option>
            <option value="'.MYSB_VALUE_TYPE_BOOL.'" >bool</option>
            <option value="'.MYSB_VALUE_TYPE_VARCHAR64.'" >varchar(64)</option>
            <option value="'.MYSB_VALUE_TYPE_VARCHAR512.'" >varchar(512)</option>
            <option value="'.MYSB_VALUE_TYPE_TEXT.'" >text/varchar(512)</option>
            <option value="'.MYSB_VALUE_TYPE_VARCHAR64_SELECT.'" >select/varchar(64)</option>
            <option value="'.MYSB_VALUE_TYPE_TEL.'" >tel/varchar(64)</option>
            <option value="'.MYSB_VALUE_TYPE_URL.'" >url/varchar(128)</option>
        </select></div>
    '._G('DBMF_blockref_type').'
    </div>
    <div class="row" style="text-align: center;">
        <input type="hidden" name="blockref_add" value="'.$block->id.'">
        <input type="submit" value="'._G('DBMF_blockref_add').'">
    </div>
    </form>
</div>';
}

echo '
</div>

<div class="boxed" style="width: 600px; margin-top: 10px;">
<form action="?mod=dbmf3&amp;tpl=admin/structure" method="post">
    <div class="title roundtop">
        <b>'._G('DBMF_addblock').'</b>
    </div>
    <div class="row">
        <div class="right"><input type="text" name="addblock_name"></div>
        '._G('DBMF_block_name').'
    </div>
    <div class="row" style="text-align: center;">
        <input type="hidden" name="block_add" value="1">
        <input type="submit" value="'._G('DBMF_block_add').'">
    </div>
</form>
</div>';

?>
