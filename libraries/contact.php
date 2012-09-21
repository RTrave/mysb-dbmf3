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

    public $prefix = null;
    public $lastname = null;
    public $firstname = null;
    public $adress_1 = null;
    public $adress_2 = null;
    public $tel_1 = null;
    public $tel_2 = null;
    public $tel_fax = null;
    public $mail = null;
    public $function = null;
    public $organism = null;
    public $date_creat = null;
    public $date_modif = null;
    public $comments = null;

    public function __construct($id=null, $data_contact = array()) {
        global $app;
        if($id!=null) {
            $req_contact = MySBDB::query("SELECT * FROM ".MySB_DBPREFIX.'dbmfcontacts '.
                'WHERE id='.$id
                ,"MySBDBMFContact::__construct($id)",
                false, 'dbmf3');
            $data_contact = MySBDB::fetch_array($req_contact);
        }
        parent::__construct((array) ($data_contact));
    }
}

class MySBDBMFContactHelper {

    public function create($lastname,$firstname) {
        global $app;
        $cid = MySBDB::lastID('dbmfcontacts')+1;
        if($cid==0) $cid = 1;
        $today = getdate();
        $today_date = $today['year'].'-'.$today['mon'].'-'.$today['mday'].' '.$today['hours'].':'.$today['minutes'].':'.$today['seconds'];
        MySBDB::query('INSERT INTO '.MySB_DBPREFIX.'dbmfcontacts '.
            '(id, lastname, firstname, date_creat, date_modif) VALUES '.
            "(".$cid.", '".$lastname."', '".$firstname."', '".$today_date."', '".$today_date."' ); ",
            "MySBDBMFContact::create($title,$date,$pass_ids)",
            true, 'dbmf3' );
        $new_contact = new MySBDBMFContact($cid);
        return $new_contact;
    }

}

?>
