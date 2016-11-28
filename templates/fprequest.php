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

if(!MySBRoleHelper::checkAccess('dbmf_user',false)) return;

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
<div id="contacts_results">';

    //$app->tpl_dbmf_searchresult = $app->dbmf_search_result;
    _incI('contacts_sort','dbmf3');

    echo '
</div>';
}

if( !isset($_POST['search_type']) ) $_POST['search_type'] = 'lastname';
if( !isset($_POST['search_name']) ) $_POST['search_name'] = '';

echo '
<form action="index.php" method="post">

<div class="list_support">
<div class="boxed" style="width: 500px;">
    <div class="title roundtop"><b>'._G('DBMF_search').'</b></div>
    <div class="row" style="text-align: center;">
        <input type="radio" name="search_type" id="search_bylastname" value="lastname" '.MySBUtil::form_ischecked($_POST['search_type'],'lastname').'>
        <label for="search_bylastname">'._G('DBMF_search_lastname').' '._G('DBMF_common_lastname').'</label>
        <input type="radio" name="search_type" id="search_all_fields" value="all_fields" '.MySBUtil::form_ischecked($_POST['search_type'],'all_fields').'>
        <label for="search_all_fields">'._G('DBMF_search_all_fields').'</label>
        <input type="radio" name="search_type" id="search_byid" value="byid" '.MySBUtil::form_ischecked($_POST['search_type'],'byid').'>
        <label for="search_byid">'._G('DBMF_search_byid').'</label>
    </div>
    <div class="row" style="text-align: center;">
        <input  type="text" 
                name="search_name" value="'.$_POST['search_name'].'" 
                class="smart"
                style="text-align: left;">
    </div>
    <div class="row" style="text-align: center;">
        <input type="hidden" name="dbmf_request" value="1">
        <input type="submit" value="'._G('DBMF_search_submit').'">
    </div>
</div>
</div>

</form>';


?>
