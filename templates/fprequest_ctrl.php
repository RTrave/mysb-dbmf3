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
}

if(isset($_POST['dbmf_request'])) {

    $sql_r = 'SELECT id from '.MySB_DBPREFIX.'dbmfcontacts ';
    $clause_a = '';
    $blocks = MySBDBMFBlockHelper::load();
    $clause_owner = MySBDBMFBlockHelper::sqlWhereClauseOwner();

    if( $_POST['search_type']=='all_fields' ) {
        $str_search_all = MySBUtil::str2whereclause($_POST['search_name']);
   	    $clause_a = 'lastname RLIKE \''.$str_search_all.'\' OR '.
   	        'firstname RLIKE \''.$str_search_all.'\' OR '.
   	        'mail RLIKE \''.$str_search_all.'\' ';
        $block_common = MySBDBMFBlockHelper::getByID(1);
        foreach($block_common->blockrefs as $blockref) {
            $clause_a .= ' OR '.$blockref->keyname.' RLIKE \''.$str_search_all.'\'';
        }
    } elseif( $_POST['search_type']=='lastname' ) {
        $clause_a = 'lastname RLIKE \''.MySBUtil::str2whereclause($_POST['search_name']).'\' ';
    } elseif( $_POST['search_type']=='byid' ) {
        $clause_a = 'id='.$_POST['search_name'].' ';
    } else {
        $clause_a = '';
    }

    if($clause_owner!='' and $clause_a!='') $clause_a = '('.$clause_a.') and ('.$clause_owner.')';
    elseif($clause_owner!='') $clause_a .= '('.$clause_owner.')';

	$_SESSION['dbmf_query_select'] = $sql_r;
	$_SESSION['dbmf_query_where'] = '('.$clause_a.')';

}

include( _pathT('fprequest','dbmf3') );

?>
