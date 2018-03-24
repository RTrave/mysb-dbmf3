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


$exports = MySBDBMFExportHelper::load();
if(count($exports)<1)
    $app->pushAlert(_G('DBMF_no_exportrights'));

if(isset($_GET['dbmf_contact_delete'])) {
    MySBDBMFContactHelper::delete($_GET['dbmf_contact_delete']);
    echo '
<script>
slide_hide("contact'.$_GET['dbmf_contact_delete'].'");
</script>';
    return;
}

if(isset($_POST['dbmf_request_reuse'])) {
    $app->dbmf_search_result = MySBDB::query( $_SESSION['dbmf_search_query'],
	    "request_process.php(reuse)",
	    false, 'dbmf3');
	$app->dbmf_export_plugin = MySBDBMFExportHelper::getByID($_SESSION['dbmf_export_plugin']);
}

if(isset($_POST['dbmf_export_process'])) {

    $strp = explode('export_plug',$_POST['export_plug'] );
    $plug_id = intval($strp[1]);
    $app->dbmf_export_plugin = MySBDBMFExportHelper::getByID($plug_id);
    $_SESSION['dbmf_export_plugin'] = $app->dbmf_export_plugin->id;
    //echo $app->dbmf_export_plugin->name.'<br>';
    $app->dbmf_export_plugin->htmlParamProcess();

    $blocks = MySBDBMFBlockHelper::load();
    $clause_owner = MySBDBMFBlockHelper::sqlWhereClauseOwner();

    $sql_select = 'SELECT id from '.MySB_DBPREFIX.'dbmfcontacts ';
    $clause_a = '';
    foreach($blocks as $block) {
        $group_edit = MySBGroupHelper::getByID($block->groupedit_id);
        if(($clause=$block->htmlProcessWhereClause('b'))=='') {
            foreach($block->blockrefs as $blockref) {
                if($blockref->isActive()) {
                    $refname = $blockref->keyname;
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
    if($clause_owner!='' and $clause_a!='') $clause_a = '('.$clause_a.') and ('.$clause_owner.')';
    elseif($clause_owner!='') $clause_a .= '('.$clause_owner.')';

    $clause_export = $app->dbmf_export_plugin->requestWhereClause();
    if($clause_export!='') $clause_a = '('.$clause_a.' and ('.$clause_export.'))';

	$_SESSION['dbmf_query_where'] = $clause_a;
	$_SESSION['dbmf_query_select'] = $sql_select;
}

include( _pathT('export','dbmf3') );

?>
