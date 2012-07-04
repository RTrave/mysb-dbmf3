<?php
/***************************************************************************
 *
 *   phpMySandBox/RSVP module - TRoman<abadcafe@free.fr> - 2012
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
        $req = MySBDB::query('CREATE TABLE '.MySB_DBPREFIX.'dbmfcommon ( '.
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
        $req = MySBDB::query('DROP TABLE '.MySB_DBPREFIX.'dbmfcommon',
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

    }

    public function uninit() {
        global $app;

        //plugins
    }

}
?>
