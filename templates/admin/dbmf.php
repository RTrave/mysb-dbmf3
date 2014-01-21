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


_incI('admin_menu');

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

<h3>'._G('DBMF_listgroups').'</h3>
<div class="table_support">
<center>
<table width="50%"><tbody>
<tr class="title">
    <td>id</td>
    <td width="100px">'._G('DBMF_group_name').'</td>
    <td>'._G('DBMF_groups_comments').'</td>
    <td width="250px" align="center">'._G('DBMF_groups_dbmfpriority').'</td>
</tr>';

$dbmf_groups = MySBDBMFGroupHelper::load();
foreach($dbmf_groups as $group) {
    if($group->dbmf_priority=='' or $group->dbmf_priority==0) 
        $class_group = ' style="background: #bbbbbb;"';
    else 
        $class_group = '';
    echo '
<tr '.$class_group.'>
    <td>'.$group->id.'</td>
    <td>'.$group->name.'</td>
    <td>'.$group->comments.'</td>
    <td align="center">
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
    </td>
</tr>
';
}
echo '
</tbody></table>
</center>
</div>

</div>

<h2>'._G('DBMF_admin_exports').'</h2>';

if(isset($_POST['dbmf_editexport'])) {
    $export = MySBDBMFExportHelper::getByID($_POST['dbmf_editexport']);
    echo '
<h3>'._G('DBMF_editexport').'</h3>

<form action="index.php?mod=dbmf3&amp;tpl=admin/dbmf" method="post">

<div class="table_support">
<center>
<table><tbody>
<tr>
    <td>'._G('DBMF_export_name').'</td>
    <td><input type="text" name="export_name" value="'.$export->name.'"></td>
</tr>
<tr>
    <td>'._G('DBMF_export_comments').'</td>
    <td><input type="text" name="export_comments" value="'.$export->comments.'"></td>
</tr>
<tr>
    <td>'._G('DBMF_export_type').'</td>
    <td>';
    $pluginsExport = MySBPluginHelper::loadByType('DBMFExport');
    foreach($pluginsExport as $plugin) 
        if($plugin->value0==$export->type) break;
    echo $plugin->value1.'</td>
</tr>
<tr>
    <td>'._G('DBMF_export_group').'</td>
    <td>
    <select name="export_groupid" >';
    foreach($dbmf_groups as $group) {
        echo '
        <option value="'.$group->id.'" '.MySBUtil::form_isselected($group->id,$export->group_id).'>'.$group->comments.'</option>';
    }
    echo '
    </select>    
    </td>
</tr>
<tr>
    <td>'._G('DBMF_export_config').'</td>
    <td>';
    echo '
        '.$export->htmlConfigForm();
    echo '
    </td>
</tr>
<tr>
    <td colspan="2" style="text-align: center;">
        <input type="hidden" name="dbmf_editexport_process" value="'.$export->id.'">
        <input type="submit" value="'._G('DBMF_editexport_submit').'">
    </td>
</tr>
</tbody></table>
</center>
</div>';
}

echo '
</form><h3>'._G('DBMF_listexports').'</h3>
<div class="table_support">
<center>
<table width="80%"><tbody>
<tr class="title">
    <td width="20px">id</td>
    <td width="100px">'._G('DBMF_export_name').'</td>
    <td width="100px">'._G('DBMF_export_comments').'</td>
    <td width="50px">'._G('DBMF_export_type').'</td>
    <td width="100px">'._G('DBMF_export_group').'</td>
    <td align="center">'._G('DBMF_export_config').'</td>
    <td width="50px" align="center">'._G('DBMF_edition').'</td>
</tr>';

$exports = MySBDBMFExportHelper::load();
foreach($exports as $export) {
    $group = MySBGroupHelper::getByID($export->group_id);
    echo '
<tr>
    <td>'.$export->id.'</td>
    <td>'.$export->name.'</td>
    <td>'.$export->comments.'</td>
    <td>'.$export->type.'</td>
    <td>'.$group->comments.'</td>
    <td>'.$export->displayConfig().'</td>
    <td>
    <form action="index.php?mod=dbmf3&amp;tpl=admin/dbmf" method="post">
        <input type="hidden" name="dbmf_editexport" value="'.$export->id.'">
        <input type="submit" value="'._G('DBMF_editexport').'">
    </form>
    </td>
</tr>';
}

echo '
</tbody></table>
</center>
</div>

<h3>'._G('DBMF_addexports').'</h3>

<form action="index.php?mod=dbmf3&amp;tpl=admin/dbmf" method="post">

<div class="table_support">
<center>
<table><tbody>
<tr>
    <td>'._G('DBMF_export_name').'</td>
    <td><input type="text" name="export_name" value=""></td>
</tr>
<tr>
    <td>'._G('DBMF_export_comments').'</td>
    <td><input type="text" name="export_comments" value=""></td>
</tr>
<tr>
    <td>'._G('DBMF_export_type').'</td>
    <td>
    <select name="export_type">';
$pluginsExport = MySBPluginHelper::loadByType('DBMFExport');
foreach($pluginsExport as $plugin) 
    echo '
        <option value="'.$plugin->value0.'">'.$plugin->value1.'</option>';
echo '
    </select>    
    </td>
</tr>
<tr>
    <td>'._G('DBMF_export_group').'</td>
    <td>
    <select name="export_groupid">';

foreach($dbmf_groups as $group) {
    echo '
        <option value="'.$group->id.'">'.$group->comments.'</option>';
}

echo '
    </select>    
    </td>
</tr>
<tr>
    <td colspan="2" style="text-align: center;">
        <input type="hidden" name="dbmf_addexport" value="1">
        <input type="submit" value="'._G('DBMF_addexports').'">
    </td>
</tr>
</tbody></table>
</center>
</div>

</form>';

echo '
<a name="orphans"></a>
<h3>'._G('DBMF_orphans').'</h3>';

if(!isset($app->dbmf_search_result)) echo '
<form action="index.php?mod=dbmf3&amp;tpl=admin/dbmf#orphans" method="post">
<p>Search orphans contacts:
    <input type="hidden" name="dbmf_orphans" value="1">
    <input type="submit" value="'._G('DBMF_orphans_search').'">
</p>
</form>';
else {

    echo '
<p>
'.MySBEditor::init("simple").'
'.MySBDB::num_rows($app->dbmf_search_result).' results<br>
</p>';
    $app->tpl_dbmf_searchresult = $app->dbmf_search_result;
    _incT('contacts_display','dbmf3');

}

?>
