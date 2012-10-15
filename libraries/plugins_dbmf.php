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


class MySBPluginDBMFEvent extends MySBPlugin {
    /**
     * value0: Plugin DBMFEvent Helper Class
     * ivalue0: placement
     */

    /**
     * Plugin constructor.
     * @param   array               Parameters of plugin
     */
    public function __construct($plugin = array()) {
        parent::__construct((array) ($plugin));
        $PlugHelper = $this->value0;
        if(class_exists($PlugHelper)) $this->plugref = new $PlugHelper();
        else $this->plugref = null;
    }

    /**
     * 
     * @param   
     */
    public function contactCreate($contact) {
        global $app;
        if( $this->plugref!=null and method_exists($this->plugref,'contactCreate') ) 
            return $this->plugref->contactCreate($contact);
    }

    /**
     * 
     * @param   
     */
    public function contactUpdate($contact) {
        global $app;
        if( $this->plugref!=null and method_exists($this->plugref,'contactUpdate') ) 
            return $this->plugref->contactUpdate($contact);
    }

    /**
     * 
     * @param   
     */
    public function contactDelete($contact) {
        global $app;
        if( $this->plugref!=null and method_exists($this->plugref,'contactDelete') ) 
            return $this->plugref->contactDelete($contact);
    }
}


class MySBPluginDBMFDisplay extends MySBPlugin {

    /**
     * value0: Plugin DBMFDisplay Helper Class
     * ivalue0: placement
     */


    /**
     * Plugin constructor.
     * @param   array               Parameters of plugin
     */
    public function __construct($plugin = array()) {
        parent::__construct((array) ($plugin));
        $PlugHelper = $this->value0;
        $this->plugref = new $PlugHelper();
    }

    /**
     * Table header cell code.
     * @param   
     */
    public function displayTDheader($placement) {
        global $app;
        if($placement!=$this->ivalue0) return;
        return $this->plugref->header($this);
    }

    /**
     * Table bobdy cell code.
     * @param   
     */
    public function displayTD($placement,$contact) {
        global $app;
        if($this->ivalue0!=$placement) return;
        return $this->plugref->td($contact);
    }

}



class DBMFPluginsDisplayOrganism {
    public function header($plugin) {
        $output = '<td width="280px"><i>'._G("DBMF_common_function").'</i><br>'._G("DBMF_common_organism").'</td>';
        return $output;
    }
    public function td($contact) {
        $output = '<td><i>'.$contact->b1r02.'</i><br>'.$contact->b1r03.'</td>';
        return $output;
    }
}

class DBMFPluginsDisplayTel {
    public function header($plugin) {
        $output = '<td width="160px">Tel</td>';
        return $output;
    }
    public function td($contact) {
        $output = '<td><i>'._G("DBMF_common_tel_1").':</i> '.$contact->b1r05.'<br><i>'._G("DBMF_common_tel_2").':</i> '.$contact->b1r06.'</td>';
        return $output;
    }
}

class DBMFPluginsDisplayMail {
    public function header($plugin) {
        $output = '<td width="24px"></td>';
        return $output;
    }
    public function td($contact) {
        $output = '';
        if($contact->b1r08!='') 
            $output .= '
        <a href="mailto:'.$contact->b1r08.'">
            <img src="modules/dbmf3/images/mail_icon24.png" 
                 alt="'._G('DBMF_mailto').' '.$contact->id.'" 
                 title="'._G('DBMF_mailto').' '.$contact->lastname.' '.$contact->firstname.' ('.$contact->id.')">
        </a>';
        return '<td>'.$output.'</td>';
    }

}


?>
