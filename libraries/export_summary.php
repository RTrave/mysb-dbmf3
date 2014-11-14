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
<div class="list_support" style="vertical-align: top;">
'._G('DBMF_display_showfield').':<br>';

        foreach($blocks as $block) {
            if($block->isViewable()) {
                $output .= '
    <div class="boxed" style="font-size: 70%; width: 100%; margin: 2px 2px 0px;">
        <div class="title" style="padding: 2px 2px 0px; min-height: 22px; width: 100px; "><b>'._G($block->lname).'</b></div>
        <div class="row" style="padding: 2px 2px 0px; min-height: 18px; widthA: 70%;">';
                foreach($block->blockrefs as $blockref) {
                    if( $blockref->isActive() and 
                        ($blockref->type==MYSB_VALUE_TYPE_INT or $blockref->type==MYSB_VALUE_TYPE_BOOL) ) {
                        $output .= '
        <div class="rowA" style="display:inline-block; padding: 2px 2px 0px; min-height: 22px;">';
/*
                        if($showcols->have($blockref->id)) $colsshow_check = 'checked';
                        else $colsshow_check = '';
*/
                        $output .= '<input type="checkbox" name="summary_'.$blockref->keyname.'">
                        '._G($blockref->lname).'
        </div>';

                    }
                }
                $output .= '
        </div>
    </div>';
            }
        }
        $output .= '
</div>
<br>';
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
<div id="summary_results">';
        echo '
<p>Resultats:</p><br>
<div class="boxed" style="width: 450px; margin: 10px auto 3px;">
';
        $complete_sum = 0;
        foreach( $app->dbmf_summary_blockrefs as $blockref ) {
            echo '
    <div class="row">
        <div class="right">';
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
            echo $sum.' / '.$nb.' '._G('DBMF_common_contact');
            echo '</div>
        '._G($blockref->lname).'
    </div>';
        }
        echo '
    <div class="row">
        <div class="right"><b>'.$complete_sum.' / '.MySBDB::num_rows($search_summary).' '._G('DBMF_common_contact').'</b></div>
        '._G('DBMF_export_summary').'
    </div>
</div>
</div>';

    }

}

?>
