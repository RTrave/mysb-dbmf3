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


echo '
<div class="content list">';

if( isset($_GET['filter']) and $_GET['filter']=='all' ) {
    $_SESSION["dbmf_memento_lastfilter"] = 'all';
    $mementos_title = _G('DBMF_mementos_all').' '._G('DBMF_mementos_bydate');
} else {
    $_GET['filter']='';
    $_SESSION["dbmf_memento_lastfilter"] = '';
    $mementos_title = _G('DBMF_mementos_actives').' '._G('DBMF_mementos_bydate');
}

echo '
<div class="row bg-primary-light">
  <div class="col-sm-6">
    <h1 class="t-left">'.$mementos_title.'</h1>
  </div>
  <div class="col-sm-6">
    <div class="sort_actions">
      '._G('DBMF_mementos_sortby').':
      <select name="dbmf_mementos_sortby" onchange="changesort(this.value);">
        <option value="0">'._G('DBMF_mementos_sortby_all').'</option>';
$memcatgs = MySBDBMFMementoCatgHelper::loadAvailable();
foreach( $memcatgs as $memcatg ) {
  echo '
        <option value="'.$memcatg->id.'" '.MySBUtil::form_isselected($memcatg->id,$_SESSION["dbmf_memcatg_sort"]).'>'.$memcatg->name.'</option>';
}
echo '
      </select>
    </div>
  </div>
</div>';



echo '
<div id="dbmfMementoList" class="content list">';

include(_pathI('mementos_list_ctrl','dbmf3'));

echo '
</div>

</div>

<script>
show("mementos_results");
function changesort(selvalue) {
    loadItem( "dbmfMementoList", "index.php?mod=dbmf3&inc=mementos_list&sort="+selvalue+"&filter='.$_GET['filter'].'" );
}
function changesort1(selvalue) {
    loadItem( "mementos_results", "index.php?mod=dbmf3&inc=mementos_sort&sort="+selvalue+"&filter='.$_GET['filter'].'" );
}
</script>';


?>
