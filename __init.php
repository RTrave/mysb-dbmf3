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

class MySBModule_dbmf3 {

    public $version = 1;

    public function create() {
        global $app;

        //tables
        $req = MySBDB::query('CREATE TABLE '.MySB_DBPREFIX.'dbmfcontacts ( '.
            'id int, '.
            'prefix varchar(32), '.
            'lastname varchar(512), '.
            'firstname varchar(512), '.
            'adress_1 varchar(512), '.
            'adress_2 varchar(512), '.
            'tel_1 varchar(32), '.
            'tel_2 varchar(32), '.
            'tel_fax varchar(32), '.
            'mail varchar(512), '.
            'function varchar(512), '.
            'organism varchar(512), '.
            'date_creat varchar(32), '.
            'date_modif varchar(32), '.
            'comments varchar(512) )',
            "__init.php",
            false, "dbmf3");

        $req = MySBDB::query('CREATE TABLE '.MySB_DBPREFIX.'dbmfblocks ( '.
            'id int not null,  '.
            'name varchar(32),  '.
            'lname varchar(512), '.
            'group_id int )',
            "__init.php",
            false, "dbmf3");

        $req = MySBDB::query('CREATE TABLE '.MySB_DBPREFIX.'dbmfrouting ( '.
            'id int, '.
            'lname varchar(512) )',
            "__init.php",
            false, "dbmf3");

        $req = MySBDB::query('ALTER TABLE '.MySB_DBPREFIX.'groups ADD COLUMN '.
            'dbmf_priority int',
            "__init.php",
            false, "dbmf3");


    }

    public function delete() {
        global $app;

        //tables
        $req = MySBDB::query('DROP TABLE '.MySB_DBPREFIX.'dbmfcontacts',
            "__init.php",
            false, "dbmf3");
        $req = MySBDB::query('DROP TABLE '.MySB_DBPREFIX.'dbmfblocks',
            "__init.php",
            false, "dbmf3");
         $req = MySBDB::query('DROP TABLE '.MySB_DBPREFIX.'dbmfrouting',
            "__init.php",
            false, "dbmf3");
    }

    public function init1() {
        global $app;

        MySBPluginHelper::create('addcontact_menutext','MenuItem',
            array("DBMF_topmenu_addcontact", "addcontact", 'DBMF_topmenu_addcontactinfos',''),
            array(1,0,0,0),
            4,"dbmf_editor",'dbmf3');

        MySBPluginHelper::create('dbmfadmin_menutext','MenuItem',
            array("DBMF_topmenu_dbmfadmin", "adminblocks", 'DBMF_topmenu_dbmfadmininfos',''),
            array(2,0,0,0),
            4,"dbmf_admin",'dbmf3');

        MySBPluginHelper::create('dbmfgroup_php','Include',
            array("libraries/group.php", '', '',''),
            array(0,0,0,0),
            5,'','dbmf3');
        MySBPluginHelper::create('dbmfcontact_php','Include',
            array("libraries/contact.php", '', '',''),
            array(0,0,0,0),
            5,'','dbmf3');
        MySBPluginHelper::create('dbmfblock_php','Include',
            array("libraries/block.php", '', '',''),
            array(0,0,0,0),
            6,'','dbmf3');

        MySBPluginHelper::create('dbmf_request','FrontPage',
            array("request", '', '',''),
            array(0,0,0,0),
            5,"dbmf_user",'dbmf3');

        $editrole = MySBRole::create('dbmf_admin','Can admin DB blocks');
        $editrole->assignToGroup('admin',true);
        $editrole = MySBRole::create('dbmf_editor','Can edit DB entries');
        $editrole->assignToGroup('admin',true);
        $editrole = MySBRole::create('dbmf_user','Can view DB entries',true);
        $editrole->assignToGroup('admin',true);
    }

    public function uninit() {
        global $app;

        MySBRole::delete('dbmf_user');
        MySBRole::delete('dbmf_editor');
        MySBRole::delete('dbmf_admin');

        //plugins
        MySBPluginHelper::delete('dbmf_request','dbmf3');
        MySBPluginHelper::delete('dbmfadmin_menutext','dbmf3');
        MySBPluginHelper::delete('addcontact_menutext','dbmf3');
        MySBPluginHelper::delete('dbmfblock_php','dbmf3');
        MySBPluginHelper::delete('dbmfcontact_php','dbmf3');
        MySBPluginHelper::delete('dbmfgroup_php','dbmf3');
    }

}
?>
