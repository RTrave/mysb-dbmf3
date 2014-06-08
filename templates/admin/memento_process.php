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

if(!MySBRoleHelper::checkAccess('dbmf_config')) return;


if( isset($_POST['memcatg_submit']) and $_POST['memcatg_submit']==1 ) {

    $memcatgs = MySBDBMFMementoCatgHelper::load();
    $groups = MySBGroupHelper::load();

    foreach( $memcatgs as $memcatg ) {
        $groups_csv = new MySBCSValues();
        foreach( $groups as $group ) {
            if( isset($_POST['memcatg_mc'.$memcatg->id.'g'.$group->id]) and 
                $_POST['memcatg_mc'.$memcatg->id.'g'.$group->id]!='' ) 
                $groups_csv->add( $group->id );
        }
        $memcatg->update( array(
            'name' => $_POST['memcatg_name'.$memcatg->id],
            'group_ids' => $groups_csv->csstring() ) );
    }
}

if( isset($_POST['memcatg_new']) and $_POST['memcatg_new']==1 ) {

    $new_memcatg = MySBDBMFMementoCatgHelper::create( $_POST['memcatg_name_new'] );

}

?>
