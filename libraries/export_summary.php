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
class MySBDBMFExportSummary extends MySBDBMFExport {

    public function __construct($id=-1, $data_export = array()) {
        global $app;
        parent::__construct($id,(array) ($data_export));
    }

    public function selectionProcess( $selection ) {

    }

    public function htmlParamForm() {
        global $app;
        $output = '';
        $blocks = MySBDBMFBlockHelper::load();
        $output .= '
<h3>'._G('DBMF_display_showfield').'</h3>';

        foreach($blocks as $block) {
            if($block->isViewable()) {
            $output .= '
<div class="row" style="font-size: 70%;">
  <p class="col-12">
    <b>'._G($block->lname).'</b>
  </p>
</div>
<div class="row checkbox-list" style="font-size: 70%;">';
            foreach($block->blockrefs as $blockref) {
                if( $blockref->isActive() and
                    ( $blockref->type==MYSB_VALUE_TYPE_INT or
                      $blockref->type==MYSB_VALUE_TYPE_BOOL) )
                {
                    $output .= '
  <label for="summary_'.$blockref->keyname.'">
    <p><i>'._G($blockref->lname).'</i></p>
    <input type="checkbox" name="summary_'.$blockref->keyname.'"
           id="summary_'.$blockref->keyname.'">
  </label>';
                }
            }
            $output .= '
</div>';
            }
        }
    $output .= '';
    return $output;
    }

    public function htmlParamProcess() {
        global $app;
        $app->dbmf_summary_blockrefs = array();
        $blocks = MySBDBMFBlockHelper::load();
        foreach($blocks as $block) {
            if($block->isViewable()) {
                foreach($block->blockrefs as $blockref) {
                    if($blockref->isActive() and isset($_POST['summary_'.$blockref->keyname]) and $_POST['summary_'.$blockref->keyname]=='on') {
                        $app->dbmf_summary_blockrefs[] = $blockref;
                    }
                }
            }
        }
    }

    public function requestOrderBy() {
        //return $_POST["dbmf_exportdisplay_orderby$this->id"];
        return '';
    }


    /**
     * Search result output
     * @param
     */
    public function htmlResultOutput() {
        global $app,$_SESSION;
        $summary_query_where = '';
        $summary_query_fields = '';
        foreach( $app->dbmf_summary_blockrefs as $blockref ) {
            if( $summary_query_where!='' ) $summary_query_where .= ' or ';
            $summary_query_where .= $blockref->keyname.'!=\'\'';
            $summary_query_fields .= ','.$blockref->keyname;
        }
        if( $_SESSION['dbmf_query_where']!='' )
            $summary_query_where = $_SESSION['dbmf_query_where'].' and ('.$summary_query_where.')';
        else $summary_query_where = '('.$summary_query_where.')';
        $sql_summary =  'SELECT id'.$summary_query_fields.' FROM '.MySB_DBPREFIX.'dbmfcontacts '.
                        ' WHERE ('.$summary_query_where.')';
        $search_summary = MySBDB::query( $sql_summary,
            "export_summary.php",
            false, 'dbmf3');

        echo '
<div class="content list" id="summary_results">
    <div class="row" style="min-height: 3rem;">
      <div class="col-6 bg-primary">
        <p>'._G("DBMF_summary_lname").'</p>
      </div>
      <div class="col-3 t-right bg-primary">
        <p>'._G("DBMF_summary_sum").'</p>
      </div>
      <div class="col-3 t-right bg-primary">
        <p>'._G("DBMF_summary_count").'</p>
      </div>
    </div>
  ';
    $complete_sum = 0;
        foreach( $app->dbmf_summary_blockrefs as $blockref ) {
            echo '
    <div class="row" style="min-height: 3rem;">
      <div class="col-6 bg-primary-light">
        <p>'._G($blockref->lname).'</p>
      </div>
      <div class="col-3 t-right bg-primary-light">';
            $sum = 0;
            $nb = 0;
            while( $data_summary = MySBDB::fetch_array($search_summary) ) {
                if( $data_summary[$blockref->keyname]!='' and $data_summary[$blockref->keyname]!=0 ) {
                    $sum += $data_summary[$blockref->keyname];
                    $nb ++;
                }
            }
            MySBDB::data_seek($search_summary,0);
            $complete_sum += $sum;
            echo '
        <p>'.$sum.'</p>
      </div>
      <div class="col-3 t-right bg-primary-light">
        <p>'.$nb.'</p>
      </div>
    </div>';
        }
        echo '
    <div class="row">
      <div class="col-6 bg-primary">
        <p>'._G('DBMF_export_summary').'</p>
      </div>
      <div class="col-3 bg-primary t-right">
        <p>'.$complete_sum.'</p>
      </div>
      <div class="col-3 bg-primary t-right">
        <p>'.MySBDB::num_rows($search_summary).'</p>
      </div>
    </div>
</div>';

    }

}

?>
