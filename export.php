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
<h1>'._G('DBMF_export').'</h1>

<form action="?mod=dbmf3&amp;tpl=export" method="post">

<h2>'._G('DBMF_export_type').'</h2>

<h3>'._G('DBMF_export_select_type').'</h3>

<p>';

$exports = MySBDBMFExportHelper::load();
echo '
<select onChange="';
foreach($exports as $export) 
    echo 'hide(\'hide'.$export->id.'\');';
echo 'show(this.options[this.selectedIndex].value);">';
foreach($exports as $export) 
    echo '
    <option value="hide'.$export->id.'">'.$export->name.'</option>';

echo '
</select>
</p>';

$hide_flag = '';
foreach($exports as $export) {
    echo '
<div id="hide'.$export->id.'" '.$hide_flag.'>
<h3>'.$export->name.': configuration</h3>
<p>'.$export->comments.'</p>
</div>';
    if($hide_flag=='') $hide_flag = ' style="display: none;"';
}

echo '
<h2>'._G('DBMF_export_criteria').'</h2>

<h3>'._G('DBMF_export_generalcriteria').'</h3>
<p>
</p>

<h3>'._G('DBMF_export_blockscriteria').'</h3>
<div class="table_support" align="center">
<table><tbody>

';

$blocks = MySBDBMFBlockHelper::load();
$blockn_flag = 0;
foreach($blocks as $block) {
    $group_edit = MySBGroupHelper::getByID($block->groupedit_id);
    if($blockn_flag==0) $blockn_flag = 1;
    elseif($block->isEditable()) {
        echo '
<tr>
    <td colspan="2" style="text-align: center;">
    <select name="block_andorflag_'.$block->id.'">
        <option value="or">OR</option>
        <option value="and">AND</option>
    </select>
    </td>
</tr>';
    }
    echo '
<tr class="title" >
    <td colspan="2">';
    if($block->isEditable()) 
        echo $block->htmlFormWhereClause('b').' ';
    echo $block->lname.' <small><i>('.$group_edit->comments.')</i></small></td>
</tr>';
    if($block->isEditable()) {
        echo '
<tr>
    <td style="text-align: right;">'._G('DBMF_request_blockref_and_or').'</td>
    <td>
    <select name="blockref_andorflag_'.$block->id.'">
        <option value="or">OR</option>
        <option value="and">AND</option>
    </select>
    </td>
</tr>';
    }
    foreach($block->blockrefs as $blockref) {
        if($block->isEditable() and $blockref->isActive()) {
            $refname = 'br'.$blockref->id;
            echo '
<tr style="'.$class_edit.'">
    <td style="vertical-align: top; text-align: right;"><b>'.$blockref->lname.':</b></td>
    <td>';
            echo $blockref->htmlFormWhereClause('br',$contact->$refname);
            echo '
    </td>
</tr>';
        }
    }
}

echo '
</tbody></table>
</div>
<p style="text-align: center;">
    <input type="hidden" name="dbmf_export_process" value="1">
    <input type="submit" value="'._G('DBMF_search_submit').'" class="submit">
</p>
</form>';

if(isset($_POST['dbmf_export_process'])) {
    echo '
<h2>'._G('DBMF_search_results').'</h2>
<p>
'.MySBDB::num_rows($app->dbmf_search_result).' results<br>
</p>
';

    $app->tpl_dbmf_searchresult = $app->dbmf_search_result;
    _T('templates/contacts_display.php','dbmf3');

    echo '
';
}

?>
