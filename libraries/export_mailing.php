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
        $output = '';
        return $output;
    }

    public function htmlParamProcess() {
        global $app;
    }

    /**
     * Search result output
     * @param   
     */
    public function htmlResultOutput($results) {
        global $app;
        echo '
<p>
'.MySBDB::num_rows($results).' results<br>
</p>
<p>Mails:<br>
';
        //$search_result = $app->tpl_dbmf_searchresult;
        while($data_result = MySBDB::fetch_array($results)) {
            $contact = new MySBDBMFContact(null,$data_result);
            if($contact->mail!='') 
                echo $contact->mail.'; ';
        }
        echo '</p>';
    }

}

?>
