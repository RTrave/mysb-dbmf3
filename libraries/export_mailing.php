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
 * DBMF Mailing Export class
 * 
 */
class MySBDBMFExportMailing extends MySBDBMFExport {

    public function __construct($id=-1, $data_export = array()) {
        global $app;
        parent::__construct($id,(array) ($data_export));
    }

/*
    public function update($data_export) {
        parent::update( $data_export );
    }
*/

    public function selectionProcess( $selection ) {
        
    }

    public function htmlParamForm() {
        $output = '
<p>'._G('DBMF_exportmail_modulo').' = 
    <input type="text" name="dbmf_exportmail_modulo" value="0" size="6">
</p>';
        return $output;
    }

    public function htmlParamProcess() {
        global $app;
        $app->tpl_dbmfexportmail_modulo = $_POST['dbmf_exportmail_modulo'];
        
    }

    /**
     * Search result output
     * @param   
     */
    public function htmlResultOutput($results) {
        global $app;
        $output = '
<p>
'.MySBDB::num_rows($results).' results<br>
</p>
<p>Mails (by '.$app->tpl_dbmfexportmail_modulo.'):<br><br>
<code style="width: 70%;">
';
        $modulo_index = 0;
        while($data_result = MySBDB::fetch_array($results)) {
            if($modulo_index>=$app->tpl_dbmfexportmail_modulo) {
                $modulo_index = 0;
                $output .= "<br><br>\n";
            }
            $modulo_index++;
            $contact = new MySBDBMFContact(null,$data_result);
            if($contact->mail!='') 
                 $output .= $contact->mail.'; ';
        }
        $output .= '
</code>
</p>';
        return $output;
    }

}

?>
