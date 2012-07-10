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
<div id="mysb_topadmin">
<div class="mysb_topadmin_menu">
<a href="index.php?mod=dbmf3&amp;tpl=admingroups">'._G('DBMF_admin_groups').'</a>
<a href="index.php?mod=dbmf3&amp;tpl=adminblocks">'._G('DBMF_admin_blocks').'</a>
<br>
</div>
</div>
<h1>'._G('DBMF_admin_groups').'</h1>

<h2>'._G('DBMF_listgroups').'</h2>
<div class="table_support">
<center>
<table width="50%"><tbody>
<tr class="title">
    <td>id</td>
    <td width="100px">'._G('DBMF_group_name').'</td>
    <td>'._G('DBMF_groups_comments').'</td>
    <td width="350">'._G('DBMF_groups_dbmfpriority').'</td>
</tr>';

$dbmf_groups = MySBDBMFGroupHelper::load();
foreach($dbmf_groups as $group) {
    echo '
<tr>
    <td>'.$group->id.'</td>
    <td>'.$group->name.'</td>
    <td>'.$group->comments.'</td>
    <td>
        <form action="index.php?mod=dbmf3&amp;tpl=admingroups" method="post">
        <select name="dbmf_priority">
            <option value="">Unactive</option>';
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
</div>';


?>
