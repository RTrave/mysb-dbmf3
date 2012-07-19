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

if(isset($_POST['dbmf_request'])) {

    if (!empty($_POST['search_all'])) {
        $str_search_all = MySBUtil::str2whereclause($_POST['search_all']);
   	    $sql_r = 'SELECT * from '.MySB_DBPREFIX.'dbmfcontacts '.
        'WHERE lastname RLIKE \''.$str_search_all.'\' OR '.
   	    'firstname RLIKE \''.$str_search_all.'\' OR '.
   	    'function RLIKE \''.$str_search_all.'\' OR '.
   	    'organism RLIKE \''.$str_search_all.'\' OR '.
   	    'comments LIKE \''.$str_search_all.'\' OR '.
   	    'mail RLIKE \''.$str_search_all.'\' '.
   	    'ORDER by lastname;';
   	    $_POST['search_name'] = '';
    } elseif(!empty($_POST['search_name'])) {
        $sql_r = 'SELECT * from '.MySB_DBPREFIX.'dbmfcontacts '.
        'WHERE lastname RLIKE \''.MySBUtil::str2whereclause($_POST['search_name']).'\' '.
        'ORDER by lastname;';
    } else {
        $sql_r = 'SELECT * from '.MySB_DBPREFIX.'dbmfcontacts '.
        'ORDER by lastname;';
    }
	$app->dbmf_search_result = MySBDB::query( $sql_r,
	    "request_process.php",
	    false, 'dbmf3');
}

if(isset($_POST['dbmf_request_advanced'])) {

    $sql_a = 'SELECT * from '.MySB_DBPREFIX.'dbmfcontacts ';
    $clause_a = '';
    $blocks = MySBDBMFBlockHelper::load();
    foreach($blocks as $block) {
        $group_edit = MySBGroupHelper::getByID($block->groupedit_id);
        if(($clause=$block->htmlProcessWhereClause('b'))=='') {
            foreach($block->blockrefs as $blockref) {
                if($block->isEditable() and $blockref->isActive()) {
                    $refname = 'br'.$blockref->id;
                    if(($clause_t = $blockref->htmlProcessWhereClause('br'))!=null) {
                        if($clause!='') $clause .= ' '.$_POST['blockref_andorflag_'.$block->id].' ';
                        $clause .= $clause_t;
                    }
                }
            }
        }
        if($clause_a!='' and $clause!='') $clause_a .= ' '.$_POST['block_andorflag_'.$block->id].' ';
        if($clause!='') $clause_a .= '('.$clause.')';
    }
    if($clause_a!='') $sql_a .= 'WHERE '.$clause_a.' ';
    $sql_a .= 'ORDER by lastname';
	$app->dbmf_search_result = MySBDB::query( $sql_a,
	    "request_process.php",
	    false, 'dbmf3');

}

?>
