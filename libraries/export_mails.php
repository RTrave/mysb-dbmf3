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
<div class="row label">
  <label class="col-sm-8" for="dbmf_exportmailscsv_modulo">
    '._G('DBMF_exportmail_modulo').'
  </label>
  <div class="col-sm-4">
    <input type="text" name="dbmf_exportmailscsv_modulo"
           value="50" id="dbmf_exportmailscsv_modulo">
  </div>
</div>';
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
    public function htmlResultOutput() {
        global $app;
        $sql_all =  'SELECT mail from '.MySB_DBPREFIX.'dbmfcontacts WHERE '.$_SESSION['dbmf_query_where'].
            ' ORDER by id';
        $results = MySBDB::query( $sql_all,
            "MySBDBMFExportCSV::htmlResultOutput()",
            false, 'dbmf3');

        $output = '
<div class="content list searchresults">
  <div class="row bg-primary">
    <div class="col-4">
      <p>'.MySBDB::num_rows($results).' results</p>
    </div>
    <div class="col-8">
      <p>Mails (by '.$app->tpl_dbmfexportmailscsv_modulo.')</p>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
    <code style="width: 70%;">
';
        $modulo_index = 0;
        $count = 0;
        while($data_result = MySBDB::fetch_array($results)) {
            if($modulo_index>=$app->tpl_dbmfexportmailscsv_modulo) {
                $modulo_index = 0;
                $output .= '
    </code>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
    <code style="width: 70%;">';
            }
            $contact = new MySBDBMFContact(null,$data_result);
            if($contact->mail!='') {
                $modulo_index++;
                $mails_dp = str_replace(',','; ',$contact->mail);
                $output .= $mails_dp.'; ';
                $count++;
            }
        }
        $output .= '
    </code>
    </div>
  </div>
  <div class="row bg-primary">
    <div class="col-12">
      <p>'.$count.' valids</p>
    </div>
  </div>
</div>';
        return $output;
    }

}

?>
