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


define('MYSB_DBMF_BLOCKREF_STATUS_INACTIVE', 0);
define('MYSB_DBMF_BLOCKREF_STATUS_ACTIVE', 1);


/**
 * DBMF Block References class
 * 
 */
class MySBDBMFBlockRef extends MySBValue {
    public $block_id = null;
    public $keyname = null;
    public $lname = null;
    public $disabled = null;

    public function __construct( $id=-1, $data_blockref=array() ) {
        global $app;
        if($id!=-1) {
            $req_blockrefs = MySBDB::query("SELECT * FROM ".MySB_DBPREFIX.'dbmfblockrefs WHERE '.
                'id='.$id,
                "MySBDBMFBlockRef::__construct()",
                true, 'dbmf3');
            $data_blockref = MySBDB::fetch_array($req_blockrefs);
        }
        parent::__construct((array) ($data_blockref));
    }

    public function update( $data_blockref ) {
        global $app;
        parent::update('dbmfblockrefs', (array) ($data_blockref));
    }

    public function statusSwitch() {
        if($this->status==MYSB_DBMF_BLOCKREF_STATUS_ACTIVE) 
            $this->update( array( 'status'=>MYSB_DBMF_BLOCKREF_STATUS_INACTIVE ) );
        else
            $this->update( array( 'status'=>MYSB_DBMF_BLOCKREF_STATUS_ACTIVE ) );
    }

    public function isActive() {
        if($this->status==MYSB_DBMF_BLOCKREF_STATUS_ACTIVE) return true;
        return FALSE;
    }

    public function indexUP() {
        MySBDB::query("UPDATE ".MySB_DBPREFIX."dbmfblockrefs SET ".
                "i_index=".($this->i_index-3)." ".
                "WHERE id=".$this->id,
                "MySBDBMFBlockRef::indexUP()",
                true, 'dbmf3');
        $block = MySBDBMFBlockHelper::getByID($this->block_id);
        $block->indexCheck();
    }

    public function indexDOWN() {
        MySBDB::query("UPDATE ".MySB_DBPREFIX."dbmfblockrefs SET ".
                "i_index=".($this->i_index+3)." ".
                "WHERE id=".$this->id,
                "MySBDBMFBlockRef::indexUP()",
                true, 'dbmf3');
        $block = MySBDBMFBlockHelper::getByID($this->block_id);
        $block->indexCheck();
    }

}


class MySBDBMFBlockRefHelper {

    public function create($lname,$type,$block_id) {
        global $app;

        $req_lastid = MySBDB::query('SELECT keyname from '.MySB_DBPREFIX.'dbmfblockrefs '.
            'WHERE block_id='.$block_id.' '.
            'ORDER BY keyname DESC',
            "MySBDB::lastID($table)" );
        $data_lastid = MySBDB::fetch_array($req_lastid);
        if($data_lastid['keyname']!='') {
            $tmpid = explode('r',$data_lastid['keyname']);
            $brid = ((int) $tmpid[1]) + 1;
        } else $brid = 1;
        $brkeyname = 'b'.$block_id.'r'.$brid;
        $req_blockrefs = MySBDB::query("SELECT * FROM ".MySB_DBPREFIX."dbmfblockrefs ".
            "WHERE block_id=".$block_id." ".
            "ORDER by i_index DESC",
            "MySBDBMFBlockRefHelper::create()",
            true, 'dbmf3');
        $data_blockref = MySBDB::fetch_array($req_blockrefs);
        $new_id = MySBDB::lastID('dbmfblockrefs')+1;
        $index = $data_blockref['i_index'] + 2;
        MySBDB::query("INSERT INTO ".MySB_DBPREFIX.'dbmfblockrefs '.
            "(id, block_id, keyname, lname, type, status, i_index) VALUES ".
            "($new_id, $block_id, '$brkeyname', '$lname', $type, ".MYSB_DBMF_BLOCKREF_STATUS_INACTIVE.", ".$index.") ",
            "MySBDBMFBlockRefHelper::create($lname,$type,$block_id)",
            true, 'dbmf3');
        $new_blockref = new MySBDBMFBlockRef($new_id);
        $new_blockref->grp = 'dbmf3';
        MySBDB::query("ALTER TABLE ".MySB_DBPREFIX.'dbmfcontacts '.
            'ADD COLUMN '.$brkeyname.' '.$new_blockref->getSQLType(),
            "MySBDBMFBlockRefHelper::create($lname,$type,$block_id)",
            true, 'dbmf3');
        if(isset($app->cache_dbmfblockrefs)) 
            $app->cache_dbmfblockrefs[$new_id] = $new_blockref;
        return $new_blockref;
    }

    public function delete($id) {
        global $app;
        $blockref = MySBDBMFBlockRefHelper::getByID($id);
        MySBDB::query("DELETE FROM ".MySB_DBPREFIX.'dbmfblockrefs WHERE '.
            "id=$id",
            "MySBDBMFBlockRefHelper::delete($id)",
            true, 'dbmf3');
        MySBDB::query("ALTER TABLE ".MySB_DBPREFIX.'dbmfcontacts '.
		    'DROP COLUMN '.$blockref->keyname,
            "MySBDBMFBlockRefHelper::delete($id)",
            false, 'dbmf3');
        if(isset($app->cache_dbmfblockrefs)) 
            unset($app->cache_dbmfblockrefs[$id]);
    }

    public function load($forced=false) {
        global $app;
        if(isset($app->cache_dbmfblockrefs) and $forced==false) 
            return $app->cache_dbmfblockrefs;
        $app->cache_dbmfblockrefs = array();
        $req_blockrefs = MySBDB::query("SELECT * FROM ".MySB_DBPREFIX."dbmfblockrefs ".
            "ORDER BY i_index",
            "MySBDBMFBlockRefHelper::load()",
            true, 'dbmf3');
        while($data_blockref = MySBDB::fetch_array($req_blockrefs)) {
            $blockref = new MySBDBMFBlockRef(-1,(array) ($data_blockref));
            $blockref->grp = 'dbmf3';
            $app->cache_dbmfblockrefs[$data_blockref['id']] = $blockref;
        }
        return $app->cache_dbmfblockrefs;
    }

    public function getByID($id) {
        global $app;
        $blockrefs = MySBDBMFBlockRefHelper::load();
        return $blockrefs[$id];
    }

}

?>
