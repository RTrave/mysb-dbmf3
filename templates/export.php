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

if( isset($_POST['dbmf_export_process']) ) {
    echo '
<h2>'._G('DBMF_search_results').'</h2>';

    echo $app->dbmf_export_plugin->htmlResultOutput();
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
<a id="params_show" onClick="toggle_slide(\'params\');hide(\'params_showA\');" class="button">'._G('DBMF_export_showparams').'</a><br>
<br></p>
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

<div class="list_support" id="rsvpCriteria">

<div class="boxed" 
     style="width: 450px; margin: 10px auto 3px;">';

$blocks = MySBDBMFBlockHelper::load();
$blockn_flag = 0;
foreach($blocks as $block) {
    $group_edit = MySBGroupHelper::getByID($block->groupedit_id);
    if($blockn_flag==0 and $block->isViewable()) $blockn_flag = 1;
    elseif($block->isViewable()) {
        echo '
    <div class="row" style="text-align: center; background: transparent; border: 0px; padding-bottom: 0px;">
    <select name="block_andorflag_'.$block->id.'">
        <option value="or">OR</option>
        <option value="and">AND</option>
    </select>
    </div>';
    }
    if($block->isViewable()) {
        echo '
    <div class="title roundtop">
            '.$block->htmlFormWhereClause('b').'<b>'._G($block->lname).'</b>
            <small><i>('.$group_edit->comments.')</i></small>
            <div style="float: right; cursor: pointer;">
                <img src="images/icons/go-down.png" alt="go-down"
                     onClick="toggle_slide(\'block_select_'.$block->id.'\');"></div>
    </div>
    <div id="block_select_'.$block->id.'" style="display: none; width: 100%;">
    <div class="row" style="text-align: center;">
        '._G('DBMF_request_blockref_and_or').'
        <input type="radio" name="blockref_andorflag_'.$block->id.'" value="or" checked>OR 
        <input type="radio" name="blockref_andorflag_'.$block->id.'" value="and">AND<br>
    </div>';
        foreach($block->blockrefs as $blockref) {
            if($blockref->isActive()) {
                echo '
    <div class="row">
        <div class="right">'.$blockref->htmlFormWhereClause('br').'</div>
        '._G($blockref->lname).'
    </div>';
            }
        }
        echo '
    </div>';
    }
}

echo '
</div>
</div>

<p style="text-align: center;">
    <input type="hidden" name="dbmf_export_process" value="1">
    <input type="submit" value="'._G('DBMF_search_submit').'">
</p>
</form>';

?>
