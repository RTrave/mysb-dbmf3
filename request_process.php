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

if( !MySBRoleHelper::checkAccess('dbmf_user',false) ) return;


if(isset($_POST['dbmf_contact_delete'])) {
    MySBDBMFContactHelper::delete($_POST['dbmf_contact_delete']);
}

if(isset($_POST['dbmf_request_reuse'])) {
    $app->dbmf_search_result = MySBDB::query( $_SESSION['dbmf_search_query'],
	    "request_process.php(reuse)",
	    false, 'dbmf3');
}

if(isset($_POST['dbmf_request'])) {

    $sql_r = 'SELECT * from '.MySB_DBPREFIX.'dbmfcontacts ';
    $clause_a = '';
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
    if (!empty($_POST['search_all'])) {
        $str_search_all = MySBUtil::str2whereclause($_POST['search_all']);
   	    $clause_a = 'lastname RLIKE \''.$str_search_all.'\' OR '.
   	        'firstname RLIKE \''.$str_search_all.'\' ';
        $block_common = MySBDBMFBlockHelper::getByID(1);
        foreach($block_common->blockrefs as $blockref) {
            $clause_a .= ' OR '.$blockref->keyname.' RLIKE \''.$str_search_all.'\'';
        }
   	    $_POST['search_name'] = '';
    } elseif(!empty($_POST['search_name'])) {
        $clause_a = 'lastname RLIKE \''.MySBUtil::str2whereclause($_POST['search_name']).'\' ';
    } elseif(!empty($_POST['search_byid'])) {
        $clause_a = 'id='.$_POST['search_byid'].' ';
    } else {
        $clause_a = '';
    }
    if($clause_owner!='' and $clause_a!='') $clause_a .= ' and ('.$clause_owner.')';
    elseif($clause_owner!='') $clause_a .= '('.$clause_owner.')';
    if($clause_a!='') $sql_r .= 'WHERE '.$clause_a.' ';
    $sql_r .= 'ORDER by lastname';
	$_SESSION['dbmf_search_query'] = $sql_r;
	$app->dbmf_search_result = MySBDB::query( $sql_r,
	    "request_process.php",
	    false, 'dbmf3');
    
}

?>
