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
            'groupedit_id int )',
            "__init.php",
            false, "dbmf3");

        $req = MySBDB::query('CREATE TABLE '.MySB_DBPREFIX.'dbmfblockrefs ( '.
            'id int not null, '.
            'block_id int, '.
            'keyname varchar(32), '.
            'lname varchar(64), '.
            'type varchar(64), '.
            'status int, '.
            'i_index int)',
            "__init.php",
            false, "dbmf3");

        $req = MySBDB::query('CREATE TABLE '.MySB_DBPREFIX.'dbmfexports ( '.
            'id int, '.
            'type varchar(32), '.
            'name varchar(64), '.
            'comments varchar(128), '.
            'config varchar(512), '.
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
        $req = MySBDB::query('ALTER TABLE '.MySB_DBPREFIX.'groups DROP COLUMN dbmf_priority',
            "__init.php",
            false, "dbmf3");
        $req = MySBDB::query('DROP TABLE '.MySB_DBPREFIX.'dbmfcontacts',
            "__init.php",
            false, "dbmf3");
        $req = MySBDB::query('DROP TABLE '.MySB_DBPREFIX.'dbmfblockrefs',
            "__init.php",
            false, "dbmf3");
        $req = MySBDB::query('DROP TABLE '.MySB_DBPREFIX.'dbmfblocks',
            "__init.php",
            false, "dbmf3");
         $req = MySBDB::query('DROP TABLE '.MySB_DBPREFIX.'dbmfexports',
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

        MySBPluginHelper::create('export_menutext','MenuItem',
            array("DBMF_topmenu_export", "export", 'DBMF_topmenu_exportinfos',''),
            array(1,0,0,0),
            5,"dbmf_user",'dbmf3');

        MySBPluginHelper::create('blockedit_menutext','MenuItem',
            array("DBMF_topmenu_blockedit", "blockedit", 'DBMF_topmenu_blockeditinfos',''),
            array(2,0,0,0),
            8,"dbmf_blockedit",'dbmf3');

        MySBPluginHelper::create('admindbmf_menutext','MenuItem',
            array("DBMF_adminmenu_dbmf", "admindbmf", 'DBMF_adminmenu_dbmfinfos',''),
            array(3,0,0,0),
            6,"dbmf_admin",'dbmf3');
        MySBPluginHelper::create('dbmf_request','FrontPage',
            array("request", '', '',''),
            array(0,0,0,0),
            5,"dbmf_user",'dbmf3');

        MySBPluginHelper::create('dbmf_exportdisplay','DBMFExport',
            array("Display", 'HTML display render', 'libraries/export_display.php',''),
            array(0,0,0,0),
            6,"dbmf_user",'dbmf3');
        MySBPluginHelper::create('dbmf_exportmailing','DBMFExport',
            array("Mailing", 'Mailing engine', 'libraries/export_mailing.php',''),
            array(0,0,0,0),
            3,"dbmf_user",'dbmf3');

        $adminrole = MySBRoleHelper::create('dbmf_admin','Can admin DBMF');
        $adminrole->assignToGroup('admin',true);
        $blockeditrole = MySBRoleHelper::create('dbmf_blockedit','Can edit DB blocks');
        $blockeditrole->assignToGroup('admin',true);
        $editrole = MySBRoleHelper::create('dbmf_editor','Can edit DB entries');
        $editrole->assignToGroup('admin',true);
        $userrole = MySBRoleHelper::create('dbmf_user','Can view DB entries',true);
        $userrole->assignToGroup('admin',true);

        MySBDBMFExportHelper::create("DBMF_display","Display","DBMF_HTML_table", '', 1);
    }

    public function uninit() {
        global $app;

        MySBRoleHelper::delete('dbmf_user');
        MySBRoleHelper::delete('dbmf_editor');
        MySBRoleHelper::delete('dbmf_blockedit');
        MySBRoleHelper::delete('dbmf_admin');

        MySBDBMFExportHelper::delete(MySBDBMFExportHelper::getByName('DBMF_display')->id);

        //plugins
        MySBPluginHelper::delete('dbmf_exportmailing','dbmf3');
        MySBPluginHelper::delete('dbmf_exportdisplay','dbmf3');

        MySBPluginHelper::delete('dbmf_request','dbmf3');
        MySBPluginHelper::delete('dbmfadmin_menutext','dbmf3');
        MySBPluginHelper::delete('admindbmf_menutext','dbmf3');
        MySBPluginHelper::delete('addcontact_menutext','dbmf3');
        MySBPluginHelper::delete('export_menutext','dbmf3');
        MySBPluginHelper::delete('blockedit_menutext','dbmf3');
        
    }

}
?>
