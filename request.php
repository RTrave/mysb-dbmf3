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

if(!MySBRoleHelper::checkAccess('dbmf_user')) return;

echo '
<h1>'._G('DBMF_request_title').'</h1>

<h2>'._G('DBMF_search').'</h2>

<h3>'._G('DBMF_search_keyword').'</h3>
<form action="" method="post">
<ul>
<li>
    '._G('DBMF_search_lastname').': <br>
    <input type="text" name="search_name" size="24" maxlength="64" value="'.$_POST['search_name'].'">
</li>
<li>
    '._G('DBMF_search_all_fields').': <br>
    <input type="text" name="search_all" size="24" maxlength="64" value="'.$_POST['search_all'].'">
</li>
</ul>
<p>
<input type="hidden" name="dbmf_request" value="1">
<input type="submit" value="'._G('DBMF_search_submit').'" class="submit">
</p>
</form>
';

echo '
<h3>'._G('DBMF_search_advanced').'</h3>

<form action="" method="post">
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
<tr>
    <td colspan="2" align="center">
        <input type="hidden" name="dbmf_request_advanced" value="1">
        <input type="submit" value="'._G('DBMF_search_submit').'" class="submit">
    </td>
</tr>
</tbody></table>
</div>
</form>
';

if(isset($_POST['dbmf_request']) or isset($_POST['dbmf_request_advanced'])) {
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
