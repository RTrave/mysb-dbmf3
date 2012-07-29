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

if(isset($_POST['dbmf_export_process'])) {

    $app->dbmf_export_plugin = MySBDBMFExportHelper::getByID($_POST['export_plug'][11]);

    $app->dbmf_export_plugin->htmlParamProcess();

    $blocks = MySBDBMFBlockHelper::load();
    $clause_owner = '';
    foreach($blocks as $block) {
        if($block->isEditable()) {
            if($clause_owner!='')  $clause_owner .= ' or ';
            $clause_owner .= $block->htmlProcessWhereClause();
        }
    }

    $sql_a = 'SELECT * from '.MySB_DBPREFIX.'dbmfcontacts ';
    $clause_a = '';
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
    if($clause_owner!='' and $clause_a!='') $clause_a .= ' and ('.$clause_owner.')';
    elseif($clause_owner!='') $clause_a .= '('.$clause_owner.')';
    if($clause_a!='') $sql_a .= 'WHERE '.$clause_a.' ';
    $sql_a .= 'ORDER by lastname';
	$app->dbmf_search_result = MySBDB::query( $sql_a,
	    "request_process.php",
	    false, 'dbmf3');

}

?>