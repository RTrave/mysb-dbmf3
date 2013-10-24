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
<h1>'._G('DBMF_blocks_edit').'</h1>';


if( isset($_POST['blockref_edit']) or 
    isset($_POST['blockref_edit_process']) or 
    isset($_POST['blockref_add']) ) {

$blockref = $app->tpl_blockref_edit;
echo '

<h2>'._G('DBMF_blockref_edition').'</h2>

<div class="table_support">
<form action="?mod=dbmf3&amp;tpl=blockedit" method="post">
<table style="width: 50%;"><tbody>
<tr class="title">
    <td style="text-align: center;" colspan="2">'._G($blockref->lname).'('.$blockref->keyname.')</td>
</tr>
<tr>
    <td style="text-align: right; width: 50%;">'._G('DBMF_blockref_lname').'</td>
    <td><input type="text" name="lname" value="'.$blockref->lname.'"></td>
</tr>
<tr>
    <td style="text-align: right; width: 50%;">'._G('DBMF_blockref_type').'</td>
    <td>'.$blockref->getType().'</td>
</tr>
<tr>
    <td colspan="2" style="text-align: center;">
        <input type="hidden" name="blockref_edit_process" value="'.$blockref->id.'">
        <input type="submit" value="'._G('DBMF_blockref_edition_process').'"><br>
    </td>
</tr>
</tbody></table>
</center>
</form>

<center>
<table style="width: 50%;"><tbody>
<tr class="title">
    <td style="text-align: center;" colspan="3">Options</td>
</tr>
<tr>
    <td colspan="2">'._G('DBMF_blockref_option_orderby').'</td>
    <td style="text-align: center;">
        <form action="?mod=dbmf3&amp;tpl=blockedit" method="post">
        <input type="hidden" name="blockref_edit" value="'.$blockref->id.'">
        <input type="hidden" name="blockref_switchorderby" value="1">';
        if($blockref->orderby==1) echo '
        <input type="submit" value="'._G('DBMF_blockref_orderby_ok').'">';
        else echo '
        <input type="submit" value="'._G('DBMF_blockref_orderby_no').'">';
        echo '
        </form>
    </td>
</tr>
<tr>
    <td colspan="2">'._G('DBMF_blockref_option_alwaysshown').'</td>
    <td style="text-align: center;">
        <form action="?mod=dbmf3&amp;tpl=blockedit" method="post">
        <input type="hidden" name="blockref_edit" value="'.$blockref->id.'">
        <input type="hidden" name="blockref_setalwaysshown" value="1">';
        echo '
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
        <input type="submit" value="'._G('DBMF_blockref_alwaysshown_submit').'">';
        echo '
        </form>
    </td>
</tr>';

if($blockref->type==MYSB_VALUE_TYPE_VARCHAR64_SELECT) {
    $req_options = MySBDB::query("SELECT * from ".MySB_DBPREFIX."valueoptions ".
        "WHERE value_keyname='".$blockref->grp."-".$blockref->keyname."' ".
        "ORDER BY value0",
        "MySBDBMFBlockRef::htmlOptionFormTR()");
    while($option = MySBDB::fetch_array($req_options)) {
        echo '
<tr>
    <td>Option '.$option['value0'].'</td>
    <td>
        <form action="?mod=dbmf3&amp;tpl=blockedit" method="post">
        <input type="text" name="blockref_mod_option" value="'.$option['value1'].'">
        <input type="hidden" name="blockref_option_id" value="'.$option['value0'].'">
        <input type="hidden" name="blockref_edit" value="'.$blockref->id.'">
        <input type="submit" value="'._G('DBMF_blockref_mod_option').'">
        </form>
    </td>
    <td style="width: 50px;">
        <form action="?mod=dbmf3&amp;tpl=blockedit" method="post">
        <input type="hidden" name="blockref_del_option" value="'.$option['value1'].'">
        <input type="hidden" name="blockref_edit" value="'.$blockref->id.'">
        <input type="hidden" name="blockref_option_id" value="'.$option['value0'].'">
        <input type="submit" value="'._G('DBMF_blockref_del_option').'">
        </form>
    </td>
</tr>';
    }
    echo '
<tr>
    <td>'._G('DBMF_blockref_newoption').'</td>
    <td colspan="2">
        <form action="?mod=dbmf3&amp;tpl=blockedit" method="post">
        <input type="text" name="blockref_new_option" value="">
        <input type="hidden" name="blockref_edit" value="'.$blockref->id.'">
        <input type="submit" value="'._G('DBMF_blockref_add_option').'">
        </form>
    </td>
</tr>';
}

echo '
</tbody></table>
</div>
';
}


echo '

<h2>'._G('DBMF_currentblocks').'</h2>';

$blocks = MySBDBMFBlockHelper::load();
foreach($blocks as $block) {

    $group_edit = MySBGroupHelper::getByID($block->groupedit_id);
    echo '
<a name="a_block'.$block->id.'"></a>
<h3>'._G($block->lname).' <small><i>('.$group_edit->comments.')</i></small></h3>';

    if($block->isEditable()) {
    
        echo '
<div class="table_support">
<form action="?mod=dbmf3&amp;tpl=blockedit" method="post">
<table style="width: 40%;"><tbody>
<tr class="title">
    <td style="text-align: center;" colspan="2">'._G($block->lname).'</td>
</tr>
<tr>
    <td style="text-align: right; width: 50%;">id</td>
    <td>'.$block->id.'</td>
</tr>
<tr>
    <td style="text-align: right;">'._G('DBMF_block_lname').'</td>
    <td><input type="text" name="lname" value="'.$block->lname.'"></td>
</tr>
<tr>
    <td style="text-align: right;">'._G('DBMF_block_groupedit').'</td>
    <td>
        <select name="group_id">';

        $groups = MySBDBMFGroupHelper::load();
        foreach($groups as $group) {
            if($group->dbmf_priority>0) echo '
            <option value="'.$group->id.'" '.MySBUtil::form_isselected($group->id,$group_edit->id).'>'.$group->comments.'</option>';
        }
        
        echo '
        </select>
    </td>
</tr>
<tr>
    <td colspan="2" style="text-align: center;">
        <input type="hidden" name="block_edit" value="'.$block->id.'">
        <input type="submit" value="'._G('DBMF_block_edition').'">
    </td>
</tr>
</tbody></table>
</form>


<table><tbody>
<tr>
    <td style="text-align: center; width: 30px;">
        <form action="?mod=dbmf3&amp;tpl=blockedit#a_block'.$block->id.'" method="post">
        <input type="hidden" name="block_orderdown" value="'.$block->id.'">
        <input type="submit" value="&darr;">
        </form>
    </td>
    <td style="text-align: center; width: 30px;">
        <form action="?mod=dbmf3&amp;tpl=blockedit#a_block'.$block->id.'" method="post">
        <input type="hidden" name="block_orderup" value="'.$block->id.'">
        <input type="submit" value="&uarr;">
        </form>
    </td>
    <td style="text-align: left; width: 90px;">
        <small>(index: '.$block->i_index.')</small>
    </td>
    <td style="text-align: center; width: 30px;">
        <form action="?mod=dbmf3&amp;tpl=blockedit#a_block'.$block->id.'" method="post"
        OnSubmit="return mysb_confirm(\''.MySBUtil::str2strict(sprintf(_G('DBMF_confirm_block_delete'), $block->lname, $group_edit->comments )).'\')">
        <input type="hidden" name="block_del" value="'.$block->id.'">
        <input type="submit" value="'._G('DBMF_block_delete').'">
        </form>
    </td>
</tr>
</tbody></table>
<br>
<table style="width: 70%;"><tbody>
';

        foreach($block->blockrefs as $blockref) {

            if($blockref->status==MYSB_DBMF_BLOCKREF_STATUS_ACTIVE)
                $class_bref = '';
            else $class_bref = ' style="background: #bbbbbb;"';
            echo '
<tr '.$class_bref.'>
    <td style="width: 20px;">'.$blockref->keyname.'</td>
    <td>
        <b>'._G($blockref->lname).'</b>
    </td>
    <td style="width: 100px; text-align: center;">
        <form action="?mod=dbmf3&amp;tpl=blockedit" method="post">
        <input type="hidden" name="blockref_edit" value="'.$blockref->id.'">
        <input type="submit" value="'._G('DBMF_blockref_edition').'">
        </form>
    </td>
    <td style="width: 100px; text-align: center;">'.$blockref->getType().'</td>
    <td style="width: 50px; text-align: center;">
        <form action="?mod=dbmf3&amp;tpl=blockedit#a_block'.$block->id.'" method="post">
        <input type="hidden" name="blockref_orderdown" value="'.$blockref->id.'">
        <input type="submit" value="&darr;">
        </form>
    </td>
    <td style="width: 50px; text-align: center;">
        <form action="?mod=dbmf3&amp;tpl=blockedit#a_block'.$block->id.'" method="post">
        <input type="hidden" name="blockref_orderup" value="'.$blockref->id.'">
        <input type="submit" value="&uarr;">
        </form>
    </td>
    <td style="width: 100px; text-align: center;">
        <form action="?mod=dbmf3&amp;tpl=blockedit#a_block'.$block->id.'" method="post">
        <input type="hidden" name="blockref_switchactive" value="'.$blockref->id.'">';

            if($blockref->status==MYSB_DBMF_BLOCKREF_STATUS_ACTIVE) 
                echo '
        <input type="submit" value="'._G('DBMF_blockref_desactive').'">';
            else 
                echo '
        <input type="submit" value="'._G('DBMF_blockref_active').'">';

            echo '
        </form>
    </td>
    <td style="width: 100px; text-align: center;">
        <form action="?mod=dbmf3&amp;tpl=blockedit#a_block'.$block->id.'" method="post"
        OnSubmit="return mysb_confirm(\''.MySBUtil::str2strict(sprintf(_G('DBMF_confirm_blockref_delete'), $blockref->lname, $blockref->keyname )).'\')">
        <input type="hidden" name="block_id" value="'.$block->id.'">
        <input type="hidden" name="blockref_del" value="'.$blockref->id.'">
        <input type="submit" value="'._G('DBMF_blockref_delete').'">
        </form>
    </td>
</tr>
';
        }

        echo '
<tr>
    <td colspan="8" style="text-align: center;">
        <br>
        <form action="?mod=dbmf3&amp;tpl=blockedit" method="post">
        '._G('DBMF_blockref_name').': <input type="text" name="lname" value="">
        '._G('DBMF_blockref_type').': 
        <select name="type">
            <option value="'.MYSB_VALUE_TYPE_INT.'" >int</option>
            <option value="'.MYSB_VALUE_TYPE_BOOL.'" >bool</option>
            <option value="'.MYSB_VALUE_TYPE_VARCHAR64.'" >varchar(64)</option>
            <option value="'.MYSB_VALUE_TYPE_VARCHAR512.'" >varchar(512)</option>
            <option value="'.MYSB_VALUE_TYPE_TEXT.'" >text/varchar(512)</option>
            <option value="'.MYSB_VALUE_TYPE_VARCHAR64_SELECT.'" >select/varchar(64)</option>
            <option value="'.MYSB_VALUE_TYPE_TEL.'" >tel/varchar(64)</option>
            <option value="'.MYSB_VALUE_TYPE_URL.'" >url/varchar(128)</option>
        </select><br>
        <input type="hidden" name="blockref_add" value="'.$block->id.'">
        <input type="submit" value="'._G('DBMF_blockref_add').'">
        </form>
    </td>
</tr>

</tbody></table>
</div>
';
    }
}

echo '
<h2>'._G('DBMF_addblock').'</h2>
<div class="table_support">
<form action="?mod=dbmf3&amp;tpl=blockedit" method="post">
<p>
   '._G('DBMF_block_name').' <input type="text" name="addblock_name">
   <input type="hidden" name="block_add" value="1">
   <input type="submit" value="'._G('DBMF_block_add').'">
</p>
</form>
</div>
';

?>
