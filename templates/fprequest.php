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

    $editor = new MySBEditor();
    echo $editor->init("simple");
    echo '
<h2>'._G('DBMF_search_results').'</h2>
<div id="results">';

    //$app->tpl_dbmf_searchresult = $app->dbmf_search_result;
    _incI('contacts_sort','dbmf3');

    echo '
</div>';
}

/*
if( isset($_POST['search_name']) ) $search_name = $_POST['search_name'];
else $search_name = '';
if( isset($_POST['search_all']) ) $search_all = $_POST['search_all'];
else $search_all = '';
if( isset($_POST['search_byid']) ) $search_byid = $_POST['search_byid'];
else $search_byid = '';
*/

if( !isset($_POST['search_type']) ) $_POST['search_type'] = 'lastname';
if( !isset($_POST['search_name']) ) $_POST['search_name'] = '';

echo '
<h2>'._G('DBMF_search').'</h2>

<form action="index.php" method="post">

<div class="paragraph">

<input type="radio" name="search_type" id="search_bylastname" value="lastname" '.MySBUtil::form_ischecked($_POST['search_type'],'lastname').'>
<label for="search_bylastname">'._G('DBMF_search_lastname').'</label>
<input type="radio" name="search_type" id="search_all_fields" value="all_fields" '.MySBUtil::form_ischecked($_POST['search_type'],'all_fields').'>
<label for="search_all_fields">'._G('DBMF_search_all_fields').'</label>
<input type="radio" name="search_type" id="search_byid" value="byid" '.MySBUtil::form_ischecked($_POST['search_type'],'byid').'>
<label for="search_byid">'._G('DBMF_search_byid').'</label>
<br><br>
<input type="text" name="search_name" value="'.$_POST['search_name'].'" class="smart">
<br><br>
<input type="hidden" name="dbmf_request" value="1">
<input type="submit" value="'._G('DBMF_search_submit').'">
<br>

</div>

</form>
';


?>
