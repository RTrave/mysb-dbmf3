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
 * DBMF Contact class
 *
 */
class MySBDBMFContact extends MySBObject {

    public $lastname = null;
    public $firstname = null;

    public function __construct($id=null, $data_contact = array()) {
        global $app;
        if(!isset($app->dbmf_cache_contacts)) $app->dbmf_cache_contacts = array();
        if($id!=null) {
            if( isset($app->dbmf_cache_contacts[$id]) and $app->dbmf_cache_contacts[$id]!='' ) {
                $data_contact = $app->dbmf_cache_contacts[$id];
            } else {
                $req_contact = MySBDB::query("SELECT * FROM ".MySB_DBPREFIX.'dbmfcontacts '.
                    'WHERE id='.$id
                    ,"MySBDBMFContact::__construct($id)",
                    false, 'dbmf3');
                $data_contact = MySBDB::fetch_array($req_contact);
                $app->dbmf_cache_contacts[$id] = $data_contact;
            }
        }
        parent::__construct((array) ($data_contact));
    }

    public function update( $data_contact ) {
        global $app;
        parent::__update('dbmfcontacts', (array) ($data_contact));
    }

    public function addMementoSimple($message) {
        global $app;
        $memento = MySBDBMFMementoHelper::create($app->auth_user->id,$this->id,MYSB_DBMF_MEMENTO_TYPE_PUNCTUAL);
        $today = getdate();
        $today_date =   $today['year'].'-'.$today['mon'].'-'.$today['mday'].' '.
                        $today['hours'].':'.$today['minutes'].':'.$today['seconds'];
        $memento->update( array(
            'date_memento' => $today_date,
            'comments' => $message,
            'group_edition' => 1,
            'type' => 1 ) );
    }

    public function duplicate() {
        global $app;
        //if($id==null) return null;
        /*
        $req_contact = MySBDB::query("SELECT * FROM ".MySB_DBPREFIX.'dbmfcontacts '.
                    'WHERE id='.$this->id
                    ,"MySBDBMFContact::duplicate()",
                    false, 'dbmf3');
        */
        $cols_sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS ".
                    "WHERE TABLE_NAME='".MySB_DBPREFIX."dbmfcontacts';";
        $cols_req = MySBDB::query($cols_sql,
                    "MySBDBMFContact::duplicate()",
                    false, 'dbmf3');
        $colstxt = "";
        while($col = MySBDB::fetch_array($cols_req)) {
            if($col[0]!='id') {
              if($colstxt!='') $colstxt .= ',';
              $colstxt .= $col[0];
            }
        }
        //echo "console.log('SQL:_".$cols_sql."_');";
        //echo "console.log('cols:".$colstxt."');";
        $cid = MySBDB::lastID('dbmfcontacts')+1;
        if($cid==0) $cid = 1;
        $dup_sql =  "INSERT INTO ".MySB_DBPREFIX."dbmfcontacts (".$colstxt.") ".
                    "SELECT ".$colstxt." FROM ".MySB_DBPREFIX."dbmfcontacts ".
                    "WHERE id=".$this->id." ".
                    "ON DUPLICATE KEY UPDATE id=".$cid;
        //echo "console.log('new ID:".$cid."');";
        MySBDB::query($dup_sql,
                      "MySBDBMFContact::duplicate()",
                      false, 'dbmf3');
        $lastid_req = MySBDB::query("SELECT LAST_INSERT_ID();",
                      "MySBDBMFContact::duplicate()",
                      false, 'dbmf3');
        $lastid = MySBDB::fetch_array($lastid_req);
        if(count($lastid)==0) return null;
        echo "console.log('last ID:".$lastid[0]."');";
        $dup_contact = new MySBDBMFContact($lastid[0]);
        $today = getdate();
        $today_date = $today['year'].'-'.$today['mon'].'-'.$today['mday'].' '.$today['hours'].':'.$today['minutes'].':'.$today['seconds'];
        //echo "console.log('rel ID:".$dup_contact->id."');";
        $dup_contact->update( array(
            'lastname' => $dup_contact->lastname.'(COPY)',
            'date_creat' => $today_date,
            'date_modif' => $today_date )
        );
        return $dup_contact;
    }

}


class MySBDBMFContactHelper {

    public static function create($lastname,$firstname,$mail) {
        global $app;
        $cid = MySBDB::lastID('dbmfcontacts')+1;
        if($cid==0) $cid = 1;
        $today = getdate();
        $today_date = $today['year'].'-'.$today['mon'].'-'.$today['mday'].' '.$today['hours'].':'.$today['minutes'].':'.$today['seconds'];
        MySBDB::query('INSERT INTO '.MySB_DBPREFIX.'dbmfcontacts '.
            '(id, lastname, firstname, mail, date_creat, date_modif) VALUES '.
            "(".$cid.", '".MySBUtil::str2db($lastname)."', '".MySBUtil::str2db($firstname)."', '".$mail."', '".$today_date."', '".$today_date."' ); ",
            "MySBDBMFContactHelper::create($lastname,$firstname,$mail)",
            true, 'dbmf3' );
        $new_contact = new MySBDBMFContact($cid);
        $pluginsEvent = MySBPluginHelper::loadByType('DBMFEvent');
        foreach($pluginsEvent as $plugin)
            $plugin->contactCreate($new_contact);
        return $new_contact;
    }

    public static function delete($id) {
        global $app;
        $contact = new MySBDBMFContact($id);
        $pluginsEvent = MySBPluginHelper::loadByType('DBMFEvent');
        foreach($pluginsEvent as $plugin)
            $plugin->contactDelete($contact);
        MySBDB::query('DELETE FROM '.MySB_DBPREFIX.'dbmfcontacts '.
            'WHERE id='.$id,
            "MySBDBMFContactHelper::delete($id)",
            true, 'dbmf3' );
        MySBDB::query('DELETE FROM '.MySB_DBPREFIX.'dbmfmementos '.
            'WHERE contact_id='.$id,
            "MySBDBMFContactHelper::delete($id)",
            true, 'dbmf3' );
    }

}

?>
