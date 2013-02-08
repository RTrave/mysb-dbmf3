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

echo '
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
<li>
    '._G('DBMF_search_byid').': <br>
    <input type="text" name="search_byid" size="8" maxlength="64" value="'.$_POST['search_byid'].'">
</li>
</ul>
<p>
<input type="hidden" name="dbmf_request" value="1">
<input type="submit" value="'._G('DBMF_search_submit').'" class="submit">
</p>
</form>
';


?>
