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
        $groups = MySBDBMFGroupHelper::load();
        if($groups[$this->groupedit_id]->dbmf_priority<=0) return false;
        if($app->auth_user->haveGroup($this->groupedit_id)) return true;
        return false;
    }

    public function isViewable() {
        global $app;
        if($this->isEditable()) return true;
        $groups = MySBDBMFGroupHelper::load();
        if($groups[$this->groupview_id]->dbmf_priority<=0) return false;
        if($app->auth_user->haveGroup($this->groupview_id)) return true;
        return false;
    }

    public function refAdd($lname,$type) {
        global $app;
        $blockref = MySBDBMFBlockRefHelper::create($lname,$type,$this->id);
        $this->blockrefs[$blockref->id] = $blockref;
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
    public function htmlProcessWhereClause($prefix) {
        global $_POST;
        $clause = '';
        if($_POST[$prefix.$this->name]=='on') {
            foreach($this->blockrefs as $blockref) {
                if($this->isEditable() and $blockref->isActive()) {
                    if($clause!='')  $clause .= ' '.$_POST['blockref_andorflag_'.$this->id].' ';
                    $clause .= $blockref->name."!='' ";
                }
            }
        }
        return $clause;
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
        MySBDB::query('INSERT INTO '.MySB_DBPREFIX."dbmfblocks ".
            "(id, name, lname, groupedit_id) VALUES ".
            "( $bid,'".$new_block_name."','".MySBUtil::str2db($lname)."',".$pri_group->id." )",
            "MySBDBMFBlockHelper::create($name,$lname)",
            true, "dbmf3");
        $new_block = new MySBDBMFBlock($bid);
        if(isset($app->cache_dbmfblocks)) 
            $app->cache_dbmfblocks[$brid] = $new_block;
        return $new_block;
    }

    public function load() {
        global $app;
        if(isset($app->cache_dbmfblocks)) 
            return $app->cache_dbmfblocks;
        $app->cache_dbmfblocks = array();
        $req_dbmfblocks = MySBDB::query("SELECT * FROM ".MySB_DBPREFIX."dbmfblocks ".
                "ORDER BY id",
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

}

?>
