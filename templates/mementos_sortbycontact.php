<?php 
/***************************************************************************
 *
 *   phpMySandBox/DBMF3 module - TRoman<roman.trave@abadcafe.org> - 2022
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License', or
 *   ('at your option) any later version.
 *
***************************************************************************/

// No direct access.
defined('_MySBEXEC') or die;

global $app;

if( !MySBRoleHelper::checkAccess('dbmf_user') ) return;

if( !isset($_SESSION["dbmf_memcatg_sort"]) )
    $_SESSION["dbmf_memcatg_sort"] = 0;
if( isset($_GET["sort"]) )
    $_SESSION["dbmf_memcatg_sort"] = $_GET["sort"];

if( !isset($_POST["dbmf_mementos_sortby"]) )
    $_POST["dbmf_mementos_sortby"] = '';

if( !isset($_POST["crit_keyname"]) )
    $_POST["crit_keyname"] = '';

$_SESSION["dbmf_memento_lastfilter"] = 'bycontact';

echo '
<div class="content list">

  <div class="row bg-primary">
    <div class="col-sm-12">
      <h1 class="t-left">'._G('DBMF_mementosbycontact').'</h1>
    </div>
  </div>';

echo '
  <form action="index.php?mod=dbmf3&amp;tpl=mementos&filter=bycontact" 
        method="post">
  <div class="row bg-primary-light">

<div class="row">
  <p class="col-sm-4">
    '._G('DBMF_mementos_sortby').':
  </p>
  <div class="col-sm-8">
    <select name="dbmf_mementos_sortby">
      <option value="0">'._G('DBMF_mementos_sortby_all').'</option>';
$memcatgs = MySBDBMFMementoCatgHelper::loadAvailable();
foreach( $memcatgs as $memcatg ) {
  echo '
      <option value="'.$memcatg->id.'" '.MySBUtil::form_isselected($memcatg->id,$_POST["dbmf_mementos_sortby"]).'>'.$memcatg->name.'</option>';
}
echo '
    </select>
  </div>
</div>
<div class="row">
  <p class="col-sm-4">
    '._G('DBMF_mementos_bycontact_info').'
  </p>
  <div class="col-sm-8">
    <select name="crit_keyname" onchange="changekey(this.value);">
      <option name="" value="">--</option>';

$blocks = MySBDBMFBlockHelper::load();
foreach($blocks as $block) {
  foreach($block->blockrefs as $blockref) {
    if($blockref->isActive()) {
      echo '
      <option value="'.$blockref->keyname.'" '.
        MySBUtil::form_isselected($blockref->keyname,$_POST['crit_keyname']).'>'._G($blockref->lname).'</option>';
    }
  }
}
echo '
    </select>
  </div>
</div>

<div  class="row" id="keysorting" 
      style="min-height: 68px; border: 1px white solid">
</div>';
echo '
<div class="row" style="padding-top: 15px;">
  <div class="col-sm-3"></div>
  <div class="col-sm-6">
    <input type="submit" class="btn-primary"
           value="'._G('DBMF_mementos_bycontact_showresults').'">
  </div>
  <div class="col-sm-3"></div>
</div>

  </div>
  </form>
';


$clause_where = '';
if(isset($_POST['crit_keyname']) and $_POST['crit_keyname']!='') {
  $contacts_sql = 'SELECT id,'.$_POST['crit_keyname'].' from '.MySB_DBPREFIX.'dbmfcontacts ';
  $clause_sortby = 'ORDER BY '.$_POST['crit_keyname'];
  $blockref = MySBDBMFBlockRefHelper::getByKeyname($_POST['crit_keyname']);
  $clause_where = $blockref->htmlProcessWhereClause('br_');
  if($clause_where!='')
    $clause_where = 'WHERE '.$clause_where.' ';
} else {
  $contacts_sql = 'SELECT id from '.MySB_DBPREFIX.'dbmfcontacts ';
  $clause_sortby = '';
}
$contacts_sql = $contacts_sql.$clause_where.$clause_sortby;
//echo $contacts_sql.'<br>';
$contacts_req = MySBDB::query($contacts_sql,
                  'mementos_bycontact_ctrl.php', true, 'dbmf3');

if(isset($_POST['dbmf_mementos_sortby'])) {
  $memcats_sort = $_POST['dbmf_mementos_sortby'];
} else
  $memcats_sort = 0;

$mementos_p = array();
while($contact_data = MySBDB::fetch_array($contacts_req)) {
  //echo $contact_data[0].',';
  $mementos_contact = MySBDBMFMementoHelper::load($contact_data[0]);
  foreach($mementos_contact as $memento) {
    if( $memcats_sort==0 || $memcats_sort==$memento->memcatg_id ) {
      if(isset($_POST['crit_keyname']) and $_POST['crit_keyname']!='') {
        $memento->crit_infos = _G($blockref->lname).' : '.$contact_data[1];
      }
      $mementos_p[] = $memento;
    }
  }
}
echo '
<div id="dbmfMementoList" class="content list">';

include(_pathI('mementos_list_ctrl','dbmf3'));

echo '
</div>

</div>

<script>
show("mementos_results");
function changekey(selvalue) {
    loadItem( "keysorting", "index.php?mod=dbmf3&inc=blockref_sorting&keyname="+selvalue+"&filter='.$_GET['filter'].'" );
}';
if(isset($_POST['crit_keyname']) and $_POST['crit_keyname']!='') 
  echo '
changekey("'.$_POST['crit_keyname'].'");';
echo '
</script>';


?>
