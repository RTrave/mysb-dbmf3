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
<h1>'._G('DBMF_admin_blocks').'</h1>

<h2>'._G('DBMF_currentblocks').'</h2>
<div class="table_support">
<center>
<table width="50%"><tbody>
<tr class="title">
    <td>id</td>
    <td>'._G('DBMF_block_name').'</td>
    <td>'._G('DBMF_block_lname').'</td>
    <td>'._G('DBMF_block_edition').'</td>
    <td>'._G('DBMF_block_remove').'</td>
</tr>';

$blocks = MySBDBMFBlockHelper::load();
foreach($blocks as $block) {
    echo '
<tr>
    <td>'.$block->id.'</td>
    <td>'.$block->name.'</td>
    <td>'.$block->lname.'</td>
    <td>'.'</td>
    <td>'.'</td>
</tr>';
}

echo '
</tbody></table>
</center>
</div>';

echo '
<h2>'._G('DBMF_addblock').'</h2>
<form action="?mod=dbmf3&amp;tpl=adminblocks" method="post">
<p>
   '._G('DBMF_block_name').' <input type="text" name="addblock_name">
   <input type="hidden" name="block_add" value="1">
   <input type="submit" value="'._G('DBMF_block_add').'">
</p>
</form>';

?>
