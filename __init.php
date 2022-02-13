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

    public $lname = 'dbmf3';
    public $version = 22;
    public $release_version = '4c';
    public $homelink = 'https://github.com/RTrave/mysb-dbmf3';
    public $require = array(
        'core' => 7
        );

    public function create() {
        global $app;

        //tables
        $req = MySBDB::query('CREATE TABLE '.MySB_DBPREFIX.'dbmfcontacts ( '.
            'id int unique key, '.
            'date_creat date, '.
            'date_modif date, '.
            'lastname varchar(64), '.
            'firstname varchar(64) )
            DEFAULT CHARSET=utf8',
            "__init.php",
            false, "dbmf3");

        $req = MySBDB::query('CREATE TABLE '.MySB_DBPREFIX.'dbmfblocks ( '.
            'id int not null unique key,  '.
            'name varchar(32),  '.
            'lname varchar(512), '.
            'groupedit_id int, '.
            'i_index int )
            DEFAULT CHARSET=utf8',
            "__init.php",
            false, "dbmf3");

        $req = MySBDB::query('CREATE TABLE '.MySB_DBPREFIX.'dbmfblockrefs ( '.
            'id int not null unique key, '.
            'block_id int, '.
            'keyname varchar(32), '.
            'lname varchar(64), '.
            'type varchar(64), '.
            'status int, '.
            'i_index int)
            DEFAULT CHARSET=utf8',
            "__init.php",
            false, "dbmf3");

        $req = MySBDB::query('CREATE TABLE '.MySB_DBPREFIX.'dbmfexports ( '.
            'id int unique key, '.
            'type varchar(32), '.
            'name varchar(64), '.
            'comments varchar(128), '.
            'config varchar(512), '.
            'group_id int )
            DEFAULT CHARSET=utf8',
            "__init.php",
            false, "dbmf3");

        $req = MySBDB::query('ALTER TABLE '.MySB_DBPREFIX.'groups ADD COLUMN '.
            'dbmf_priority int',
            "__init.php",
            false, "dbmf3");
        $req = MySBDB::query('UPDATE '.MySB_DBPREFIX.'groups SET dbmf_priority=3 '.
            'WHERE id=1 ',
            "__init.php",
            false, "dbmf3");

        include(MySB_ROOTPATH.'/modules/dbmf3/framework.php');

        $common_block = MySBDBMFBlockHelper::create('DBMF_common_blockname');
        $prefix = MySBDBMFBlockRefHelper::create('DBMF_common_prefix',MYSB_VALUE_TYPE_VARCHAR64_SELECT,$common_block->id);
        MySBDBMFBlockRefHelper::create('DBMF_common_function',MYSB_VALUE_TYPE_VARCHAR512,$common_block->id);
        MySBDBMFBlockRefHelper::create('DBMF_common_organism',MYSB_VALUE_TYPE_VARCHAR512,$common_block->id);
        //MySBDBMFBlockRefHelper::create('DBMF_common_lastname',MYSB_VALUE_TYPE_VARCHAR64,$common_block->id);
        //MySBDBMFBlockRefHelper::create('DBMF_common_firstname',MYSB_VALUE_TYPE_VARCHAR64,$common_block->id);
        MySBDBMFBlockRefHelper::create('DBMF_common_adress_1',MYSB_VALUE_TYPE_TEXT,$common_block->id);
        //MySBDBMFBlockRefHelper::create('DBMF_common_adress_2',MYSB_VALUE_TYPE_TEXT,$common_block->id);
        MySBDBMFBlockRefHelper::create('DBMF_common_tel_1',MYSB_VALUE_TYPE_VARCHAR64,$common_block->id);
        MySBDBMFBlockRefHelper::create('DBMF_common_tel_2',MYSB_VALUE_TYPE_VARCHAR64,$common_block->id);
        MySBDBMFBlockRefHelper::create('DBMF_common_tel_fax',MYSB_VALUE_TYPE_VARCHAR64,$common_block->id);
        MySBDBMFBlockRefHelper::create('DBMF_common_mail',MYSB_VALUE_TYPE_VARCHAR512,$common_block->id);
        MySBDBMFBlockRefHelper::create('DBMF_common_comments',MYSB_VALUE_TYPE_TEXT,$common_block->id);

        $prefix->addSelectOption('DBMF_common_prefix_miss');
        $prefix->addSelectOption('DBMF_common_prefix_mr');


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
        $req = MySBDB::query('DROP TABLE '.MySB_DBPREFIX.'dbmfmementos',
            "__init.php",
            false, "dbmf3");
        $req = MySBDB::query('DROP TABLE '.MySB_DBPREFIX.'dbmfmementocatgs',
            "__init.php",
            false, "dbmf3");

        $req = MySBDB::query('ALTER TABLE '.MySB_DBPREFIX.'users DROP COLUMN dbmf_showcols',
            "__init.php",
            false, "dbmf3");

        MySBDB::query("DELETE FROM ".MySB_DBPREFIX."valueoptions WHERE value_keyname='dbmf3-b1r01'",
                "__init.php");

        //configs
        MySBConfigHelper::delete('dbmf_showblocks_colsnb', 'dbmf3');
        MySBConfigHelper::delete('dbmf_showfields_colsnb', 'dbmf3');
    }

    public function init1() {
        global $app;

        MySBPluginHelper::create('addcontact_menutext','MenuItem',
            array('DBMF_topmenu_addcontact', "addcontact", 'DBMF_topmenu_addcontactinfos',''),
            array(1,0,0,0),
            4,"dbmf_editor",'dbmf3');

        MySBPluginHelper::create('export_menutext','MenuItem',
            array('DBMF_topmenu_export', "export", 'DBMF_topmenu_exportinfos',''),
            array(1,0,0,0),
            5,"dbmf_user",'dbmf3');

        MySBPluginHelper::create('blockedit_menutext','MenuItem',
            array('DBMF_topmenu_blockedit', "blockedit", 'DBMF_topmenu_blockeditinfos',''),
            array(2,0,0,0),
            3,"dbmf_blockedit",'dbmf3');

        MySBPluginHelper::create('admindbmf_menutext','MenuItem',
            array('DBMF_adminmenu_dbmf', "admindbmf", 'DBMF_adminmenu_dbmfinfos',''),
            array(3,0,0,0),
            6,"admin",'dbmf3');
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
        MySBPluginHelper::create('dbmf_exportmailscsv','DBMFExport',
            array("MailsCSV", 'CSV Mails export', 'libraries/export_mails.php',''),
            array(0,0,0,0),
            4,"dbmf_user",'dbmf3');

        MySBPluginHelper::create('dbmf_showmail','DBMFDisplay',
            array("DBMFPluginsDisplayMail", '', '',''),
            array(1,0,0,0),
            3,"dbmf_user",'dbmf3');
        MySBPluginHelper::create('dbmf_showorga','DBMFDisplay',
            array("DBMFPluginsDisplayOrganism", '', '',''),
            array(2,0,0,0),
            4,"dbmf_user",'dbmf3');
        MySBPluginHelper::create('dbmf_showtels','DBMFDisplay',
            array("DBMFPluginsDisplayTel", '', '',''),
            array(2,0,0,0),
            3,"dbmf_user",'dbmf3');

        //configs
        MySBConfigHelper::create('dbmf_showfields_colsnb','6',MYSB_VALUE_TYPE_INT,
            'How many columns (in display parameters)', 'dbmf3');

        //roles
        $blockeditrole = MySBRoleHelper::create('dbmf_blockedit','Can edit DB blocks');
        $blockeditrole->assignToGroup('admin',true);
        $editrole = MySBRoleHelper::create('dbmf_editor','Can edit DB entries');
        $editrole->assignToGroup('admin',true);
        $userrole = MySBRoleHelper::create('dbmf_user','Can view DB entries',true);
        $userrole->assignToGroup('admin',true);
        $userrole->assignToGroup('users',true);

        MySBDBMFExportHelper::create("DBMF_display","Display","DBMF_HTML_table", '', 1);
    }

    public function init2() {
        global $app;
        MySBPluginHelper::create('dbmf_exportcsv','DBMFExport',
            array("CSV", 'CSV export', 'libraries/export_csv.php',''),
            array(0,0,0,0),
            2,"dbmf_user",'dbmf3');
    }

    public function init3() {
        global $app;
        MySBPluginHelper::create('dbmf_exportupdate','DBMFExport',
            array("UPDATE", 'Update export', 'libraries/export_update.php',''),
            array(0,0,0,0),
            2,"dbmf_blockedit",'dbmf3');
    }

    public function init4() {
        global $app;
        MySBPluginHelper::create('dbmf_infos','FrontPage',
            array("infos", '', '',''),
            array(0,0,0,0),
            3,"dbmf_user",'dbmf3');
    }

    public function init5() {
        global $app;
        $req = MySBDB::query('CREATE TABLE '.MySB_DBPREFIX.'dbmfmementos ( '.
            'id int unique key, '.
            'user_id int, '.
            'group_id int, '.
            'contact_id int, '.
            'type int, '.
            'date_memento datetime, '.
            'dayofmonth_memento int, '.
            'monthofyear_memento int, '.
            'date_process datetime, '.
            'comments varchar(512) )
            DEFAULT CHARSET=utf8',
            "__init.php",
            false, "dbmf3");

        MySBPluginHelper::create('mementos_menutext','MenuItem',
            array('DBMF_topmenu_mementos', "mementos", 'DBMF_topmenu_mementosinfos',''),
            array(2,0,0,0),
            7,"dbmf_user",'dbmf3');

    }

    public function init6() {
        global $app;
/*
        $req = MySBDB::query('ALTER TABLE '.MySB_DBPREFIX.'dbmfblocks '.
            'ADD i_index int',
            "__init.php",
            false, "dbmf3");
*/
        MySBDBMFBlockHelper::indexBlocks();
        $req = MySBDB::query('ALTER TABLE '.MySB_DBPREFIX.'users '.
            'ADD dbmf_showcols varchar(512)',
            "__init.php",
            false, "dbmf3");
        $req = MySBDB::query('ALTER TABLE '.MySB_DBPREFIX.'dbmfblockrefs '.
            'ADD orderby int',
            "__init.php",
            false, "dbmf3");
    }

    public function init7() {
        global $app;
        $req = MySBDB::query('ALTER TABLE '.MySB_DBPREFIX.'dbmfmementos '.
            'ADD comments2 varchar(512)',
            "__init.php",
            false, "dbmf3");
        $req = MySBDB::query('ALTER TABLE '.MySB_DBPREFIX.'dbmfmementos '.
            'ADD group_edition int',
            "__init.php",
            false, "dbmf3");
    }

    public function init8() {
        global $app;
        MySBPluginHelper::delete('dbmf_showmail','dbmf3');
        $req = MySBDB::query('ALTER TABLE '.MySB_DBPREFIX.'dbmfcontacts '.
            'CHANGE COLUMN b1r08 mail varchar(512)',
            "__init.php",
            false, "dbmf3");
        $req = MySBDB::query('DELETE FROM '.MySB_DBPREFIX.'dbmfblockrefs '.
            'WHERE keyname=\'b1r08\'',
            "__init.php",
            false, "dbmf3");
    }

    public function init9() {
        global $app;
        MySBPluginHelper::delete('dbmf_showorga','dbmf3');
        MySBPluginHelper::delete('dbmf_showtels','dbmf3');
    }

    public function init10() {
        global $app;
        $req = MySBDB::query('ALTER TABLE '.MySB_DBPREFIX.'dbmfblockrefs '.
            'ADD alwaysshown int',
            "__init.php",
            false, "dbmf3");
    }

    public function init11() {
        global $app;
        MySBConfigHelper::create('dbmf_globalaccess','0',MYSB_VALUE_TYPE_BOOL,
            'Users have global access on all contacts', 'dbmf3');
    }

    public function init12() {
        global $app;
        MySBPluginHelper::create('dbmf_request','FrontPage',
            array("fprequest", '', '',''),
            array(0,0,0,0),
            5,"dbmf_user",'dbmf3');
        MySBPluginHelper::create('dbmf_infos','FrontPage',
            array("fpinfos", '', '',''),
            array(0,0,0,0),
            3,"dbmf_user",'dbmf3');
        MySBPluginHelper::create('export_menutext','MenuItem',
            array('DBMF_topmenu_export', "export", 'DBMF_topmenu_exportinfos',''),
            array(2,0,0,0),
            5,"dbmf_user",'dbmf3');
        MySBPluginHelper::create('addcontact_menutext','MenuItem',
            array('DBMF_topmenu_addcontact', "contact_add", 'DBMF_topmenu_addcontactinfos',''),
            array(1,0,0,0),
            4,"dbmf_editor",'dbmf3');
        MySBPluginHelper::create('blockedit_menutext','MenuItem',
            array('DBMF_topmenu_blockedit', "admin/structure", 'DBMF_topmenu_blockeditinfos',''),
            array(2,0,0,0),
            3,"dbmf_blockedit",'dbmf3');
        MySBPluginHelper::create('admindbmf_menutext','MenuItem',
            array('DBMF_adminmenu_dbmf', "admin/dbmf", 'DBMF_adminmenu_dbmfinfos',''),
            array(3,0,0,0),
            6,"admin",'dbmf3');
    }

    public function init13() {
        global $app;
        $req = MySBDB::query('CREATE TABLE '.MySB_DBPREFIX.'dbmfmementocatgs ( '.
            'id int not null primary key, '.
            'name varchar(64), '.
            'group_ids varchar(64) )
            DEFAULT CHARSET=utf8',
            "__init.php",
            false, "dbmf3");
        MySBDB::query('ALTER TABLE '.MySB_DBPREFIX.'dbmfmementos '.
            'DROP COLUMN group_id',
            "__init.php",
            false, "dbmf3");
        MySBDB::query('ALTER TABLE '.MySB_DBPREFIX.'dbmfmementos '.
            'ADD COLUMN memcatg_id int',
            "__init.php",
            false, "dbmf3");
        MySBDB::query('UPDATE '.MySB_DBPREFIX.'dbmfmementos '.
            'SET memcatg_id=1',
            "__init.php",
            false, "dbmf3");
        MySBDBMFMementoCatgHelper::create("");
        MySBRoleHelper::delete('dbmf_blockedit');
        $configrole = MySBRoleHelper::create('dbmf_config','Can configure DBMF3');
        $configrole->assignToGroup('admin',true);
        MySBPluginHelper::delete('blockedit_menutext','dbmf3');
        MySBPluginHelper::create('config_menutext','MenuItem',
            array('DBMF_topmenu_config', "admin/structure", 'DBMF_topmenu_configinfos',''),
            array(2,0,0,0),
            3,"dbmf_config",'dbmf3');
    }

    public function init14() {
        global $app;
        MySBDB::query('ALTER TABLE '.MySB_DBPREFIX.'dbmfmementos '.
            'ADD COLUMN is_notified int',
            "__init.php",
            false, "dbmf3");
        $selconf = MySBConfigHelper::create('dbmf_notify_freq','DBMF_memnotif_week',MYSB_VALUE_TYPE_VARCHAR64_SELECT,
            'Memento notification frequency (without new active)', 'dbmf3');
        $selconf->addSelectOption( 'DBMF_memnotif_week' );
        $selconf->addSelectOption( 'DBMF_memnotif_month' );
    }

    public function init15() {
        global $app;
        MySBConfigHelper::create('dbmf_ln_infos','',MYSB_VALUE_TYPE_TEXT,
            'Lastname informations', 'dbmf3');
        MySBConfigHelper::create('dbmf_fn_infos','',MYSB_VALUE_TYPE_TEXT,
            'Firstname informations', 'dbmf3');
        $req = MySBDB::query('ALTER TABLE '.MySB_DBPREFIX.'dbmfblockrefs '.
            'ADD infos varchar(512)',
            "__init.php",
            false, "dbmf3");
    }

    public function init16() {
        global $app;
        MySBPluginHelper::create('dbmf_exportsummary','DBMFExport',
            array('Summary', 'Display summary', 'libraries/export_summary.php',''),
            array(0,0,0,0),
            6,"dbmf_user",'dbmf3');
        MySBConfigHelper::delete('dbmf_showfields_colsnb','dbmf3');
    }

    public function init17() {
        global $app;
        $autosubsrole = MySBRoleHelper::create('dbmf_autosubs','Auto-subscribe process');

        MySBConfigHelper::create('dbmf_autosubs_blockref','',MYSB_VALUE_TYPE_VARCHAR512,
            'BlockRef filled with 1 when autosubs', 'dbmf3');
        MySBPluginHelper::create('autosubs_menutext','MenuItem',
            array('DBMF_topmenu_autosubs', "autosubs1&blanklay=1", 'DBMF_topmenu_autosubsinfos',''),
            array(1,0,0,0),
            6,"dbmf_autosubs",'dbmf3');
        $req = MySBDB::query('ALTER TABLE '.MySB_DBPREFIX.'dbmfblockrefs '.
            'ADD autosubs int',
            "__init.php",
            false, "dbmf3");
    }

    public function init18() {
        global $app;
        MySBPluginHelper::create('config_menutext','MenuItem',
            array('DBMF_topmenu_config', "admin/config&page=structure", 'DBMF_topmenu_configinfos',''),
            array(2,0,0,0),
            3,"dbmf_config",'dbmf3');
    }

    public function init19() {
        global $app;
        MySBPluginHelper::create('autosubs_menutext','MenuItem',
            array('DBMF_topmenu_autosubs', "autosubs/step1", 'DBMF_topmenu_autosubsinfos',''),
            array(1,0,0,0),
            6,"dbmf_autosubs",'dbmf3');
    }

    public function init20() {
        global $app;
        MySBConfigHelper::create('dbmf_autosubs_blockreflock','',MYSB_VALUE_TYPE_VARCHAR512,
            'Contact locked if this BlockRef is filled with 1', 'dbmf3');
        MySBConfigHelper::create('dbmf_autosubs_mailconfirm','1',MYSB_VALUE_TYPE_BOOL,
            'Send a confirmation mail', 'dbmf3');
        MySBConfigHelper::create('dbmf_autosubs_mailaddress','',MYSB_VALUE_TYPE_VARCHAR512,
            'Mail copy to', 'dbmf3');
        MySBConfigHelper::create('dbmf_autosubs_anonaccess','',MYSB_VALUE_TYPE_BOOL,
            'Anonymous access to autosubs', 'dbmf3');
    }


    public function init21() {
        global $app;
        MySBRoleHelper::delete('dbmf_autosubs');
        MySBConfigHelper::delete('dbmf_autosubs_blockref','dbmf3');
        MySBConfigHelper::delete('dbmf_autosubs_blockreflock','dbmf3');
        MySBConfigHelper::delete('dbmf_autosubs_mailconfirm','dbmf3');
        MySBConfigHelper::delete('dbmf_autosubs_mailaddress','dbmf3');
        MySBConfigHelper::delete('dbmf_autosubs_anonaccess','dbmf3');
        MySBPluginHelper::delete('autosubs_menutext','dbmf3');
        $req = MySBDB::query('ALTER TABLE '.MySB_DBPREFIX.'dbmfblockrefs DROP COLUMN autosubs',
            "__init.php",
            false, "dbmf3");
    }

    public function init22() {
        global $app;
        $req = MySBDB::query('ALTER TABLE '.MySB_DBPREFIX.'dbmfblockrefs '.
            'ADD params varchar(256)',
            "__init.php",
            false, "dbmf3");
    }

    public function init23() {
        global $app;
        MySBPluginHelper::create('mementosort_menutext','MenuItem',
            array('DBMF_topmenu_mementosort', "mementosort/step1", 'DBMF_topmenu_mementosortinfos',''),
            array(1,0,0,0),
            6,"dbmf_user",'dbmf3');
    }

    public function uninit() {
        global $app;

        MySBRoleHelper::delete('dbmf_user');
        MySBRoleHelper::delete('dbmf_editor');
        MySBRoleHelper::delete('dbmf_config');

        MySBDBMFExportHelper::delete(MySBDBMFExportHelper::getByName('DBMF_display')->id);
        $plugs = MySBPluginHelper::loadByType('DBMFExport');
        foreach($plugs as $plug)
            MySBPluginHelper::delete($plug->name,$plug->module);
        $plugs = MySBPluginHelper::loadByType('DBMFDisplay');
        foreach($plugs as $plug)
            MySBPluginHelper::delete($plug->name,$plug->module);

        MySBConfigHelper::delete('dbmf_globalaccess','dbmf3');
        MySBConfigHelper::delete('dbmf_notify_freq','dbmf3');
        MySBConfigHelper::delete('dbmf_ln_infos','dbmf3');
        MySBConfigHelper::delete('dbmf_fn_infos','dbmf3');

        //plugins
        MySBPluginHelper::delete('dbmf_exportupdate','dbmf3');
        MySBPluginHelper::delete('dbmf_exportcsv','dbmf3');
        MySBPluginHelper::delete('dbmf_exportmailscsv','dbmf3');
        MySBPluginHelper::delete('dbmf_exportmailing','dbmf3');
        MySBPluginHelper::delete('dbmf_exportdisplay','dbmf3');

        MySBPluginHelper::delete('dbmf_request','dbmf3');
        MySBPluginHelper::delete('dbmf_infos','dbmf3');
        MySBPluginHelper::delete('dbmfadmin_menutext','dbmf3');
        MySBPluginHelper::delete('admindbmf_menutext','dbmf3');
        MySBPluginHelper::delete('addcontact_menutext','dbmf3');
        MySBPluginHelper::delete('export_menutext','dbmf3');
        MySBPluginHelper::delete('config_menutext','dbmf3');
        MySBPluginHelper::delete('mementos_menutext','dbmf3');

    }

}
?>
