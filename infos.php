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


$act_mementos = MySBDBMFMementoHelper::loadByUserID_Actives($app->auth_user->id);
if(count($act_mementos)>=1) 
    echo '
<h2>'._G('DBMF_baseinfos_mementos').'</h2>
<ul>
<li>
    <a href="?mod=dbmf3&amp;tpl=mementos">'._G('DBMF_baseinfos_mementos_actives').'</a>: <blink><b>'.count($act_mementos).'</b></blink>
</li>
</ul>
';




if(!isset($_SESSION['dbmf3_baseinfos_date'])) {
    $sql_r = 'SELECT id from '.MySB_DBPREFIX.'dbmfcontacts ';
    $blocks = MySBDBMFBlockHelper::load();
    $clause_owner = '';
    foreach($blocks as $block) {
        $clause_owner_part = '';
        if($block->id!=1 and $block->isViewable()) {
            if($clause_owner_part!='')  $clause_owner_part .= ' or ';
            $clause_owner_part .= $block->htmlProcessWhereClause();
        }
        if($clause_owner!='' and $clause_owner_part!='') $clause_owner .= ' or ';
        if($clause_owner_part!='') $clause_owner .= $clause_owner_part;
    }
    if($clause_owner=='') {
        $app->pushAlert(_G('DBMF_no_rights'));
    }
    $clause_t .= '('.$clause_owner.')';
    if($clause_t!='()') $sql_r .= 'WHERE '.$clause_t.' ';
    //$sql_r .= 'ORDER by lastname';
	//$_SESSION['dbmf_search_query'] = $sql_r;
	$infosava_result = MySBDB::query( $sql_r,
	    "infos.php",
	    false, 'dbmf3');

	$infostot_result = MySBDB::query( 'SELECT id from '.MySB_DBPREFIX.'dbmfcontacts',
	    "infos.php",
	    false, 'dbmf3');

    $_SESSION['dbmf3_baseinfos_date'] = 1;
    $_SESSION['dbmf3_baseinfos_tot'] = MySBDB::num_rows($infostot_result);
    $_SESSION['dbmf3_baseinfos_ava'] = MySBDB::num_rows($infosava_result);

}

echo '
<h2>'._G('DBMF_baseinfos').'</h2>

<ul>
<li>
    '._G('DBMF_baseinfos_complete').': '.$_SESSION['dbmf3_baseinfos_tot'].'
    
</li>
<li>
    '._G('DBMF_baseinfos_avaible').': '.$_SESSION['dbmf3_baseinfos_ava'].'
    
</li>
</ul>
';


?>
