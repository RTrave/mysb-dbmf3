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

if( isset($_GET['filter']) and $_GET['filter']=='all' ) {
    $mementos_p = MySBDBMFMementoHelper::load(null,$_SESSION["dbmf_memcatg_sort"]);
    $_SESSION["dbmf_memento_lastfilter"] = 'all';
} elseif( isset($_GET['filter']) and $_GET['filter']!='bycontact' ) {
    $_GET['filter']='';
    $_SESSION["dbmf_memento_lastfilter"] = '';
    $mementos_p = MySBDBMFMementoHelper::loadActives($_SESSION["dbmf_memcatg_sort"]);
}

echo '

<div class="row bg-primary" style="margin-top: 25px;">
  <div class="col-sm-4">
  </div>
  <div class="col-sm-8 t-right">
    <label for="expand" style="display:inline;">
      '._G("DBMF_memento_expand").':
    </label>
    <input type="checkbox" id="expand" name="feature"
           value="expand" onclick="checkExpand(this)"/>
  </div>
</div>';

if( isset($_GET['filter']) and 
    ($_GET['filter']=='all' or $_GET['filter']=='bycontact'))
  echo '
<div class="row bg-primary" style="margin-top: 0;">
  <div class="col-sm-4 t-right">
     <label for="mem_active_check" class="mem_active_check"
            style="display:inline;">'.
      _G("DBMF_memento_statut_active").':</label>
    <input  type="checkbox" id="mem_active_check" name="mem_active_check"
            checked
            value="statut_active" onclick="checkStatus(this,\'mem_active\')"/>
  </div>
  <div class="col-sm-4 t-right">
     <label for="mem_inactive_check" class="mem_inactive_check"
            style="display:inline;">'.
      _G("DBMF_memento_statut_inactive").':</label>
    <input  type="checkbox" id="mem_inactive_check" name="mem_inactive_check"
            checked
            value="statut_inactive" onclick="checkStatus(this,\'mem_inactive\')"/>
  </div>
  <div class="col-sm-4 t-right">
     <label for="mem_processed_check" class="mem_processed_check"
            style="display:inline;">'.
      _G("DBMF_memento_statut_processed").':</label>
    <input  type="checkbox" id="mem_processed_check" name="mem_processed_check"
            checked
            value="statut_processed" onclick="checkStatus(this,\'mem_processed\')"/>
  </div>
</div>';

echo '
<script>

function checkStatus(checkbox,status)
{
  var xmementos = document.getElementsByClassName(status);
  
  var i;
  if (checkbox.checked)
  {
    for (i = 0; i < xmementos.length; i++) {
      xmementos[i].style.display = "table";
    }
  } else {
    for (i = 0; i < xmementos.length; i++) {
      xmementos[i].style.display = "none";
    }
  }
}

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
</script>



<div class="mementos-list content list ">';

$mementos_nb = array();
$mementos_nb[0] = 0;
$mementos_nb[1] = 0;
$mementos_nbactives = array();
$mementos_nbactives[0] = 0;
$mementos_nbactives[1] = 0;

foreach($mementos_p as $memento) {
  if($memento->type==0) {
    $mementos_nb[0]++;
    if($memento->isActive())
      $mementos_nbactives[0]++;
  }
  if($memento->type==1) {
    $mementos_nb[1]++;
    if($memento->isActive())
      $mementos_nbactives[1]++;
  }
}

$memento_type = 0;

while($memento_type!=2) {

  if($memento_type==0) $h3 = 'DBMF_memento_type_punctual';
  elseif($memento_type==1) $h3 = 'DBMF_memento_type_monthofyear';
  echo '
<h2 class="bg-primary" style="margin-top: 2px;">'._G($h3).': 
'.$mementos_nb[$memento_type].' 
('._G("DBMF_memento_statut_active").': '.$mementos_nbactives[$memento_type].')</h2>';

  foreach($mementos_p as $memento) {

    if($memento->type==$memento_type) {

      echo '
    <div class="content list slide slide-toggled" id="memento'.$memento->id.'">';

      $app->tpl_dbmf_currentmemento = $memento;
      include( _pathI('memento_display_ctrl','dbmf3') );

      echo '
    </div>';

    }

  }
  if($mementos_nb[$memento_type]==0) {
    echo '
    <div class="row">
      <p class="col-sm-12">'.
        _G("DBMF_mementos_bycontact_noresults").'
      </p>
    </div>';
  }
  $memento_type++;

}

echo '
</div>

<script>
var xcheck = document.getElementById("expand");
checkExpand(xcheck);
</script>
';


?>
