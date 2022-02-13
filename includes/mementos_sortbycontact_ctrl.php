<?php
/***************************************************************************
 *
 *   phpMySandBox/DBMF3 module - TRoman<abadcafe@free.fr> - 2022
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


echo '
<div class="content list">
<div class="row bg-primary">
  <div class="col-sm-12">
    <h1 class="t-left">'._G('DBMF_mementosbycontact').'</h1>
  </div>
</div>';

if( isset($_GET['filter']) and $_GET['filter']=='all' ) {
    $mementos_p = MySBDBMFMementoHelper::load(null,$_SESSION["dbmf_memcatg_sort"]);
    $_SESSION["dbmf_memento_lastfilter"] = 'all';
    $mementos_title = _G('DBMF_mementos_all').' ('.count($mementos_p).')';
#     echo '
# <h2 class="border-top">'._G('DBMF_mementos_all').' ('.count($mementos_p).')</h2>';
} else {
    $_GET['filter']='';
    $_SESSION["dbmf_memento_lastfilter"] = '';
    $mementos_p = MySBDBMFMementoHelper::loadActives($_SESSION["dbmf_memcatg_sort"]);
    $mementos_title = _G('DBMF_mementos_actives').' ('.count($mementos_p).')';
#     echo '
# <h2 class="border-top">'._G('DBMF_mementos_actives').' ('.count($mementos_p).')</h2>';
}

echo '
<form action="index.php?mod=dbmf3&amp;tpl=mementos_bycontact&filter=bycontact" method="post">
<div class="row bg-primary-light">
  <div class="row label">
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
  <div class="row" id="keysorting" style="min-height: 68px; border: 1px white solid">
    <!--<div class="col-sm-8" id="keysorting">-->';
echo '
      <!--
      <input type="text" name="crit_value" id="crit_value"
             maxlength="64" value="">
      -->
    <!--</div>-->
  </div>
  <div class="row border-top">
    <div class="col-sm-3"></div>
    <div class="col-sm-6">
      <input type="submit" class="btn-primary"
             value="'._G('DBMF_mementos_bycontact_showresults').'">
    </div>
    <div class="col-sm-3"></div>
  </div>
</div>
</form>


<div class="row bg-primary-light" style="margin-top: 25px;">
  <div class="col-sm-4">
<!--
    <h1 class="t-left">'.$mementos_title.'</h1>
-->
  </div>
  <div class="col-sm-8">
    <div class="sort_actions">
<!--
      '._G('DBMF_mementos_sortby').':
      <select name="dbmf_mementos_sortby" onchange="changesort(this.value);">
        <option value="0">'._G('DBMF_mementos_sortby_all').'</option>';
$memcatgs = MySBDBMFMementoCatgHelper::loadAvailable();
foreach( $memcatgs as $memcatg ) {
  echo '
        <option value="'.$memcatg->id.'" '.MySBUtil::form_isselected($memcatg->id,$_SESSION["dbmf_memcatg_sort"]).'>'.$memcatg->name.'</option>';
}
echo '
      </select><br>
-->
    <label for="expand" style="display:inline;">'._G("DBMF_memento_expand").':</label>
    <input type="checkbox" id="expand" name="feature"
           value="expand" onclick="checkExpand(this)"/>
    </div>
  </div>
</div>


<script>
function checkExpand(checkbox)
{
  var xinfos = document.getElementsByClassName("infos");
  var xdates = document.getElementsByClassName("date");
  var i;
  if (checkbox.checked)
  {
    for (i = 0; i < xinfos.length; i++) {
      xinfos[i].className += " expanded";
    }
    for (i = 0; i < xdates.length; i++) {
      xdates[i].className += " expanded";
    }
  } else {
    for (i = 0; i < xinfos.length; i++) {
      xinfos[i].className = "infos";
    }
    for (i = 0; i < xdates.length; i++) {
      xdates[i].className = "date";
    }
  }
}
</script>';

//$memento_list = [];
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
  //if(isset($_POST['crit_value']) and $_POST['crit_value']!='')
  //  $clause_where = 'WHERE ('.$_POST['crit_keyname'].'="'.$_POST['crit_value'].'") ';
}
$contacts_sql = $contacts_sql.$clause_where.$clause_sortby;
//echo $contacts_sql.'<br>';
$contacts_req = MySBDB::query($contacts_sql,
                  'mementos_bycontact_ctrl.php', true, 'dbmf3');

$mementos_p = array();
while($contact_data = MySBDB::fetch_array($contacts_req)) {
  //echo $contact_data[0].',';
  $mementos_contact = MySBDBMFMementoHelper::load($contact_data[0]);
  foreach($mementos_contact as $memento) {
    if(isset($_POST['crit_keyname']) and $_POST['crit_keyname']!='') {
      $memento->crit_infos = _G($blockref->lname).' : '.$contact_data[1];
    }
    $mementos_p[] = $memento;
  }
}
//MySBDBMFMementoHelper::loadContactInfos($mementos_p);
echo '
<div id="dbmfMementoList" class="content list">';

$memento_type = -1;

foreach($mementos_p as $memento) {
/*
  if($memento->type!=$memento_type) {
    $memento_type = $memento->type;
    if($memento_type==0) $h3 = 'DBMF_memento_type_punctual';
    elseif($memento_type==1) $h3 = 'DBMF_memento_type_monthofyear';
    echo '
<h2>'._G($h3).'</h2>';
  }
*/

  # if($memento->isActive()) $Active = true;
  # else $Active = false;
  # if($Active) $memclass = 'mem_active';
  # elseif(!$Active and $memento->date_process!='') $memclass = 'mem_processed';
  # else $memclass='';
    echo '
  <div class="content list slide slide-toggled" id="memento'.$memento->id.'">';

    $app->tpl_dbmf_currentmemento = $memento;
    include( _pathI('memento_display_ctrl','dbmf3') );

    echo '
  </div>';

}

if(count($mementos_p)==0)
  echo _G("DBMF_mementos_bycontact_noresults");

echo '
</div>

</div>

<script>
show("mementos_results");
function changesort(selvalue) {
    loadItem( "mementos_results", "index.php?mod=dbmf3&inc=mementos_sortbycontact&sort="+selvalue+"&filter='.$_GET['filter'].'" );
}
function changekey(selvalue) {
    loadItem( "keysorting", "index.php?mod=dbmf3&inc=blockref_sorting&keyname="+selvalue+"&filter='.$_GET['filter'].'" );
}
changekey("'.$_POST['crit_keyname'].'");
</script>';


?>
