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

if( isset($_POST['dbmf_request'])
    or isset($_POST['dbmf_request_advanced'])
    or isset($_POST['dbmf_request_byid'])
    or isset($_POST['dbmf_contact_delete']) ) {

    $editor = new MySBEditor();
    echo $editor->init("simple");
    echo '
<div class="col-lg-10 col-unique">
<div class="content list" id="search">
<h1>'._G('DBMF_search_results').'</h1>
<div id="contacts_results" class="slide slide-toggled">';

    //$app->tpl_dbmf_searchresult = $app->dbmf_search_result;
    include( _pathI('contacts_sort_ctrl','dbmf3') );

    echo '
</div>
</div>
</div>';

  $req_autofocus = "";
} else {
  $req_autofocus = "autofocus";
}

if( !isset($_POST['search_type']) ) $_POST['search_type'] = 'all_fields';
if( !isset($_POST['search_name']) ) $_POST['search_name'] = '';

echo '
<div class="col-md-8 col-unique">
<div class="content">
<form action="index.php" method="post">

  <h1>'._G('DBMF_request_title').'</h1>

  <div class="row radio-list t-center">
    <label for="search_bylastname">
      <input type="radio" name="search_type" id="search_bylastname" value="lastname"
             '.MySBUtil::form_ischecked($_POST['search_type'],'lastname').'>
      '._G('DBMF_search_lastname').' '._G('DBMF_common_lastname').'
    </label>
    <label for="search_all_fields">
      <input type="radio" name="search_type" id="search_all_fields" value="all_fields"
             '.MySBUtil::form_ischecked($_POST['search_type'],'all_fields').'>
      '._G('DBMF_search_all_fields').'
    </label>
    <label for="search_byid">
      <input type="radio" name="search_type" id="search_byid" value="byid"
               '.MySBUtil::form_ischecked($_POST['search_type'],'byid').'>
      '._G('DBMF_search_byid').'
    </label>
  </div>

  <div class="row label">
    <label class="col-sm-6" for="search_name">'._G('DBMF_search').'</label>
    <div class="col-sm-6">
      <input  type="text" name="search_name" id="search_name"
              value="'.$_POST['search_name'].'"
              '.$req_autofocus.'>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-3"></div>
    <div class="col-sm-6">
      <input type="hidden" name="dbmf_request" value="1">
      <input type="submit" class="btn-primary"
             value="'._G('DBMF_search_submit').'">
    </div>
    <div class="col-sm-3"></div>
  </div>
</form>
</div>
</div>';

?>
