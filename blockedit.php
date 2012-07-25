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

echo '
<h1>'._G('DBMF_blocks_edit').'</h1>

<h2>'._G('DBMF_currentblocks').'</h2>';

$blocks = MySBDBMFBlockHelper::load();
foreach($blocks as $block) {
    $group_edit = MySBGroupHelper::getByID($block->groupedit_id);
    echo '
<h3>'.$block->lname.' <small><i>('.$group_edit->comments.')</i></small></h3>';
    if($block->isEditable()) 
        echo '
<form action="?mod=dbmf3&amp;tpl=blockedit" method="post">
<div class="table_support">
<center>
<table><tbody>
<tr class="title">
    <td align="right">id</td>
    <td>'.$block->id.'</td>
</tr>
<tr>
    <td align="right">'._G('DBMF_block_lname').'</td>
    <td><input type="text" name="lname" value="'.$block->lname.'"></td>
</tr>
<tr>
    <td align="right">'._G('DBMF_block_groupedit').'</td>
    <td>'.$group_edit->comments.'</td>
</tr>
<tr>
    <td colspan="2" align="center">
        <input type="hidden" name="block_edit" value="'.$block->id.'">
        <input type="submit" value="'._G('DBMF_block_edition').'" class="submit">
    </td>
</tr>
</tbody></table>
</center>
</div>
</form>';

    echo '
<div class="table_support">
<center>
<table width="70%"><tbody>
<tr class="title">
    <td width="20px">id</td>
    <td>'._G('DBMF_blockref_name').'</td>
    <td width="50px" align="center">'._G('DBMF_blockref_type').'</td>
    <td width="100px" align="center">'._G('DBMF_blockref_active').'</td>
    <td width="100px" align="center">'._G('DBMF_blockref_delete').'</td>
</tr>';
    foreach($block->blockrefs as $blockref) {
        if($blockref->status==MYSB_DBMF_BLOCKREF_STATUS_ACTIVE)
            $class_bref = '';
        else $class_bref = ' style="background: #bbbbbb;"';
        echo '
<tr '.$class_bref.'>
    <td>'.$blockref->id.'</td>
    <td>';
        
        if($block->isEditable()) {
            echo '
        <form action="?mod=dbmf3&amp;tpl=blockedit" method="post">
        <input type="text" name="lname" value="'.$blockref->lname.'">
        <input type="hidden" name="blockref_rename" value="'.$blockref->id.'">
        <input type="submit" value="'._G('DBMF_blockref_rename').'" class="submit">
        </form>';
        } else {
            echo '
        '.$blockref->lname.'';
        }
        echo '
    </td>
    <td align="center">'.$blockref->getType().'</td>
    <td align="center">';
        if($block->isEditable()) {
            echo '
        <form action="?mod=dbmf3&amp;tpl=blockedit" method="post">
        <input type="hidden" name="blockref_switchactive" value="'.$blockref->id.'">';
            if($blockref->status==MYSB_DBMF_BLOCKREF_STATUS_ACTIVE) 
                echo '
        <input type="submit" value="'._G('DBMF_blockref_desactive').'" class="submit">';
            else 
                echo '
        <input type="submit" value="'._G('DBMF_blockref_active').'" class="submit">';
            echo '
        </form>';
        } else {
            if($blockref->status==MYSB_DBMF_BLOCKREF_STATUS_ACTIVE) 
                echo '
        '._G('DBMF_blockref_actived').'';
            else 
                echo '
        '._G('DBMF_blockref_desactived').'';
        }
        echo '
    </td>
    <td align="center">';
        if($block->isEditable()) {
            echo '
        <form action="?mod=dbmf3&amp;tpl=blockedit" method="post">
        <input type="hidden" name="block_id" value="'.$block->id.'">
        <input type="hidden" name="blockref_del" value="'.$blockref->id.'">
        <input type="submit" value="'._G('DBMF_blockref_delete').'" class="submit">
        </form>';
        } else {
            echo _G('DBMF_block_noneditable');
        }
        echo '
    </td>
</tr>';
    }
    if($block->isEditable()) 
        echo '
<tr>
    <td colspan="5" align="center">
        <br>
        <form action="?mod=dbmf3&amp;tpl=blockedit" method="post">
        '._G('DBMF_blockref_name').': <input type="text" name="lname" value="">
        '._G('DBMF_blockref_type').': 
        <select name="type">
            <option value="'.MYSB_VALUE_TYPE_INT.'" >int</option>
            <option value="'.MYSB_VALUE_TYPE_BOOL.'" >bool</option>
            <option value="'.MYSB_VALUE_TYPE_VARCHAR64.'" >varchar(64)</option>
            <option value="'.MYSB_VALUE_TYPE_VARCHAR512.'" >varchar(512)</option>
            <option value="'.MYSB_VALUE_TYPE_LINES.'" >text/varchar(512)</option>
        </select><br>
        <input type="hidden" name="blockref_add" value="'.$block->id.'">
        <input type="submit" value="'._G('DBMF_blockref_add').'" class="submit">
        </form>
        <br>
    </td>
</tr>';

    echo '
</tbody></table>
</center>
</div>';
}

echo '
<h2>'._G('DBMF_addblock').'</h2>
<form action="?mod=dbmf3&amp;tpl=blockedit" method="post">
<p>
   '._G('DBMF_block_name').' <input type="text" name="addblock_name">
   <input type="hidden" name="block_add" value="1">
   <input type="submit" value="'._G('DBMF_block_add').'" class="submit">
</p>
</form>';

?>
