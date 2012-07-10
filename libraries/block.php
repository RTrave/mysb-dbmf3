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


/**
 * DBMF Block class
 * 
 */
class MySBDBMFBlock extends MySBObject {

    public $id = null;
    public $name = null;
    public $lname = null;
    public $group_id = null;

    public function __construct($id=null, $data_block = array()) {
        global $app;
        if($id!=null) {
            $req_block = MySBDB::query("SELECT * FROM ".MySB_DBPREFIX.'dbmfblocks '.
                'WHERE id='.$id, 
                "MySBDBMFBlock::__construct($id)",
                false, 'dbmf3');
            $data_block = MySBDB::fetch_array($req_block);
        } else $id = $data_block['id'];
        parent::__construct((array) ($data_block));
        $req_blockref = MySBDB::query("SELECT * FROM ".MySB_DBPREFIX.'dbmfblock'.$id.'_ref ' ,
                "MySBDBMFBlock::__construct($id)",
                false, 'dbmf3');
        $data_blockref = MySBDB::fetch_array($req_blockref);
        parent::__construct((array) ($data_blockref), 'ref_');
    }

}

class MySBDBMFBlockHelper {

    public function create($lname) {
        global $app;
        $bid = MySBUTIL::lastid('dbmfblocks')+1;
        if($bid==0) $bid = 1;
        $new_block_name = 'dbmfblock'.$bid;
        $pri_group = MySBDBMFGroupHelper::get_primary($app->auth_user);
        if($pri_group==null) return; 
        MySBDB::query('INSERT INTO '.MySB_DBPREFIX."dbmfblocks VALUES ".
            "( $bid,'".$new_block_name."','".MySBUtil::str2db($lname)."',".$pri_group->id.")",
            "MySBDBMFBlockHelper::create($name,$lname)",
            true, "dbmf3");

        $table_ref_name = $new_block_name.'_ref';
        MySBDB::query('CREATE TABLE '.MySB_DBPREFIX.$table_ref_name.' ('.
            'id int not null, '.
            'name varchar(32), '.
            'lname varchar(64), '.
            'type varchar(64), '.
            'disabled int)',
            "MySBDBMFBlockHelper::create($name,$lname)",
            true, "dbmf3");
        return $new_block_name;
    }

    public function load() {
        global $app;
        $app->dbmfblocks = array();
        $req_dbmfblocks = MySBDB::query("SELECT * FROM ".MySB_DBPREFIX."dbmfblocks ".
                "ORDER BY id",
                "MySBDBMFBlockHelper::load()",
                true, 'dbmf3' );
        while($data_block = MySBDB::fetch_array($req_dbmfblocks)) {
            $app->dbmfblocks[] = new MySBDBMFBlock(null, $data_block);
        }
        return $app->dbmfblocks;
    }

}

?>
