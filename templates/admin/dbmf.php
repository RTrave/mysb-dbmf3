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


_incI('admin/menu');

echo '
<h1>'._G('DBMF_admin_dbmf').'</h1>';

if(isset($_POST['dbmf_editexport'])) {
    echo '
<div id="hide_1"><h2><a onClick="hide(\'hide_1\');show(\'hide_2\');"><u>'._G('DBMF_admin_groups').'</u></a></h2></div>
<div id="hide_2" style="display: none;">';
} else {
    echo '
<div>';
}
echo '
<h2>'._G('DBMF_admin_groups').'</h2>

<div class="boxed">

<div class="title roundtop">
    <b>'._G('DBMF_listgroups').'</b>
</div>';
$dbmf_groups = MySBDBMFGroupHelper::load();
foreach($dbmf_groups as $group) {
    if($group->dbmf_priority=='' or $group->dbmf_priority==0)
        $style_group = ' style="background1: #bbbbbb;"';
    else
        $style_group = ' style="font-weight: bold;"';
    echo '
<div class="row" '.$style_group.'>
    <div class="right">
        <form action="index.php?mod=dbmf3&amp;tpl=admin/dbmf" method="post">
        <select name="dbmf_priority">
            <option value="">'._G('DBMF_groups_unused').'</option>';
    $i = 1;
    while($i<=5) {
        echo '
            <option value="'.$i.'" '.MySBUtil::form_isselected($i,$group->dbmf_priority).'>'._G('DBMF_group_priority').' '.$i.'</option>';
        $i++;
    }
    echo '
        </select>
        <input type="hidden" name="group_id" value="'.$group->id.'">
        <input type="submit" value="'._G('DBMF_group_modify').'">
        </form>
    </div>
    '.$group->comments.'<br>
    <span class="help">'.$group->name.'</span>
</div>';
}
echo '
</div>';

echo '
</div>';

echo '
<h2>'._G('DBMF_admin_exports').'</h2>';

if(isset($_POST['dbmf_editexport'])) {
    $export = MySBDBMFExportHelper::getByID($_POST['dbmf_editexport']);

    echo '
<div class="boxed">
<form action="index.php?mod=dbmf3&amp;tpl=admin/dbmf" method="post">

<div class="title roundtop">
    <b>'._G('DBMF_editexport').'</b>
</div>
<div class="row">
    <div class="right"><input type="text" name="export_name" value="'.$export->name.'"></div>
    '._G('DBMF_export_name').'
</div>
<div class="row">
    <div class="right"><input type="text" name="export_comments" value="'.$export->comments.'"></div>
    '._G('DBMF_export_comments').'
</div>
<div class="row">
    <div class="right">';
    $pluginsExport = MySBPluginHelper::loadByType('DBMFExport');
    foreach($pluginsExport as $plugin)
        if($plugin->value0==$export->type) break;
    echo $plugin->value1.'</div>
    '._G('DBMF_export_type').'
</div>
<div class="row">
    <div class="right">
    <select name="export_groupid" >';
    foreach($dbmf_groups as $group) {
        echo '
        <option value="'.$group->id.'" '.MySBUtil::form_isselected($group->id,$export->group_id).'>'.$group->comments.'</option>';
    }
    echo '
    </select>
    </div>
    '._G('DBMF_export_group').'
</div>
<div class="title">
    '._G('DBMF_export_config').'
</div>
<div class="row">
    '.$export->htmlConfigForm().'
</div>
<div class="title">
    <input type="hidden" name="dbmf_editexport_process" value="'.$export->id.'">
    <input type="submit" value="'._G('DBMF_editexport_submit').'">
</div>

</form>
</div>';
}

echo '
<div class="boxed">

<div class="title roundtop">
    <b>'._G('DBMF_listexports').'</b>
</div>';

$exports = MySBDBMFExportHelper::load();
foreach($exports as $export) {
    $group = MySBGroupHelper::getByID($export->group_id);
    echo '
<div class="row">
    <div class="right" style="text-align: right;">'.$export->type.'<br><span class="help">'.$group->comments.'</span></div>
    <div style="float: left;">
    <form action="index.php?mod=dbmf3&amp;tpl=admin/dbmf" method="post">
        <input type="hidden" name="dbmf_editexport" value="'.$export->id.'">
        <input src="images/icons/text-editor.png"
               type="image"
               alt="'._G('DBMF_editexport').' '.$export->name.'"
               title="'._G('DBMF_editexport').' '.$export->name.'">
    </form>
    </div>
    '.$export->name.'<br>
    <span class="help">'.$export->comments.'</span>
</div>';
}
echo '
</div>';

echo '
<div class="boxed">
<form action="index.php?mod=dbmf3&amp;tpl=admin/dbmf" method="post">

<div class="title roundtop">
    <b>'._G('DBMF_addexports').'</b>
</div>
<div class="row">
    <div class="right"><input type="text" name="export_name" value=""></div>
    '._G('DBMF_export_name').'
</div>
<div class="row">
    <div class="right"><input type="text" name="export_comments" value=""></div>
    '._G('DBMF_export_comments').'
</div>
<div class="row">
    <div class="right">
    <select name="export_type">';
$pluginsExport = MySBPluginHelper::loadByType('DBMFExport');
foreach($pluginsExport as $plugin)
    echo '
        <option value="'.$plugin->value0.'">'.$plugin->value1.'</option>';
echo '
    </select>
    </div>
    '._G('DBMF_export_type').'
</div>
<div class="row">
    <div class="right">
    <select name="export_groupid">';
foreach($dbmf_groups as $group)
    echo '
        <option value="'.$group->id.'">'.$group->comments.'</option>';
echo '
    </select>
    </div>
    '._G('DBMF_export_group').'
</div>
<div class="title">
    <input type="hidden" name="dbmf_addexport" value="1">
    <input type="submit" value="'._G('DBMF_addexports').'">
</div>

</form>
</div>';

echo '
<h3 id="orphans">'._G('DBMF_orphans').'</h3>';

if( !isset($_POST['dbmf_orphans']) ) echo '
<form action="index.php?mod=dbmf3&amp;tpl=admin/dbmf#orphans" method="post">
<div style="text-align: center;"><br>
    <input type="hidden" name="dbmf_orphans" value="1">
    <input type="submit" value="'._G('DBMF_orphans_search').'">
</div><br><br>
</form>';
else {
    $editor = new MySBEditor();
    echo '
'.$editor->init("simple").'
<div id="contacts_results">';
    //$app->tpl_dbmf_searchresult = $app->dbmf_search_result;
    _incI('contacts_sort','dbmf3');
    echo '
</div>';
}

echo '
<h3 id="autosubs">'._G('DBMF_autosubs_config').'</h3>
<form action="index.php?mod=dbmf3&amp;tpl=admin/dbmf#autosubs" method="post">';

$blockrefs = MySBDBMFBlockRefHelper::load();
foreach( $blockrefs as $blockref ) {
    echo '
<div style="display: inline-block; background: #dddddd; margin: 2px 3px; padding: 1px 3px;">
    <input name="'.$blockref->keyname.'" "'.MySBUtil::form_ischecked($blockref->autosubs,"1").'" type="checkbox">
    '._G($blockref->lname).'
</div>';
}
echo '<br>
    <input type="hidden" name="dbmf_autosubs" value="1">
    <input type="submit" value="'._G('DBMF_autosubs_configsubmit').'">
</form>

';

?>
