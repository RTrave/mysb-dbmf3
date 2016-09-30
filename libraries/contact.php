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
