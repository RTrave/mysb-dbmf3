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


define('MYSB_DBMF_MEMENTO_TYPE_PUNCTUAL', 0);
define('MYSB_DBMF_MEMENTO_TYPE_MONTHOFYEAR', 1);
define('MYSB_DBMF_MEMENTO_TYPE_DAYOFMONTH', 2);


/**
 * DBMF Mementos class
 * 
 */
class MySBDBMFMemento extends MySBObject {

    public $user_id = null;

    public $contact_id = null;

    public $memcatg_id = null;

    public $type = MYSB_DBMF_MEMENTO_TYPE_PUNCTUAL;

    public $date_memento = '';

    public $dayofmonth_memento = 0;

    public $monthofyear_memento = 0;

    public $date_process = '';

    public $comments = '';

    public $comments2 = '';

    public $group_edition = 0;


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

    public function setCategory($memcatg_id) {
        global $app;
        if($memcatg_id=='0' or $memcatg_id==0) $memcatg_id = '';
        $this->update( array(
            'memcatg_id' => $memcatg_id ));
    }
/*
    public function setOwner($group_id) {
        global $app;
        //$user_id = $app->auth_user->id;
        if($group_id=='0' or $group_id==0) $group_id = '';
        parent::update('dbmfmementos', array(
            'group_id' => $group_id ));
    }
*/
    public function isEditable() {
        global $app;
        if( $this->user_id==$app->auth_user->id ) return true;
        $memcatg = MySBDBMFMementoCatgHelper::getByID($this->memcatg_id);
        if( $memcatg->isAvailable() and $this->group_edition==1 ) return true;
        return false;
    }

    public function getDate() {
        global $app;
        switch($this->type) {
            case MYSB_DBMF_MEMENTO_TYPE_PUNCTUAL:
                $memento_date = new MySBDateTime($this->date_memento);
                return $memento_date->strEBY_l();
            case MYSB_DBMF_MEMENTO_TYPE_MONTHOFYEAR:
                switch($this->monthofyear_memento) {
                    case 1: return _G('DBMF_memento_moy_1');
                    case 2: return _G('DBMF_memento_moy_2');
                    case 3: return _G('DBMF_memento_moy_3');
                    case 4: return _G('DBMF_memento_moy_4');
                    case 5: return _G('DBMF_memento_moy_5');
                    case 6: return _G('DBMF_memento_moy_6');
                    case 7: return _G('DBMF_memento_moy_7');
                    case 8: return _G('DBMF_memento_moy_8');
                    case 9: return _G('DBMF_memento_moy_9');
                    case 10: return _G('DBMF_memento_moy_10');
                    case 11: return _G('DBMF_memento_moy_11');
                    case 12: return _G('DBMF_memento_moy_12');
                }
            case MYSB_DBMF_MEMENTO_TYPE_DAYOFMONTH:
                ;
        }
    }

    public function isActive() {
        global $app;
        //$current_date = new MySBDateTime('now');
        switch($this->type) {
            case MYSB_DBMF_MEMENTO_TYPE_PUNCTUAL:
                $memento_date = new MySBDateTime($this->date_memento);
                if($memento_date->absDiff('h')<0) return false;
                if($this->date_process=='') return true;
                $process_date = new MySBDateTime($this->date_process);
                //echo $memento_date->getRest($process_date);
                //if($memento_date->getRest($process_date)>=0) return false;
                if($memento_date->absDiff('h',$process_date)>=0) return false;
                return true;
            case MYSB_DBMF_MEMENTO_TYPE_MONTHOFYEAR:
                $current_date = new MySBDateTime('now');
                $cmonth = (int) $current_date->str_get('%m');
                $cyear = $current_date->str_get('%Y');
                //echo $cmonth.' '.$cyear;
                $memento_date = new MySBDateTime($cyear.'-'.$this->monthofyear_memento.'-1');
                //echo $memento_date->strEBY_l();
                //echo '/'.$memento_date->getRest();
                //if($memento_date->getRest()<0) return false;
                if($memento_date->absDiff('h')<0) return false;
                if($this->date_process=='') return true;
                $process_date = new MySBDateTime($this->date_process);
                if($memento_date->absDiff('h',$process_date)>=0) return false;
                return true;
            case MYSB_DBMF_MEMENTO_TYPE_DAYOFMONTH:
                ;
        }
    }

    public function process() {
        global $app;
        $current_date = new MySBDateTime();
        $this->update(array(
            'date_process' => $current_date->date_string ));
    }

    public function unprocess() {
        global $app;
        //$this->update(array('date_process' => '(null)' ));
        MySBDB::query('UPDATE '.MySB_DBPREFIX.'dbmfmementos SET date_process=(null) WHERE id='.$this->id,
            "MySBDBMFMemento::unprocess()", false, 'dbmf3' );
    }

}


/**
 * DBMF Mementos class
 * 
 */
class MySBDBMFMementoHelper {

    public function create($memcatg_id,$contact_id,$type) {
        global $app;
        $mid = MySBDB::lastID('dbmfmementos')+1;
        if($mid==0) $mid = 1;
        MySBDB::query('INSERT INTO '.MySB_DBPREFIX.'dbmfmementos '.
            '(id, user_id, contact_id, type) VALUES '.
            "(".$mid.", '".$app->auth_user->id."', '".$contact_id."', ".$type." ); ",
            "MySBDBMFMementoHelper::create($memcatg_id,$contact_id,$type)",
            true, 'dbmf3' );
        $new_memento = new MySBDBMFMemento($mid);
        $new_memento->setCategory($memcatg_id);
        return $new_memento;
    }

    public function delete($id) {
        global $app;
        MySBDB::query('DELETE FROM '.MySB_DBPREFIX.'dbmfmementos '.
            'WHERE id='.$id,
            "MySBDBMFMementoHelper::delete($id)",
            true, 'dbmf3' );
    }

    private function req_cond() {
        global $app;
        $user = $app->auth_user;
        $cond = '(user_id='.$user->id;
        $memcatgs = MySBDBMFMementoCatgHelper::loadAvailable();
        foreach( $memcatgs as $memcatg ) 
            $cond .= ' or memcatg_id='.$memcatg->id;
        return $cond .= ')';
    }

    public function load($contact_id=null,$memcatg_id=0) {
        global $app;
        $req_cond = MySBDBMFMementoHelper::req_cond();
        if($contact_id!=null)
            $req_cond .= ' and contact_id='.$contact_id; 
        if($memcatg_id!=0)
            $req_cond .= ' and memcatg_id='.$memcatg_id; 
        $req_mementos = MySBDB::query("SELECT * FROM ".MySB_DBPREFIX."dbmfmementos ".
                "WHERE (".$req_cond.") ".
                "ORDER BY type,monthofyear_memento,date_memento,date_process",
                "MySBDBMFMementoHelper::load($contact_id)",
                true, 'dbmf3' );
        $mementos = array();
        while($data_memento = MySBDB::fetch_array($req_mementos)) {
            //$mementos[$data_memento['id']] = new MySBDBMFMemento(null, $data_memento);
            $mementos[] = new MySBDBMFMemento(null, $data_memento);
        }
        return $mementos;
    }

/*
    public function loadByUserID($user_id) {
        global $app;
        $req_cond = '';
        $req_mementos = MySBDB::query("SELECT * FROM ".MySB_DBPREFIX."dbmfmementos ".
                "WHERE user_id=".$user_id." ".
                "ORDER BY date_memento",
                "MySBDBMFMementoHelper::loadByUserID($user_id)",
                true, 'dbmf3' );
        $mementos = array();
        while($data_memento = MySBDB::fetch_array($req_mementos)) {
            $mementos[$data_memento['id']] = new MySBDBMFMemento(null, $data_memento);
        }
        return $mementos;
    }
*/

    public function loadActives($memcatg_id=0) {
        global $app;
        $req_cond = MySBDBMFMementoHelper::req_cond();
        if($memcatg_id!=0)
            $req_cond .= ' and memcatg_id='.$memcatg_id; 
        $req_mementos = MySBDB::query("SELECT * FROM ".MySB_DBPREFIX."dbmfmementos ".
                "WHERE (".$req_cond.") ".
                "ORDER BY type,monthofyear_memento,date_memento,date_process",
                "MySBDBMFMementoHelper::loadActives()",
                true, 'dbmf3' );
        $mementos = array();
        while($data_memento = MySBDB::fetch_array($req_mementos)) {
            $mementos[$data_memento['id']] = new MySBDBMFMemento(null, $data_memento);
        }
        $act_mementos = array();
        foreach($mementos as $memento) {
            if($memento->isActive())
                $act_mementos[] = $memento;
        }
        return $act_mementos;
    }

}

?>
