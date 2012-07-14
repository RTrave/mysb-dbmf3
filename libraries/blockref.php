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

define('MYSB_DBMF_BLOCKREF_TYPE_INT', 1);
define('MYSB_DBMF_BLOCKREF_TYPE_BOOL', 2);
define('MYSB_DBMF_BLOCKREF_TYPE_VARCHAR64', 3);
define('MYSB_DBMF_BLOCKREF_TYPE_VARCHAR512', 4);

define('MYSB_DBMF_BLOCKREF_STATUS_INACTIVE', 0);
define('MYSB_DBMF_BLOCKREF_STATUS_ACTIVE', 1);

/**
 * DBMF Block References class
 * 
 */
class MySBDBMFBlockRef extends MySBObject {
    public $id = null;
    public $block_id = null;
    public $name = null;
    public $lname = null;
    public $type = null;
    public $disabled = null;

    public function __construct( $data_blockref ) {
        global $app;
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

    public function htmlForm($value) {
        switch($this->type) {
            case MYSB_DBMF_BLOCKREF_TYPE_INT:
                return '<input type="text" name="'.$this->name.'" size="3" maxlength="3" value="'.$value.'">';
            case MYSB_DBMF_BLOCKREF_TYPE_BOOL:
                return '<input type="checkbox" name="'.$this->name.'" '.MySBUtil::form_ischecked($value,1).'>';
            case MYSB_DBMF_BLOCKREF_TYPE_VARCHAR64:
                return '<input type="text" name="'.$this->name.'" size="52" maxlength="62" value="'.$value.'">';
            case MYSB_DBMF_BLOCKREF_TYPE_VARCHAR512:
                return '<textarea name="comments" cols="60" rows="3">'.$contact->comments.'</textarea>';
        }
    }

    public function htmlProcess($value) {
        switch($this->type) {
            case MYSB_DBMF_BLOCKREF_TYPE_INT:
                return $value;
            case MYSB_DBMF_BLOCKREF_TYPE_BOOL:
                if($value=='on') return 1;
                return 0;
            case MYSB_DBMF_BLOCKREF_TYPE_VARCHAR64:
                return $value;
            case MYSB_DBMF_BLOCKREF_TYPE_VARCHAR512:
                return $value;
        }
    }

}

class MySBDBMFBlockRefHelper {

    public function create($lname,$type,$block_id) {
        $brid = MySBUTIL::lastid('dbmfblockrefs')+1;
        if($brid==0) $brid = 1;
        $brname = 'br'.$brid;
        MySBDB::query("INSERT INTO ".MySB_DBPREFIX.'dbmfblockrefs '.
            "(id, block_id, name, lname, type, status) VALUES ".
            "($brid, $block_id, '$brname', '$lname', $type, ".MYSB_DBMF_BLOCKREF_STATUS_INACTIVE.") ",
            "MySBDBMFBlockRefHelper::create($lname,$type,$block_id)",
            true, 'dbmf3');
        if( $type==MYSB_DBMF_BLOCKREF_TYPE_INT or $type==MYSB_DBMF_BLOCKREF_TYPE_BOOL ) 
            $sql_type = 'int';
        elseif( $type==MYSB_DBMF_BLOCKREF_TYPE_VARCHAR64 )
            $sql_type = 'varchar(64)';
        elseif( $type==MYSB_DBMF_BLOCKREF_TYPE_VARCHAR512 ) 
            $sql_type = 'varchar(512)';
        MySBDB::query("ALTER TABLE ".MySB_DBPREFIX.'dbmfcontacts '.
            'ADD COLUMN '.$brname.' '.$sql_type,
            "MySBDBMFBlockRefHelper::create($lname,$type,$block_id)",
            true, 'dbmf3');

        $new_blockref = new MySBDBMFBlockRef( array(
            'id' => $brid,
            'block_id' => $block_id,
            'name' => $brname,
            'lname' => $lname,
            'type' => $type,
            'disabled' => MYSB_DBMF_BLOCKREF_STATUS_INACTIVE,
            ) );
        if(isset($app->cache_dbmfblockrefs)) 
            $app->cache_dbmfblockrefs[$brid] = $new_blockref;
        return $new_blockref;
    }

    public function delete($id) {
        MySBDB::query("DELETE FROM ".MySB_DBPREFIX.'dbmfblockrefs WHERE '.
            "id=$id",
            "MySBDBMFBlockRefHelper::delete($id)",
            true, 'dbmf3');
        MySBDB::query("ALTER TABLE ".MySB_DBPREFIX.'dbmfcontacts '.
		    'DROP COLUMN br'.$id,
            "MySBDBMFBlockRefHelper::delete($id)",
            false, 'dbmf3');
        if(isset($app->cache_dbmfblockrefs)) 
            unset($app->cache_dbmfblockrefs[$id]);
    }

    public function load() {
        global $app;
        if(isset($app->cache_dbmfblockrefs)) 
            return $app->cache_dbmfblockrefs;
        $app->cache_dbmfblockrefs = array();
        $req_blockrefs = MySBDB::query("SELECT * FROM ".MySB_DBPREFIX.'dbmfblockrefs ',
                "MySBDBMFBlockRefHelper::load()",
                true, 'dbmf3');
        while($data_blockref = MySBDB::fetch_array($req_blockrefs)) {
            $app->cache_dbmfblockrefs[$data_blockref['id']] = new MySBDBMFBlockRef((array) ($data_blockref));
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
