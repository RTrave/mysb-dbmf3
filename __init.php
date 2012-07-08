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
            'tel_pro varchar(32), '.
            'tel_dom varchar(32), '.
            'tel_fax varchar(32), '.
            'mail varchar(512), '.
            'function varchar(512), '.
            'organism varchar(512), '.
            'date_creat varchar(32), '.
            'date_modif varchar(32), '.
            'comments varchar(512) )',
            "__init.php",
            true, "dbmf3");

        $req = MySBDB::query('CREATE TABLE '.MySB_DBPREFIX.'dbmfblocks ( '.
            'bid int not null,  '.
            'bname varchar(32),  '.
            'lname varchar(512) )',
            "__init.php",
            true, "dbmf3");

        $req = MySBDB::query('CREATE TABLE '.MySB_DBPREFIX.'dbmfrouting ( '.
            'id int, '.
            'lname varchar(512) )',
            "__init.php",
            true, "dbmf3");

        //plugins using tables
        MySBPluginHelper::create('dbmforga_useroption','UserOption',
            array("dbmforga", "userorga_form", 'userorga_process',''),
            array(1,0,0,0),
            5,"admin",'dbmf3');

    }

    public function delete() {
        global $app;

        //plugins using tables
        MySBPluginHelper::delete('dbmforga_useroption','dbmf3');

        //tables
        $req = MySBDB::query('DROP TABLE '.MySB_DBPREFIX.'dbmfcontacts',
            "__init.php",
            true, "dbmf3");
        $req = MySBDB::query('DROP TABLE '.MySB_DBPREFIX.'dbmfblocks',
            "__init.php",
            true, "dbmf3");
         $req = MySBDB::query('DROP TABLE '.MySB_DBPREFIX.'dbmfrouting',
            "__init.php",
            true, "dbmf3");
    }

    public function init1() {
        global $app;

        MySBPluginHelper::create('addcontact_menutext','MenuItem',
            array("DBMF_topmenu_addcontact", "addcontact", 'DBMF_topmenu_addcontactinfos',''),
            array(1,0,0,0),
            4,"dbmf_editor",'dbmf3');

        MySBPluginHelper::create('dbmfcontact_php','Include',
            array("libraries/contact.php", '', '',''),
            array(0,0,0,0),
            5,'','dbmf3');

        $editrole = MySBRole::create('dbmf_editor','Can edit DB entries');
        $editrole->assignToGroup('admin',true);
        $editrole = MySBRole::create('dbmf_user','Can view DB entries',true);
        $editrole->assignToGroup('admin',true);
    }

    public function uninit() {
        global $app;

        MySBRole::delete('dbmf_user');
        MySBRole::delete('dbmf_editor');

        //plugins
        MySBPluginHelper::delete('addcontact_menutext','dbmf3');
        MySBPluginHelper::delete('dbmfcontact_php','dbmf3');
    }

}
?>
