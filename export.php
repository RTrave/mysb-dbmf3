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
<h1>'._G('DBMF_export').'</h1>';

if( isset($_POST['dbmf_export_process'])
    or isset($_POST['dbmf_contact_delete'])) {
    echo '
<h2>'._G('DBMF_search_results').'</h2>';

    echo $app->dbmf_export_plugin->htmlResultOutput($app->dbmf_search_result);
    return;
}


echo '
<form enctype="multipart/form-data" action="index.php?mod=dbmf3&amp;tpl=export" method="post">

<h2>'._G('DBMF_export_contacts').'</h2>

<h3>'._G('DBMF_export_select_type').'</h3>

<p>';

echo '
<select name="export_plug" onChange="';
$exports = MySBDBMFExportHelper::load();
foreach($exports as $export) 
    echo 'hide(\'export_plug'.$export->id.'\');';
echo 'show(this.options[this.selectedIndex].value);">';
foreach($exports as $export) 
    echo '
    <option value="export_plug'.$export->id.'">'._G($export->name).'</option>';

echo '
</select>
<br><br>
<a id="params_show" onClick="show(\'params\');hide(\'params_show\');" class="button">'._G('DBMF_export_showparams').'</a>
</p>
<div id="params" style="display: none;">';

$hide_flag = '';
foreach($exports as $export) {
    echo '
<div id="export_plug'.$export->id.'" '.$hide_flag.'>
<h4>'._G('DBMF_export_param_for').' '._G($export->name).' ('._G($export->comments).')</h4>
'.$export->htmlParamForm().'

</div>';
    if($hide_flag=='') $hide_flag = ' style="display: none;"';
}

echo '
</div>
<h3>'._G('DBMF_export_blockscriteria').'</h3>
<div class="table_support" id="rsvpCriteria">
<table><tbody>

';

$blocks = MySBDBMFBlockHelper::load();
$blockn_flag = 0;
foreach($blocks as $block) {
    $group_edit = MySBGroupHelper::getByID($block->groupedit_id);
    if($blockn_flag==0 and $block->isViewable()) $blockn_flag = 1;
    elseif($block->isViewable()) {
        echo '
<tr>
    <td colspan="2" style="text-align: center; background: #c0c0c0;">
    <select name="block_andorflag_'.$block->id.'">
        <option value="or">OR</option>
        <option value="and">AND</option>
    </select>
    </td>
</tr>';
    }
    if($block->isViewable()) {
        echo '
<tr class="title" >
    <td colspan="2">';
        echo $block->htmlFormWhereClause('b').' ';
        echo _G($block->lname).' <small><i>('.$group_edit->comments.')</i></small></td>
</tr>';
        echo '
<tr>
    <td style="text-align: right;"><small>'._G('DBMF_request_blockref_and_or').'</small></td>
    <td>
        <small>
        <input type="radio" name="blockref_andorflag_'.$block->id.'" value="or" checked>OR 
        <input type="radio" name="blockref_andorflag_'.$block->id.'" value="and">AND<br>
        </small>
    </td>
</tr>';
        foreach($block->blockrefs as $blockref) {
            if($blockref->isActive()) {
                echo '
<tr>
    <td style="vertical-align: top; text-align: right;">'._G($blockref->lname).':</td>
    <td>';
                echo $blockref->htmlFormWhereClause('br');
                echo '
    </td>
</tr>';
            }
        }
    }
}

echo '
</tbody></table>
</div>
<p style="text-align: center;">
    <input type="hidden" name="dbmf_export_process" value="1">
    <input type="submit" value="'._G('DBMF_search_submit').'">
</p>
</form>';

?>
