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
        if(file_exists(MySB_ROOTPATH.'/xlsxwriter.class.php'))
          $this->xlsxwriter = true;
        else
          $this->xlsxwriter = false;
    }

    public function htmlConfigForm() {
        global $app;
        if($this->xlsxwriter)
          $xlsxwriter_file = "yes";
        else
          $xlsxwriter_file = "no";
        $str_res = '
<div class="row">
  <label class="col-sm-6">
    is <i>/xlsxwriter.class.php</i> present?<br>
    <span class="help">from <a href="https://github.com/mk-j/PHP_XLSXWriter" target="_new">PHP_XLSXWriter</a></span>
  </label>
  <p class="col-sm-6">
        '.$xlsxwriter_file.'
  </p>
</div>';
        return $str_res;
    }

    public function selectionProcess( $selection ) {

    }

    public function htmlParamForm() {
        $output = '';
        $blocks = MySBDBMFBlockHelper::load();
        $output .= '
<div class="row label">
  <label class="col-sm-4" for="dbmf_exportcsv_filename">
    '._G('DBMF_exportcsv_filename').':
  </label>
  <div class="col-7">
    <input type="text" name="dbmf_exportcsv_filename"
           value="" id="dbmf_exportcsv_filename">
  </div>
  <p class="col-1">
    .csv
  </p>
</div>
<div class="row label">
  <label class="col-md-4" for="dbmf_exportcsv_fileinfos">
    '._G('DBMF_exportcsv_fileinfos').':<br>
  </label>
  <div class="col-md-8">
    <textarea name="dbmf_exportcsv_fileinfos"
              id="dbmf_exportcsv_fileinfos"></textarea>
  </div>
</div>
<div class="row label">
  <label class="col-md-4" for="dbmf_exportcsv_delimiter">
    '._G('DBMF_exportcsv_delimiter').':<br>
  </label>
  <div class="col-md-8">
    <select name="dbmf_exportcsv_delimiter" id="dbmf_exportcsv_delimiter">';
        if($this->xlsxwriter)
          $output .= '
      <option value="xlsx">XLSX format</option>';
        $output .= '
      <option value=",">,</option>
      <option value=";">;</option>
    </select>
  </div>
</div>';
        return $output;
    }

    public function htmlParamProcess() {
        global $app, $_POST;
        $this->csv_filename = $_POST['dbmf_exportcsv_filename'].'.csv';
        $this->csv_fileinfos = $_POST['dbmf_exportcsv_fileinfos'];
        $this->csv_delimiter = $_POST['dbmf_exportcsv_delimiter'];
    }

    public function requestOrderBy() {
        if( isset($_POST["dbmf_exportcsv_orderby$this->id"]) )
            return $_POST["dbmf_exportcsv_orderby$this->id"];
        return '';
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
    public function htmlResultOutput() {
        global $app;

        $sql_all =  'SELECT * from '.MySB_DBPREFIX.'dbmfcontacts WHERE '.$_SESSION['dbmf_query_where'].
            ' ORDER by id';
        $results = MySBDB::query( $sql_all,
            "MySBDBMFExportCSV::htmlResultOutput()",
            false, 'dbmf3');

        if($this->csv_delimiter=='xlsx') {

            $path_file = MySB_ROOTPATH.'/modules/dbmf3/files/sendtable.xlsx';
            $this->csv_filename = 'sendtable.xlsx';
            include_once(MySB_ROOTPATH."/xlsxwriter.class.php");

            $blockrefs = MySBDBMFBlockRefHelper::load();
            $header = array(
              _G("DBMF_common_lastname")=>'string',
              _G("DBMF_common_firstname")=>'string',
              _G("DBMF_common_mail")=>'string',
            );
            foreach($blockrefs as $blockref) {
              if( $blockref->isActive() )
                if( $blockref->getType()=='boolean' or $blockref->getType()=='int' )
                  $header[_G($blockref->lname)] = 'integer';
                else
                  $header[_G($blockref->lname)] = 'string';
            }

            $writer = new XLSXWriter();
            $writer->writeSheetHeader('Sheet1', $header );

            $count = 0;
            while($contact_data=MySBDB::fetch_array($results)) {
                $contact = new MySBDBMFContact(null,$contact_data);
                $tablin = array();
                $tablin[] = $contact->lastname;
                $tablin[] = $contact->firstname;
                $tablin[] = $contact->mail;
                foreach($blockrefs as $blockref) {
                    if( $blockref->isActive() ) {
                        if( $blockref->getType()=='tel' )
                            $tablin[] = sprintf( " %s", $contact_data[$blockref->keyname]);
                        else
                            $tablin[] = $this->db2csv(_G($contact_data[$blockref->keyname]));
                    }
                }
                $writer->writeSheetRow('Sheet1', $tablin );
                $count++;
            }
            $writer->writeToFile($path_file);

        } else {

          $csv_char = $this->csv_delimiter;
          $path_file = MySB_ROOTPATH.'/modules/dbmf3/files/sendtable.csv';

          $ftable = fopen($path_file, 'w');
          fputs($ftable, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

          $titles = '"'._G("DBMF_common_lastname").'"'.$csv_char.
                    '"'._G("DBMF_common_firstname").'"'.$csv_char.
                    '"'._G("DBMF_common_mail").'"';
          $blockrefs = MySBDBMFBlockRefHelper::load();
          foreach($blockrefs as $blockref) {
              if( $blockref->isActive() )
                  if( $blockref->getType()=='boolean' or $blockref->getType()=='int' )
                      $titles .= $csv_char._G($blockref->lname);
                      //$titles .= $csv_char.MySBUtil::str2abbrv(_G($blockref->lname));
                  else
                      $titles .= $csv_char._G($blockref->lname);
          }
          fwrite($ftable,$titles."\n");
          $count = 0;
          while($contact_data=MySBDB::fetch_array($results)) {
              $contact = new MySBDBMFContact(null,$contact_data);
              $tablin = array();
              $tablin[] = $contact->lastname;
              $tablin[] = $contact->firstname;
              $tablin[] = $contact->mail;
              foreach($blockrefs as $blockref) {
                  if( $blockref->isActive() ) {
                      if( $blockref->getType()=='tel' )
                          $tablin[] = sprintf( " %s", $contact_data[$blockref->keyname]);
                      else
                          $tablin[] = $this->db2csv(_G($contact_data[$blockref->keyname]));
                  }
              }
              fputcsv($ftable,$tablin,$csv_char,'"');
              $count++;
          }
          fclose($ftable);

        }

        echo '
<div class="searchresults">
  <p>CSV output: '.$count.' results<br>
  send by mail to: '.$app->auth_user->mail.'</p>
</div>
';
        $stmail = new MySBMail('sendtable','dbmf3');
        $stmail->addTO($app->auth_user->mail,$app->auth_user->firstname.' '.$app->auth_user->lastname);
        if($this->csv_filename=='.csv') $csv_filename = 'sendtable.csv';
        else $csv_filename = $this->csv_filename;
        $stmail->addAttachment($path_file,$csv_filename);
        $stmail->data['geckos'] = $app->auth_user->firstname.' '.$app->auth_user->lastname;
        $stmail->data['infos'] = $this->csv_fileinfos;
        $stmail->send();
        unlink($path_file);


    }

}

?>