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
        if( class_exists($PlugHelper) )
            $this->plugref = new $PlugHelper();
    }

    public function displayIcons($placement,$contact) {
        global $app;
        if( !isset($this->plugref) or $this->plugref==null or $this->ivalue0!=$placement ) return;
        return $this->plugref->icons($this,$contact);
    }

}


?>
