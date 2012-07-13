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
<h1>'._G('DBMF_admin_dbmf').'</h1>

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
        <form action="index.php?mod=dbmf3&amp;tpl=admindbmf&amp;plg=admin" method="post">
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
        <input type="submit" value="'._G('DBMF_group_modify').'" class="submit">
        </form>
    </td>
</tr>
';
}
echo '
</tbody></table>
</center>
</div>';


?>
