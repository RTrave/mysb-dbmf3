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


/**
 * DBMF Mementos class
 * 
 */
class MySBDBMFMementoCatg extends MySBObject {

    public $groups = array();

    public function __construct( $id=null, $data_mementocatg=array() ) {
        global $app;
        if( $id!=null ) {
            $req_mementocatg = MySBDB::query("SELECT * FROM ".MySB_DBPREFIX.'dbmfmementocatgs '.
                'WHERE id='.$id
                ,"MySBDBMFMemento::__construct($id)",
                false, 'dbmf3');
            $data_mementocatg = MySBDB::fetch_array($req_mementocatg);
        }
        parent::__construct((array) ($data_mementocatg));
    }

    public function update( $data_mementocatg ) {
        parent::update( 'dbmfmementocatgs', $data_mementocatg );
    }

    public function isAvailable($user=null) {
        global $app;
        if( $user==null ) $user = $app->auth_user;
        $groups_csv = new MySBCSValues($this->group_ids);
        foreach( $groups_csv->values as $groupid ) 
            if( $user->haveGroup($groupid) )
                return true;
    }

}

/**
 * DBMF Mementos class
 * 
 */
class MySBDBMFMementoCatgHelper extends MySBObject {

    public function create($name) {
        $mcid = MySBDB::lastID('dbmfmementocatgs')+1;
        if($mcid==0) $mcid = 1;
        $req_mementocatgs = MySBDB::query("INSERT INTO ".MySB_DBPREFIX."dbmfmementocatgs ".
                "(id,name,group_ids) VALUES ".
                "(".$mcid.",'".$name."','')",
                "MySBDBMFMementoCatgHelper::create($name)",
                true, 'dbmf3' );
    }

    public function load() {
        global $app;
        if( isset($app->cache_dbmfmemcatgs) ) 
            return $app->cache_dbmfmemcatgs;
        $req_memcatgs = MySBDB::query("SELECT * FROM ".MySB_DBPREFIX."dbmfmementocatgs ".
                "ORDER BY id",
                "MySBDBMFMementoCatgHelper::load()",
                true, 'dbmf3' );
        $app->cache_dbmfmemcatgs = array();
        while( $data_memcatgs = MySBDB::fetch_array($req_memcatgs )) {
            $app->cache_dbmfmemcatgs[$data_memcatgs['id']] = new MySBDBMFMementoCatg(null, $data_memcatgs);
        }
        return $app->cache_dbmfmemcatgs;
    }

    public function loadAvailable() {
        global $app;
        $memcatgs = MySBDBMFMementoCatgHelper::load();
        $available_memcatgs = array();
        foreach( $memcatgs as $memcatg )
            if( $memcatg->isAvailable() )
                $available_memcatgs[$memcatg->id] = $memcatg;
        return $available_memcatgs;
    }

    public function getByID($id) {
        global $app;
        $memcatgs = MySBDBMFMementoCatgHelper::load();
        foreach( $memcatgs as $memcatg ) 
            if( $memcatg->id==$id )
                return $memcatg;
        return null;
    }
}

?>
