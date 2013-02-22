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
 * DBMF CSV Export class
 * 
 */
class MySBDBMFExportCSV extends MySBDBMFExport {

    public function __construct($id=-1, $data_export = array()) {
        global $app;
        parent::__construct($id,(array) ($data_export));
    }


    public function selectionProcess( $selection ) {
        
    }

    public function htmlParamForm() {
        $showfields_colsnb = MySBConfigHelper::Value('dbmf_showfields_colsnb', 'dbmf3');
        $output = '';
        $blocks = MySBDBMFBlockHelper::load();
        $output .= '
<div class="table_support">
<p>
    '._G('DBMF_exportcsv_filename').':
    <input type="text" name="dbmf_exportcsv_filename" value="" size="24">.csv<br>
    '._G('DBMF_exportcsv_fileinfos').':<br>
    <textarea name="dbmf_exportcsv_fileinfos" cols="40" rows="4"></textarea>
</p>
</div>';
        return $output;
    }

    public function htmlParamProcess() {
        global $app;
        $this->csv_filename = $_POST['dbmf_exportcsv_filename'].'.csv';
        $this->csv_fileinfos = $_POST['dbmf_exportcsv_fileinfos'];
    }

    public function requestOrderBy() {
        return $_POST["dbmf_exportcsv_orderby$this->id"];
    }

    private function keyname2red($strdb) {
        if(strlen($strdb)<7) return $strdb;
        $newstr = '';
        $words = explode(' ',$strdb);
        foreach($words as $word) {
            if(strlen($word)>3) {
                $newstr .= $word[0];
                $newstr .= $word[1];
                $newstr .= $word[2];
                $newstr .= '. ';
            } else $newstr .= $word.' ';
        }
        return $newstr;
    }

    private function db2csv($text) {
        $tmptext = str_replace("\r\n","\n",$text);
        $text_lines = explode("\n", $tmptext);
        $nboflines = count($text_lines);
        $newtext = '';
        for($i=0; $i<$nboflines; $i++) {
            $newline = str_replace("\n",'',$text_lines[$i]);
            $testline = str_replace(" ",'',$newline);
            if($testline!=''and $i>0)
                $newtext .= "\n";
            if($testline!='')
                $newtext .= $newline;
        }
        return $newtext;
    }


    /**
     * Search result output
     * @param   
     */
    public function htmlResultOutput($results) {
        global $app;
        echo '
<p>
CSV output: '.MySBDB::num_rows($results).' results<br>
</p>
';

        $csv_char = ';';
        $path_file = MySB_ROOTPATH.'/modules/dbmf3/files/sendtable.csv';
        $ftable = fopen($path_file, 'w');

        $titles = '"Name"'.$csv_char.'"Firstname"';
        $blockrefs = MySBDBMFBlockRefHelper::load();
        foreach($blockrefs as $blockref) {
            $titles .= $csv_char.$this->keyname2red(_G($blockref->lname));
        }
        fwrite($ftable,$titles."\n");
        while($contact_data=MySBDB::fetch_array($results)) {
            $contact = new MySBDBMFContact(null,$contact_data);
            $tablin = array();
            $tablin[] = $contact->lastname;
            $tablin[] = $contact->firstname;
            foreach($blockrefs as $blockref) {
                $tablin[] = $this->db2csv(_G($contact_data[$blockref->keyname]));
            }
            fputcsv($ftable,$tablin,';','"');
        }
        fclose($ftable);
        $stmail = new MySBMail('mail_sendtable','dbmf3');
        $stmail->addTO($app->auth_user->mail,$app->auth_user->firstname.' '.$app->auth_user->lastname);
        if($this->csv_filename=='.csv') $csv_filename = 'sendtable.csv';
        else $csv_filename = $this->csv_filename;
        $stmail->addAttachment($path_file,$csv_filename);
        $stmail->data['infos'] = $this->csv_fileinfos;
        $stmail->send();
        unlink($path_file);


    }

}

?>
