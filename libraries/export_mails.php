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
 * DBMF CSV'Mails Export class
 * 
 */
class MySBDBMFExportMailsCSV extends MySBDBMFExport {

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
    <input type="text" name="dbmf_exportmailscsv_modulo" value="50" size="6">
</p>';
        return $output;
    }

    public function htmlParamProcess() {
        global $app;
        $app->tpl_dbmfexportmailscsv_modulo = $_POST['dbmf_exportmailscsv_modulo'];
        
    }

    /**
     * Search result output
     * @param   
     */
    public function htmlResultOutput($results) {
        global $app;
        $sql_all =  'SELECT mail from '.MySB_DBPREFIX.'dbmfcontacts WHERE '.$_SESSION['dbmf_query_where'].
            ' ORDER by id';
        $results = MySBDB::query( $sql_all,
            "MySBDBMFExportCSV::htmlResultOutput()",
            false, 'dbmf3');

        $output = '
<p>
'.MySBDB::num_rows($results).' results<br>
</p>
<p>Mails (by '.$app->tpl_dbmfexportmailscsv_modulo.'):<br><br>
<code style="width: 70%;">
';
        $modulo_index = 0;
        $count = 0;
        while($data_result = MySBDB::fetch_array($results)) {
            if($modulo_index>=$app->tpl_dbmfexportmailscsv_modulo) {
                $modulo_index = 0;
                $output .= "<br><br>\n";
            }
            $contact = new MySBDBMFContact(null,$data_result);
            if($contact->mail!='') {
                $modulo_index++;
                $output .= $contact->mail.'; ';
                $count++;
            }
        }
        $output .= '
</code>
</p>';
        $output .= '
<p>
'.$count.' valids<br>
</p>';
        return $output;
    }

}

?>
