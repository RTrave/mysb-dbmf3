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
    public $groupedit_id = null;

    public function __construct($id=-1, $data_block = array()) {
        global $app;
        if($id!=-1) {
            $req_block = MySBDB::query("SELECT * FROM ".MySB_DBPREFIX.'dbmfblocks '.
                'WHERE id='.$id, 
                "MySBDBMFBlock::__construct($id)",
                false, 'dbmf3');
            $data_block = MySBDB::fetch_array($req_block);
        } else $id = $data_block['id'];
        parent::__construct((array) ($data_block));
        $this->blockrefs = array();
        $blockrefs = MySBDBMFBlockRefHelper::load();
        foreach($blockrefs as $blockref)
            if($blockref->block_id==$this->id)
                $this->blockrefs[$blockref->id] = $blockref;
    }

    public function update($data_block) {
        parent::update( 'dbmfblocks', $data_block );
    }

    public function isEditable() {
        global $app;
        if(!MySBRoleHelper::checkAccess('dbmf_editor',false)) return false;
        $groups = MySBDBMFGroupHelper::load();
        if($groups[$this->groupedit_id]->dbmf_priority<=0) return false;
        if($app->auth_user->haveGroup($this->groupedit_id)) return true;
        return false;
    }

    public function isViewable() {
        global $app;
        if($this->isEditable()) return true;
        if(!MySBRoleHelper::checkAccess('dbmf_user',false)) return false;
        $groups = MySBDBMFGroupHelper::load();
        if($groups[$this->groupedit_id]->dbmf_priority<=0) return false;
        if($app->auth_user->haveGroup($this->groupedit_id)) return true;
        return false;
    }

    public function refAdd($lname,$type) {
        global $app;
        $blockref = MySBDBMFBlockRefHelper::create($lname,$type,$this->id);
        $this->blockrefs[$blockref->id] = $blockref;
        return $blockref;
    }

    public function refDel($id) {
        MySBDBMFBlockRefHelper::delete($id);
        unset($this->blockrefs[$id]);
    }

    /**
     * Get the HTML input form to search matching values.
     * @param   string  $prefix             form name prefix
     * @return  string                      input form in HTML format.
     */
    public function htmlFormWhereClause($prefix) {
        global $app;
        return '<input type="checkbox" name="'.$prefix.$this->name.'">';
    }

    /**
     * Get the SQL 'where' part from the HTML input form to search matching values.
     * @param   string  $prefix             form name prefix
     * @return  string                      WHERE condition in SQL format.
     */
    public function htmlProcessWhereClause($prefix='') {
        global $_POST;
        if($prefix=='') { // force check conditions
            $check_flag = 'on';
            $andor_flag = 'or';
        } else {
            if( isset($_POST[$prefix.$this->name]) ) $check_flag = $_POST[$prefix.$this->name];
            else $check_flag = '';
            if( isset($_POST['blockref_andorflag_'.$this->id]) ) $andor_flag = $_POST['blockref_andorflag_'.$this->id];
            else $andor_flag = 'or';
        }
        $clause = '';
        if($check_flag=='on') {
            foreach($this->blockrefs as $blockref) {
                if($blockref->isActive()) {
                    if($clause!='')  $clause .= ' '.$andor_flag.' ';
                    $clause .= $blockref->keyname."!='' ";
                }
            }
        }
        return $clause;
    }

    public function indexCheck() {
        global $app;
        $index = 1;
        $req_blockrefs = MySBDB::query("SELECT * FROM ".MySB_DBPREFIX."dbmfblockrefs ".
            "WHERE block_id=".$this->id." ".
            "ORDER by i_index",
            "MySBDBMFBlock::indexCheck()",
            true, 'dbmf3');
        while($data_blockref = MySBDB::fetch_array($req_blockrefs)) {
            MySBDB::query("UPDATE ".MySB_DBPREFIX."dbmfblockrefs SET ".
                "i_index=".$index." ".
                "WHERE id=".$data_blockref['id'],
                "MySBDBMFBlock::indexCheck()",
                true, 'dbmf3');
            $index += 2;
        }
        $this->blockrefs = array();
        $blockrefs = MySBDBMFBlockRefHelper::load(true);
        foreach($blockrefs as $blockref)
            if($blockref->block_id==$this->id)
                $this->blockrefs[$blockref->id] = $blockref;
    }

    public function indexUP() {
        MySBDB::query("UPDATE ".MySB_DBPREFIX."dbmfblocks SET ".
                "i_index=".($this->i_index-3)." ".
                "WHERE id=".$this->id,
                "MySBDBMFBlock::indexUP()",
                true, 'dbmf3');
        MySBDBMFBlockHelper::indexBlocks();
    }

    public function indexDOWN() {
        MySBDB::query("UPDATE ".MySB_DBPREFIX."dbmfblocks SET ".
                "i_index=".($this->i_index+3)." ".
                "WHERE id=".$this->id,
                "MySBDBMFBlock::indexUP()",
                true, 'dbmf3');
        MySBDBMFBlockHelper::indexBlocks();
    }

}

class MySBDBMFBlockHelper {

    public function create($lname) {
        global $app;
        $bid = MySBDB::lastID('dbmfblocks')+1;
        if($bid==0) $bid = 1;
        $new_block_name = 'dbmfblock'.$bid;
        $pri_group = MySBDBMFGroupHelper::get_primary($app->auth_user);
        if($pri_group==null) $pri_group_id = 1;
        else $pri_group_id = $pri_group->id; 
        MySBDB::query('INSERT INTO '.MySB_DBPREFIX."dbmfblocks ".
            "(id, name, lname, groupedit_id) VALUES ".
            "( $bid,'".$new_block_name."','".MySBUtil::str2db($lname)."',".$pri_group_id." )",
            "MySBDBMFBlockHelper::create($name,$lname)",
            true, "dbmf3");
        $new_block = new MySBDBMFBlock($bid);
        if(isset($app->cache_dbmfblocks)) 
            $app->cache_dbmfblocks[$brid] = $new_block;
        return $new_block;
    }

    public function delete($id) {
        $block = MySBDBMFBlockHelper::getByID($id);
        MySBDB::query("DELETE FROM ".MySB_DBPREFIX.'dbmfblockrefs WHERE '.
            "block_id=$id",
            "MySBDBMFBlockHelper::delete($id)",
            true, 'dbmf3');
        MySBDB::query("DELETE FROM ".MySB_DBPREFIX.'dbmfblocks WHERE '.
            "id=$id",
            "MySBDBMFBlockHelper::delete($id)",
            true, 'dbmf3');
        MySBDBMFBlockHelper::load(true);
    }

    public function load($force=false) {
        global $app;
        if(isset($app->cache_dbmfblocks) and $force==false) 
            return $app->cache_dbmfblocks;
        $app->cache_dbmfblocks = array();
        $req_dbmfblocks = MySBDB::query("SELECT * FROM ".MySB_DBPREFIX."dbmfblocks ".
                "ORDER BY i_index",
                "MySBDBMFBlockHelper::load()",
                true, 'dbmf3' );
        while($data_block = MySBDB::fetch_array($req_dbmfblocks)) {
            $app->cache_dbmfblocks[$data_block['id']] = new MySBDBMFBlock(-1, $data_block);
        }
        return $app->cache_dbmfblocks;
    }

    public function getByID($id) {
        global $app;
        $blocks = MySBDBMFBlockHelper::load();
        return $blocks[$id];
    }

    public function indexBlocks() {
        global $app;
        $index = 1;
        $req_blocks = MySBDB::query("SELECT * FROM ".MySB_DBPREFIX."dbmfblocks ".
            "ORDER by i_index",
            "MySBDBMFBlockHelper::indexBlocks()",
            true, 'dbmf3');
        while($data_block = MySBDB::fetch_array($req_blocks)) {
            MySBDB::query("UPDATE ".MySB_DBPREFIX."dbmfblocks SET ".
                "i_index=".$index." ".
                "WHERE id=".$data_block['id'],
                "MySBDBMFBlockHelper::indexBlocks()",
                true, 'dbmf3');
            $index += 2;
        }
        MySBDBMFBlockHelper::load(true);
    }

    public function sqlWhereClauseOwner() {
        global $app;
        $blocks = MySBDBMFBlockHelper::load();
        $clause_owner = '';
        if( !MySBConfigHelper::Value('dbmf_globalaccess','dbmf3') ) {
            foreach($blocks as $block) {
                $clause_owner_part = '';
                if($block->id!=1 and $block->isViewable()) {
                    if($clause_owner_part!='')  $clause_owner_part .= ' or ';
                    $clause_owner_part .= $block->htmlProcessWhereClause();
                }
                if($clause_owner!='' and $clause_owner_part!='') $clause_owner .= ' or ';
                if($clause_owner_part!='') $clause_owner .= $clause_owner_part;
            }
            if($clause_owner=='') {
                $app->pushAlert(_G('DBMF_no_rights'));
            }
        }
        return $clause_owner;
    }

}

?>
