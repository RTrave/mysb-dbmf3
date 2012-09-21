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
 * DBMF Export class
 * 
 */
class MySBDBMFExport extends MySBObject {

    public $type = null;
    public $name = null;
    public $comments = null;
    public $config = null;
    public $group_id = 1;

    public function __construct($id=-1, $data_export = array()) {
        global $app;
        if($id!=-1) {
            $req_export = MySBDB::query("SELECT * FROM ".MySB_DBPREFIX.'dbmfexports '.
                'WHERE id='.$id, 
                "MySBDBMFExport::__construct($id)",
                false, 'dbmf3');
            $data_export = MySBDB::fetch_array($req_export);
        } else $id = $data_export['id'];
        parent::__construct((array) ($data_export));
        $this->config_array = array();
        $config_values = explode(';',$this->config);
        foreach($config_values as $value) {
            $config = explode('=',$value);
            if($config[0]!='') 
                $this->config_array[$config[0]] = $config[1];
        }
    }

    public function update($data_export) {
        parent::update( 'dbmfexports', $data_export );
        $config_values = explode(';',$this->config);
        foreach($config_values as $value) {
            $config = explode('=',$value);
            if($config[0]!='') 
                $this->config_array[$config[0]] = $config[1];
        }    }

    public function displayConfig() {
        $str_res = '';
        foreach($this->config_array as $name => $config) {
            $str_res .= $name.' = '.$config.'<br>';
        }
        return $str_res;
    }

    /**
     * Config form display
     * @param   
     */
    public function htmlConfigForm() {
        return _G('DBMF_export_noconfig');
    }

    /**
     * Config form process
     * @param   
     */
    public function htmlConfigProcess() {
    }

    /**
     * Parameters form display
     * @param   
     */
    public function htmlParamForm() {
        return _G('DBMF_export_noparam');
    }

    /**
     * Parameters form process
     * @param   
     */
    public function htmlParamProcess() {
    }

    /**
     * Search result output
     * @param   
     */
    public function htmlResultOutput($results) {
        return '<p>No export output</p>';
    }

}

class MySBDBMFExportHelper {

    public function create($name,$type,$comments,$config,$group_id) {
        global $app;
        $bid = MySBDB::firstID('dbmfexports');
        if($bid==0) $bid = 1;
        MySBDB::query('INSERT INTO '.MySB_DBPREFIX."dbmfexports ".
            "(id, type, name, comments, config, group_id) VALUES ".
            "( $bid,'".$type."','".$name."','".MySBUtil::str2db($comments)."','".$config."',".$group_id." )",
            "MySBDBMFExportHelper::create($name,$type)",
            true, "dbmf3");
        $exportClass = 'MySBDBMFExport'.$type;
        if (class_exists($exportClass)) {
            $new_export = new $exportClass($bid);
            if(isset($app->cache_dbmfexports)) 
                $app->cache_dbmfexports[$bid] = $new_export;
        } else 
            $app->ERR("MySBDBMFExportHelper::create($name,$type): class '$exportClass' not found");
        return $new_export;
    }

    public function delete($id) {
        global $app;
        MySBDB::query('DELETE FROM '.MySB_DBPREFIX."dbmfexports ".
            "WHERE id='".$id."'",
            "MySBDBMFExportHelper::delete($id)",
            true, "dbmf3");
        if(isset($app->cache_dbmfexports)) 
            unset( $app->cache_dbmfexports[$id]);
    }

    public function load() {
        global $app;
        if(isset($app->cache_dbmfexports)) 
            return $app->cache_dbmfexports;
        $app->cache_dbmfexports = array();
        $req_dbmfexports = MySBDB::query("SELECT * FROM ".MySB_DBPREFIX."dbmfexports ".
                "ORDER BY id",
                "MySBDBMFExportHelper::load()",
                true, 'dbmf3' );
        while($data_export = MySBDB::fetch_array($req_dbmfexports)) {
            $exportClass = 'MySBDBMFExport'.$data_export['type'];
            if (class_exists($exportClass)) 
                $app->cache_dbmfexports[$data_export['id']] = new $exportClass(-1, $data_export);
            else 
            $app->LOG("MySBDBMFExportHelper::load(): class '$exportClass' not found");
        }
        return $app->cache_dbmfexports;
    }

    public function getByID($id) {
        global $app;
        $exports = MySBDBMFExportHelper::load();
        return $exports[$id];
    }

    public function getByName($name) {
        global $app;
        $exports = MySBDBMFExportHelper::load();
        foreach($exports as $export) {
            if($export->name==$name) return $export;
        }
    }

}

/**
 * DBMFExport plugin class
 * value0       Export symbolic name
 * value1       Complete name
 * value2       Include file
 */
class MySBPluginDBMFExport extends MySBPlugin {

    /**
     * Plugin constructor.
     * @param   array               Parameters of plugin
     */
    public function __construct($plugin = array()) {
        parent::__construct((array) ($plugin));
    }

    /**
     * Include process after plugin creation
     * @param   
     */
    public function post_create() {
        global $app;
        require (MySB_ROOTPATH.'/modules/'.$this->module.'/'.$this->value2);
    }

    /**
     * Include process
     * @param   
     */
    public function includeFile() {
        global $app;
        require (MySB_ROOTPATH.'/modules/'.$this->module.'/'.$this->value2);
    }

}

?>
