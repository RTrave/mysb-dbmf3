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


if( !isset($_SESSION["dbmf_query_sort"]) ) 
    $_SESSION["dbmf_query_sort"] = 'lastname';
if( isset($_GET["sort"]) ) 
    $_SESSION["dbmf_query_sort"] = $_GET["sort"];

if( !isset($_SESSION["dbmf_query_asc"]) ) 
    $_SESSION["dbmf_query_asc"] = '';
if( isset($_GET["asc"]) ) 
    $_SESSION["dbmf_query_asc"] = $_GET["asc"];

if( !isset($_SESSION["dbmf_search_pack"]) ) 
    $_SESSION["dbmf_search_pack"] = '20';
if( isset($_GET["pack"]) ) 
    $_SESSION["dbmf_search_pack"] = $_GET["pack"];


function sortActions($pack) {
    global $app;

    $output = '
<div class="sort_actions">
'._G('DBMF_display_orderby').':
<select name="dbmf_exportdisplay_orderby" onchange="changesort(this.value);">
    <option value="lastname" '.MySBUtil::form_isselected('lastname',$_SESSION["dbmf_query_sort"]).'>'._G('DBMF_common_lastname').'</option>
    <option value="date_modif" '.MySBUtil::form_isselected('date_modif',$_SESSION["dbmf_query_sort"]).'>'._G('DBMF_date_modif').'</option>';
    $blockref_orderby = MySBDBMFBlockRefHelper::load();
    foreach($blockref_orderby as $oblockref) {
        if($oblockref->orderby=='1') 
            $output .= '
    <option value="'.$oblockref->keyname.'" '.MySBUtil::form_isselected($oblockref->keyname,$_SESSION["dbmf_query_sort"]).'>'._G($oblockref->lname).'</option>';
    }
    $output .= '
</select>
<select name="dbmf_exportdisplay_asc" onchange="changeasc(this.value);">
    <option value="asc" '.MySBUtil::form_isselected('asc',$_SESSION["dbmf_query_asc"]).'>asc</option>
    <option value="desc" '.MySBUtil::form_isselected('desc',$_SESSION["dbmf_query_asc"]).'>desc</option>
</select>
<br>
<div style="vertical-align: top;">';
    if( $pack['next_id']!=-1 ) {
        $output .= '
    <img src="images/icons/go-last.png" style="float: right;" class="linked"
        alt="'._G('SBGT_last').'"
        title="'._G('SBGT_last').'"
        onclick="scrollresults();loadItem(\'contacts_results\',\'index.php?mod=dbmf3&inc=contacts_sort&sid='.$pack['last_id'].'\');">
    <img src="images/icons/go-next.png" style="float: right;" class="linked"
        alt="'._G('SBGT_next').'"
        title="'._G('SBGT_next').'"
        onclick="scrollresults();loadItem(\'contacts_results\',\'index.php?mod=dbmf3&inc=contacts_sort&sid='.$pack['next_id'].'\');">';
    } else {
        $output .= '
    <img src="images/blank.png" style="float: right;" alt="blank" class="linked">
    <img src="images/blank.png" style="float: right;" alt="blank" class="linked">';
    }
    $output .= '
<select name="dbmf_exportdisplay_pack" onchange="changepack(this.value);" style="float: right; margin: 0px 20px;">
    <option value="5" '.MySBUtil::form_isselected('5',$_SESSION["dbmf_search_pack"]).'>5</option>
    <option value="10" '.MySBUtil::form_isselected('10',$_SESSION["dbmf_search_pack"]).'>10</option>
    <option value="20" '.MySBUtil::form_isselected('20',$_SESSION["dbmf_search_pack"]).'>20</option>
    <option value="30" '.MySBUtil::form_isselected('30',$_SESSION["dbmf_search_pack"]).'>30</option>
    <option value="50" '.MySBUtil::form_isselected('50',$_SESSION["dbmf_search_pack"]).'>50</option>
    <option value="100" '.MySBUtil::form_isselected('100',$_SESSION["dbmf_search_pack"]).'>100</option>
</select>';
    if( $pack['prev_id']!=-1 ) {
        $output .= '
    <img src="images/icons/go-previous.png" style="float: right;" class="linked"
        alt="'._G('SBGT_previous').'"
        title="'._G('SBGT_previous').'"
        onclick="scrollresults();loadItem(\'contacts_results\',\'index.php?mod=dbmf3&inc=contacts_sort&sid='.$pack['prev_id'].'\');">
    <img src="images/icons/go-first.png" style="float: right;" class="linked"
        alt="'._G('SBGT_first').'"
        title="'._G('SBGT_first').'"
        onclick="scrollresults();loadItem(\'contacts_results\',\'index.php?mod=dbmf3&inc=contacts_sort&sid='.$pack['first_id'].'\');">';
    } else {
        $output .= '
    <img src="images/blank.png" style="float: right;" alt="blank" class="linked">
    <img src="images/blank.png" style="float: right;" alt="blank" class="linked">';
    }
    $output .= '
</div>
</div>';

    return $output;
}

if( $_SESSION['dbmf_query_where']=='' ) {
    echo '<br>'.(_G("DBMF_export_nowhereclause")).'<br><br>';
    return;
}

$sql_all =  $_SESSION['dbmf_query_select'].' WHERE '.$_SESSION['dbmf_query_where'].
            ' ORDER by '.$_SESSION["dbmf_query_sort"].
            ' '.$_SESSION["dbmf_query_asc"];
$search_all = MySBDB::query( $sql_all,
    "request_process.php",
    false, 'dbmf3');

//$search_result = $app->tpl_dbmf_searchresult;
$pluginsDisplay = MySBPluginHelper::loadByType('DBMFDisplay');
$showcols_blockrefs = array();
$showcols = new MySBCSValues($app->auth_user->dbmf_showcols);
foreach($showcols->values as $br_id) {
    $showblockref = MySBDBMFBlockRefHelper::getByID($br_id);
    if( isset($showblockref) and $showblockref->isActive() )
        $showcols_blockrefs[] = $showblockref;
}

if( !isset($_GET['sid']) ) $_GET['sid'] = 0;
$array_all = array();
$array_all[0] = array();
$counter = 0;
$i_pack = 0;
$i_contact = 0;
$i_currentpack = 0;
while( $datares = MySBDB::fetch_array($search_all) ) {
    if( $i_contact==$_SESSION["dbmf_search_pack"] ) {
        $i_contact = 0;
        $i_pack++;
        $array_all[$i_pack] = array();
    }
    if( $datares['id']==$_GET['sid'] ) $i_currentpack = $i_pack;
    $array_all[$i_pack][$i_contact] = $datares['id'];
    $counter++;
    $i_contact++;
}

$packF = array();
$packF['first_id'] = -1;
$packF['prev_id'] = -1;
if( $i_currentpack>0 ) {
    $packF['first_id'] = $array_all[0][0];
    $packF['prev_id'] = $array_all[($i_currentpack-1)][0];
}
$packF['next_id'] = -1;
$packF['last_id'] = -1;
if( $i_currentpack<$i_pack ) {
    $packF['next_id'] = $array_all[($i_currentpack+1)][0];
    $packF['last_id'] = $array_all[$i_pack][0];
}

$current_pack = $array_all[($i_currentpack)];
$first_packid = $i_currentpack*$_SESSION["dbmf_search_pack"]+1;
$last_packid = $i_currentpack*$_SESSION["dbmf_search_pack"]+count($current_pack);

$sql_mclose = '';
$i = 0;
foreach( $current_pack as $packid ) {
    if( $sql_mclose!='' ) $sql_mclose .= ' or';
    $sql_mclose .= ' id='.$packid;
    $i++;
}
$search_m = MySBDB::query( 'SELECT * FROM '.MySB_DBPREFIX.'dbmfcontacts '.
        'WHERE ('.$sql_mclose.') '.
        'ORDER by '.$_SESSION["dbmf_query_sort"].' '.
        $_SESSION["dbmf_query_asc"],
    "contacts_sort.php",
    false, 'dbmf3');

echo '
<script>
show("contacts_results");
function scrollresults() {
    $("html, body").animate({ scrollTop: $("div#contacts_results").offset().top }, 200);
}
function changesort(selvalue) {
    //scrollresults();
    loadItem( "contacts_results", "index.php?mod=dbmf3&inc=contacts_sort&sort="+selvalue );
}
function changeasc(selvalue) {
    //scrollresults();
    loadItem( "contacts_results", "index.php?mod=dbmf3&inc=contacts_sort&asc="+selvalue );
}
function changepack(selvalue) {
    //scrollresults();
    loadItem( "contacts_results", "index.php?mod=dbmf3&inc=contacts_sort&pack="+selvalue );
}
</script>';

echo sortActions($packF);


echo '
<p>
<br>
<b>'.$first_packid.'-'.$last_packid.'</b><br>
('.$counter.' results)<br>
<br>
</p>

<div class="list_support">';

while($data_print = MySBDB::fetch_array($search_m)) {

    $contact = new MySBDBMFContact(null,$data_print);
    $app->tpl_dbmf_currentcontact = $contact;
    echo '
    <div id="contact'.$contact->id.'">';
    _incI('contact_display','dbmf3');
    echo '
    </div>';

}

echo '
</div>
';
echo sortActions($packF);

echo '
<p><br>
<img    src="images/icons/go-top.png" class="linked"
        alt="'._G('SBGT_top').'"
        title="'._G('SBGT_top').'"
        onclick="scrollresults();">
</p>
<br>';

?>
