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


define('MYSB_DBMF_MEMENTO_TYPE_UNIQUE', 0);


/**
 * DBMF Mementos class
 * 
 */
class MySBDBMFMemento extends MySBObject {

    public $contact_id = null;

    public function __construct( $id=null, $data_memento=array() ) {
        global $app;
        if($id!=null) {
            $req_memento = MySBDB::query("SELECT * FROM ".MySB_DBPREFIX.'dbmfmementos '.
                'WHERE id='.$id
                ,"MySBDBMFMemento::__construct($id)",
                false, 'dbmf3');
            $data_memento = MySBDB::fetch_array($req_memento);
        }
        parent::__construct((array) ($data_memento));
    }

    public function update( $data_memento ) {
        global $app;
        parent::update('dbmfmementos', (array) ($data_memento));
    }

}


/**
 * DBMF Mementos class
 * 
 */
class MySBDBMFMementoHelper {

    public function create($owner_id,$contact_id,$type) {
        global $app;
        $mid = MySBDB::lastID('dbmfmementos')+1;
        if($mid==0) $mid = 1;
        MySBDB::query('INSERT INTO '.MySB_DBPREFIX.'dbmfmementos '.
            '(id, user_id, contact_id, type) VALUES '.
            "(".$mid.", '".$owner_id."', '".$contact_id."', ".$type." ); ",
            "MySBDBMFMementoHelper::create($owner_id,$contact_id,$type)",
            true, 'dbmf3' );
        $new_memento = new MySBDBMFMemento($mid);
        return $new_memento;
    }

    public function delete($id) {
        global $app;
        MySBDB::query('DELETE FROM '.MySB_DBPREFIX.'dbmfmementos '.
            'WHERE id='.$id,
            "MySBDBMFMementoHelper::delete($id)",
            true, 'dbmf3' );
    }


    public function load($contact_id=null) {
        global $app;
        $req_cond = '';
        if($contact_id!=null)
            $req_cond = 'WHERE contact_id='.$contact_id.' '; 
        $req_mementos = MySBDB::query("SELECT * FROM ".MySB_DBPREFIX."dbmfmementos ".
                $req_cond.
                "ORDER BY id",
                "MySBDBMFMementoHelper::load($contact_id)",
                true, 'dbmf3' );
        $mementos = array();
        while($data_memento = MySBDB::fetch_array($req_mementos)) {
            $mementos[$data_memento['id']] = new MySBDBMFMemento(null, $data_memento);
        }
        return $mementos;
    }

}

?>
