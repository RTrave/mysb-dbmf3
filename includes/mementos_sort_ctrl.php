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


if( !MySBRoleHelper::checkAccess('dbmf_user') ) return;

if( !isset($_SESSION["dbmf_memcatg_sort"]) )
    $_SESSION["dbmf_memcatg_sort"] = 0;
if( isset($_GET["sort"]) )
    $_SESSION["dbmf_memcatg_sort"] = $_GET["sort"];


echo '
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

<br><br><br>';



if( isset($_GET['filter']) and $_GET['filter']=='all' ) {
    $mementos_p = MySBDBMFMementoHelper::load(null,$_SESSION["dbmf_memcatg_sort"]);
    $_SESSION["dbmf_memento_lastfilter"] = 'all';
    echo '
<h2>'._G('DBMF_mementos_all').' ('.count($mementos_p).')</h2>';
} else {
    $_GET['filter']='';
    $_SESSION["dbmf_memento_lastfilter"] = '';
    $mementos_p = MySBDBMFMementoHelper::loadActives($_SESSION["dbmf_memcatg_sort"]);
    echo '
<h2>'._G('DBMF_mementos_actives').' ('.count($mementos_p).')</h2>
';
}

echo '
<div id="dbmfMementoList">';

$memento_type = -1;

foreach($mementos_p as $memento) {

    if($memento->type!=$memento_type) {
        if($memento_type!=-1) echo '
</div>';
        $memento_type = $memento->type;
        if($memento_type==0) $h3 = 'DBMF_memento_type_punctual';
        elseif($memento_type==1) $h3 = 'DBMF_memento_type_monthofyear';
        echo '
<h3>'._G($h3).'</h3>

<div class="list_support">';
    }

    if($memento->isActive()) $Active = true;
    else $Active = false;
    if($Active) $memclass = 'mem_active';
    elseif(!$Active and $memento->date_process!='') $memclass = 'mem_processed';
    else $memclass='';
    echo '
<div class="cell" id="memento'.$memento->id.'" style="background-color: transparent;">';

    $app->tpl_dbmf_currentmemento = $memento;
    include( _pathI('memento_display_ctrl','dbmf3') );

    echo '
</div>';

}

if( count($mementos_p)!=0 )
    echo '
</div>';


echo '
<script>
show("mementos_results");
function changesort(selvalue) {
    loadItem( "mementos_results", "index.php?mod=dbmf3&inc=mementos_sort&sort="+selvalue+"&filter='.$_GET['filter'].'" );
}
</script>';


?>
