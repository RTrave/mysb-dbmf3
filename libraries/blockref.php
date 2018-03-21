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

define('MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_NO', 0);
define('MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_ASTEXT', 1);
define('MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_ASPLUG', 2);
define('MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_ASTEXTONLY', 3);


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
        parent::__update('dbmfblockrefs', (array) ($data_blockref));
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

    public function switchOrderBy() {
        if($this->orderby==1)
            $this->update( array( 'orderby'=>0 ) );
        else
            $this->update( array( 'orderby'=>1 ) );
    }

    public function setAlwaysShown($asvalue) {
        $this->update( array( 'alwaysshown'=>$asvalue ) );
    }

    public function getReducedName() {
        $strdb = _G($this->lname);
        if(strlen($strdb)<7) return $strdb;
        $newstr = '';
        $words = explode(' ',$strdb);
        foreach($words as $word) {
            if(strlen($word)>4) {
                $newstr .= preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,0}'.'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,4}).*#s','$1', $word);
                $newstr .= '. ';
            } else $newstr .= $word.' ';
        }
        return $newstr;
    }

    function displayPlugin($contact,$class_string='') {
        $output = "";
        $disp_class = 'w60';
        if( $this->type==MYSB_VALUE_TYPE_TEXT or
            $this->type==MYSB_VALUE_TYPE_VARCHAR512 )
            $disp_class = 'w180';
        elseif( $this->type==MYSB_VALUE_TYPE_VARCHAR64 or
                $this->type==MYSB_VALUE_TYPE_VARCHAR64_SELECT )
            $disp_class = 'w120';
        elseif( $this->type==MYSB_VALUE_TYPE_TEL or
                $this->type==MYSB_VALUE_TYPE_URL )
            $disp_class = 'w80';
        $output .= '
<div    class="cell_plug '.$class_string.' '.$disp_class.'"
        style="display: inline-block;">
<table title="'.$this->keyname.$contact->id.'"><tr>
    <td class="title">
        '.$this->getReducedName().'
    </td>
</tr><tr>
    <td class="text">';
        $column_name = $this->keyname;
        if( $this->type==MYSB_VALUE_TYPE_VARCHAR64_SELECT )
            $column_value = _G($contact->$column_name);
        elseif( $this->type==MYSB_VALUE_TYPE_TEL or
                $this->type==MYSB_VALUE_TYPE_URL )
            $column_value = '
        <div style="vertical-align: middle; display: inline-block;">'.$this->htmlFormNonEditable('',$contact->$column_name,MySBUtil::str2abbrv(_G($this->lname),4,4)).'</div>';
        elseif( $this->type==MYSB_VALUE_TYPE_INT ) {
            $column_value = $contact->$column_name;
            if( $column_value=='' ) $column_value = '0';
        } elseif( $this->type==MYSB_VALUE_TYPE_BOOL )
            $column_value = $this->htmlFormNonEditable('',$contact->$column_name );
        else $column_value = MySBUtil::str2html($contact->$column_name);
        $output .= '
        '.$column_value.'
    </td>
</tr></table>
</div>';
        return $output;
    }

    function setInfos($infos) {
        $this->update( array( "infos" => $infos ) );
    }

}


class MySBDBMFBlockRefHelper {

    public static function create($lname,$type,$block_id) {
        global $app;

        $req_lastid = MySBDB::query('SELECT keyname from '.MySB_DBPREFIX.'dbmfblockrefs '.
            'WHERE block_id='.$block_id.' '.
            'ORDER BY keyname DESC',
            "MySBDBMFBlockRefHelper::create($lname,$type,$block_id)" );
        $data_lastid = MySBDB::fetch_array($req_lastid);
        if($data_lastid['keyname']!='') {
            $tmpid = explode('r',$data_lastid['keyname']);
            $brid = ((int) $tmpid[1]) + 1;
        } else $brid = 1;
        if($brid>9) $brkeyname = 'b'.$block_id.'r'.$brid;
        else $brkeyname = 'b'.$block_id.'r0'.$brid;
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
            "($new_id, $block_id, '$brkeyname', '".MySBUtil::str2db($lname)."', $type, ".MYSB_DBMF_BLOCKREF_STATUS_INACTIVE.", ".$index.") ",
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

    public static function delete($id) {
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

    public static function load($forced=false) {
        global $app;
        if(isset($app->cache_dbmfblockrefs) and $forced==false)
            return $app->cache_dbmfblockrefs;
        $app->cache_dbmfblockrefs = array();
        $req_blockrefs = MySBDB::query("SELECT * FROM ".MySB_DBPREFIX."dbmfblockrefs ".
            "ORDER BY block_id,i_index",
            "MySBDBMFBlockRefHelper::load()",
            true, 'dbmf3');
        while($data_blockref = MySBDB::fetch_array($req_blockrefs)) {
            $blockref = new MySBDBMFBlockRef(-1,(array) ($data_blockref));
            $blockref->grp = 'dbmf3';
            $app->cache_dbmfblockrefs[$data_blockref['id']] = $blockref;
        }
        return $app->cache_dbmfblockrefs;
    }

    public static function loadAlwaysShown($value=0) {
        global $app;
        $blockrefs = MySBDBMFBlockRefHelper::load();
        $as_array = array();
        foreach( $blockrefs as $blockref ) {
            if( $value==0 and
                (   $blockref->alwaysshown==MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_ASTEXT or
                    $blockref->alwaysshown==MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_ASPLUG or
                    $blockref->alwaysshown==MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_ASTEXTONLY ) ) {
                $as_array[] = $blockref;
            } elseif( $blockref->alwaysshown==$value ) {
                $as_array[] = $blockref;
            }
        }
        return $as_array;
    }

    public static function getByID($id) {
        global $app;
        $blockrefs = MySBDBMFBlockRefHelper::load();
        if( isset($blockrefs[$id]) )
            return $blockrefs[$id];
        return null;
    }

    public static function getByKeyname($keyname) {
        global $app;
        $blockrefs = MySBDBMFBlockRefHelper::load();
        foreach( $blockrefs as $blockref )
            if( $blockref->keyname==$keyname )
                return $blockref;
        return null;
    }

}

?>
