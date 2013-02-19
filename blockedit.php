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
<center>
<table width="50%"><tbody>
<tr class="title">
    <td align="center" colspan="2">'._G($blockref->lname).'('.$blockref->keyname.')</td>
</tr>
<tr>
    <td width="50%" align="right">'._G('DBMF_blockref_lname').'</td>
    <td><input type="text" name="lname" value="'.$blockref->lname.'"></td>
</tr>
<tr>
    <td width="50%" align="right">'._G('DBMF_blockref_type').'</td>
    <td>'.$blockref->getType().'</td>
</tr>
<tr>
    <td colspan="2" align="center">
        <input type="hidden" name="blockref_edit_process" value="'.$blockref->id.'">
        <input type="submit" value="'._G('DBMF_blockref_edition_process').'" class="submit"><br>
    </td>
</tr>
</tbody></table>
</center>
</form>

<center>
<table width="50%"><tbody>
<tr class="title">
    <td align="center" colspan="3">Options</td>
</tr>
<tr>
    <td colspan="2">'._G('DBMF_blockref_option_orderby').'</td>
    <td align="center">
        <form action="?mod=dbmf3&amp;tpl=blockedit" method="post">
        <input type="hidden" name="blockref_edit" value="'.$blockref->id.'">
        <input type="hidden" name="blockref_switchorderby" value="1">';
        if($blockref->orderby==1) echo '
        <input type="submit" value="'._G('DBMF_blockref_orderby_ok').'" class="submit">';
        else echo '
        <input type="submit" value="'._G('DBMF_blockref_orderby_no').'" class="submit">';
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
        <input type="submit" value="'._G('DBMF_blockref_mod_option').'" class="submit">
        </form>
    </td>
    <td width="50px">
        <form action="?mod=dbmf3&amp;tpl=blockedit" method="post">
        <input type="hidden" name="blockref_del_option" value="'.$option['value1'].'">
        <input type="hidden" name="blockref_edit" value="'.$blockref->id.'">
        <input type="hidden" name="blockref_option_id" value="'.$option['value0'].'">
        <input type="submit" value="'._G('DBMF_blockref_del_option').'" class="submit">
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
        <input type="submit" value="'._G('DBMF_blockref_add_option').'" class="submit">
        </form>
    </td>
</tr>';
}

echo '
</tbody></table>
</center>
</div>
';
}


echo '

<h2>'._G('DBMF_currentblocks').'</h2>';

$blocks = MySBDBMFBlockHelper::load();
foreach($blocks as $block) {

    $group_edit = MySBGroupHelper::getByID($block->groupedit_id);
    echo '
<a name="a_block'.$block->id.'">
<h3>'._G($block->lname).' <small><i>('.$group_edit->comments.')</i></small></h3>';

    if($block->isEditable()) {
    
        echo '
<div class="table_support">
<form action="?mod=dbmf3&amp;tpl=blockedit" method="post">
<center>
<table width="40%"><tbody>
<tr class="title">
    <td align="center" colspan="2">'._G($block->lname).'</td>
</tr>
<tr>
    <td width="50%" align="right">id</td>
    <td>'.$block->id.'</td>
</tr>
<tr>
    <td align="right">'._G('DBMF_block_lname').'</td>
    <td><input type="text" name="lname" value="'.$block->lname.'"></td>
</tr>
<tr>
    <td align="right">'._G('DBMF_block_groupedit').'</td>
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
    <td colspan="2" align="center">
        <input type="hidden" name="block_edit" value="'.$block->id.'">
        <input type="submit" value="'._G('DBMF_block_edition').'" class="submit">
    </td>
</tr>
</tbody></table>
</center>
</form>

<center>
<table><tbody>
<tr>
    <td align="center" width="30px">
        <form action="?mod=dbmf3&amp;tpl=blockedit#a_block'.$block->id.'" method="post">
        <input type="hidden" name="block_orderdown" value="'.$block->id.'">
        <input type="submit" value="&darr;" class="submit">
        </form>
    </td>
    <td align="center" width="30px">
        <form action="?mod=dbmf3&amp;tpl=blockedit#a_block'.$block->id.'" method="post">
        <input type="hidden" name="block_orderup" value="'.$block->id.'">
        <input type="submit" value="&uarr;" class="submit">
        </form>
    </td>
    <td align="left" width="90px">
        <small>(index: '.$block->i_index.')</small>
    </td>
    <td align="center" width="30px">
        <form action="?mod=dbmf3&amp;tpl=blockedit#a_block'.$block->id.'" method="post"
        OnSubmit="return mysb_confirm(\''.MySBUtil::str2strict(sprintf(_G('DBMF_confirm_block_delete'), $block->lname, $group_edit->comments )).'\')">
        <input type="hidden" name="block_del" value="'.$block->id.'">
        <input type="submit" value="'._G('DBMF_block_delete').'" class="submit">
        </form>
    </td>
</tr>
</tbody></table>
<br>
<table width="70%"><tbody>
';

        foreach($block->blockrefs as $blockref) {

            if($blockref->status==MYSB_DBMF_BLOCKREF_STATUS_ACTIVE)
                $class_bref = '';
            else $class_bref = ' style="background: #bbbbbb;"';
            echo '
<tr '.$class_bref.'>
    <td width="20px">'.$blockref->keyname.'</td>
    <td>
        <b>'._G($blockref->lname).'</b>
    </td>
    <td width="100px" align="center">
        <form action="?mod=dbmf3&amp;tpl=blockedit" method="post">
        <input type="hidden" name="blockref_edit" value="'.$blockref->id.'">
        <input type="submit" value="'._G('DBMF_blockref_edition').'" class="submit">
        </form>
    </td>
    <td width="100px" align="center">'.$blockref->getType().'</td>
    <td align="center" width="50px">
        <form action="?mod=dbmf3&amp;tpl=blockedit#a_block'.$block->id.'" method="post">
        <input type="hidden" name="blockref_orderdown" value="'.$blockref->id.'">
        <input type="submit" value="&darr;" class="submit">
        </form>
    </td>
    <td align="center" width="50px">
        <form action="?mod=dbmf3&amp;tpl=blockedit#a_block'.$block->id.'" method="post">
        <input type="hidden" name="blockref_orderup" value="'.$blockref->id.'">
        <input type="submit" value="&uarr;" class="submit">
        </form>
    </td>
    <td width="100px" align="center">
        <form action="?mod=dbmf3&amp;tpl=blockedit#a_block'.$block->id.'" method="post">
        <input type="hidden" name="blockref_switchactive" value="'.$blockref->id.'">';

            if($blockref->status==MYSB_DBMF_BLOCKREF_STATUS_ACTIVE) 
                echo '
        <input type="submit" value="'._G('DBMF_blockref_desactive').'" class="submit">';
            else 
                echo '
        <input type="submit" value="'._G('DBMF_blockref_active').'" class="submit">';

            echo '
        </form>
    </td>
    <td width="100px" align="center">
        <form action="?mod=dbmf3&amp;tpl=blockedit#a_block'.$block->id.'" method="post"
        OnSubmit="return mysb_confirm(\''.MySBUtil::str2strict(sprintf(_G('DBMF_confirm_blockref_delete'), $blockref->lname, $blockref->keyname )).'\')">
        <input type="hidden" name="block_id" value="'.$block->id.'">
        <input type="hidden" name="blockref_del" value="'.$blockref->id.'">
        <input type="submit" value="'._G('DBMF_blockref_delete').'" class="submit">
        </form>
    </td>
</tr>
';
        }

        echo '
<tr>
    <td colspan="8" align="center">
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
        </select><br>
        <input type="hidden" name="blockref_add" value="'.$block->id.'">
        <input type="submit" value="'._G('DBMF_blockref_add').'" class="submit">
        </form>
    </td>
</tr>

</tbody></table>
</center>
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
   <input type="submit" value="'._G('DBMF_block_add').'" class="submit">
</p>
</form>
</div>
';

?>
