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
';

if( isset($_POST['dbmf_request']) 
    or isset($_POST['dbmf_request_advanced'])
    or isset($_POST['dbmf_request_byid'])
    or isset($_POST['dbmf_contact_delete']) ) {
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

if( isset($_POST['search_name']) ) $search_name = $_POST['search_name'];
else $search_name = '';
if( isset($_POST['search_all']) ) $search_all = $_POST['search_all'];
else $search_all = '';
if( isset($_POST['search_byid']) ) $search_byid = $_POST['search_byid'];
else $search_byid = '';

echo '
<h2>'._G('DBMF_search').'</h2>

<h3>'._G('DBMF_search_keyword').'</h3>

<form action="index.php" method="post">

<div class="table_support">
<table style="border: 0px; width: 90%;">
<tr>
    <td class="lpadding">
        '._G('DBMF_search_lastname').': <br>
        <input type="text" name="search_name" size="24" maxlength="64" value="'.$search_name.'">
    </td>
    <td class="lpadding">
        '._G('DBMF_search_all_fields').': <br>
        <input type="text" name="search_all" size="24" maxlength="64" value="'.$search_all.'">
    </td>
    <td class="lpadding">
        '._G('DBMF_search_byid').': <br>
        <input type="text" name="search_byid" size="8" maxlength="64" value="'.$search_byid.'">
    </td>
</tr>
<tr>
    <td colspan="3" style="text-align: center;">
        <input type="hidden" name="dbmf_request" value="1">
        <input type="submit" value="'._G('DBMF_search_submit').'">
    </td>
</tr>
</table>
</div>

</form>
';


?>
