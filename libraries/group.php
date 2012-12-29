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

global $app;

/**
 * DBMF group class
 * 
 */
class MySBDBMFGroup extends MySBGroup {

    public function __construct($id=-1,$data_group=array()) {
        global $app;
        if($id!=-1) {
            $req_group = MySBDB::query("SELECT * FROM ".MySB_DBPREFIX."groups ".
                "WHERE id=".$id,
                "MySBDBMFGroup::__construct($id)",
                true, 'dbmf3' );
            $data_group = MySBDB::fetch_array($req_group);
        }
        parent::__construct(-1, (array) ($data_group));
    }

    public function setPriority($priority='') {
        global $app;
        $datas = array( 'dbmf_priority' => $priority );
        $this->update((array) ($datas));
    }

}


/**
 * DBMF group helper class
 * 
 */
class MySBDBMFGroupHelper {

    public function load() {
        global $app;
        if(isset($app->dbmfgroups)) return $app->dbmfgroups;
        $app->dbmfgroups = array();
        $req_groups = MySBDB::query("SELECT * FROM ".MySB_DBPREFIX."groups ".
                "ORDER BY id",
                "MySBDBMFGroupHelper::load()",
                true, 'dbmf3', true );
        while($data_group = MySBDB::fetch_array($req_groups)) {
            $app->dbmfgroups[$data_group['id']] = new MySBDBMFGroup(-1, $data_group);
        }
        return $app->dbmfgroups;
    }

    public function get_primary($user) {
        global $app;
        $groups = MySBDBMFGroupHelper::load();
        $primary = null;
        $priority = 10;
        foreach($groups as $group) {
            $groupname = 'g'.$group->id;
            //echo $groupname.'/'.$user->$groupname.'/'.$group->dbmf_priority.'/'.$priority.'<br>';
            if($group->dbmf_priority>=1 and $user->$groupname==1) {
                if($group->dbmf_priority<$priority) {
                    $priority = $group->dbmf_priority;
                    $primary = $group;
                }
            }
        }
        return $primary;
    }

}



?>
