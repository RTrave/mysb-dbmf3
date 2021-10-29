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
class MySBDBMFExportUpdate extends MySBDBMFExport {

    public function __construct($id=-1, $data_export = array()) {
        global $app;
        parent::__construct($id,(array) ($data_export));
    }


    public function selectionProcess( $selection ) {

    }

    public function htmlParamForm() {
        $showfields_colsnb = 3;
        $output = '';
        $blocks = MySBDBMFBlockHelper::load();
        $output .= '
<h3>'._G('DBMF_exportupdate_updfield').'</h3>
<div class="row">
  <div class="col-12">
<table style="border: 0px; font-size: 80%"><tbody>
<tr>';

        $col_nb = 1;
        foreach($blocks as $block) {
            if($block->isViewable()) {
                $output .= '
<td style="vertical-align: top;">
<table style="width: 100%; font-size: 80%; margin-left: 0px;"><tbody>
<tr class="title" >
    <td colspan="2">';
                $output .= _G($block->lname).'
    </td>
</tr>';
                foreach($block->blockrefs as $blockref) {
                    if($blockref->isActive()) {
                        $refname = $blockref->keyname;
                        $output .= '
<tr style="background-color: #fff;">
    <td style="vertical-align: top; text-align: left;">
        <input type="checkbox" name="upd_id_'.$blockref->id.'">'._G($blockref->lname).':
    </td>
    <td>';
                        //$output .= '<input type="checkbox" name="display_'.$blockref->id.'">';
                        $output .= $blockref->htmlForm('upd_val_','');
                        $output .= '
    </td>
</tr>';
                    }
                }
            }
            $output .= '
</tbody></table>
</td>';
            if($col_nb==$showfields_colsnb) {
                $output .= '
</tr><tr>';
                $col_nb = 0;
            }
            $col_nb++;
        }
        if($col_nb!=1) {
        while($col_nb!=($showfields_colsnb+1)) {
            $output .= '<td>&nbsp;</td>';
            $col_nb++;
        }
        }
        $output .= '
</tr>
</tbody></table>
  </div>
</div>';
        return $output;
    }

    public function htmlParamProcess() {
        global $app;
        $this->update_sql = '';
        $blocks = MySBDBMFBlockHelper::load();
        foreach($blocks as $block) {
            if($block->isViewable()) {
                foreach($block->blockrefs as $blockref) {
                    if( $blockref->isActive() and
                        isset($_POST['upd_id_'.$blockref->id]) and
                        $_POST['upd_id_'.$blockref->id]=='on') {
                        
                        $getvalue = $blockref->htmlProcessValue('upd_val_');

                        if($blockref->updateOnEmpty() || !empty($getvalue)){
                            if($this->update_sql!='') $this->update_sql .= ', ';
                            $this->update_sql .= $blockref->keyname.'=\''.$getvalue.'\'';
                        }
                    }
                }
            }
        }
    }

    public function requestOrderBy() {
        //return $_POST["dbmf_exportdisplay_orderby$this->id"];
    }


    /**
     * Search result output
     * @param
     */
    public function htmlResultOutput() {
        global $app;
        if($this->update_sql=='') {
            echo '<p>'._G('DBMF_exportupdate_noselection').'</p>';
            return;
        }

        $sql_all =  'SELECT id from '.MySB_DBPREFIX.'dbmfcontacts WHERE '.$_SESSION['dbmf_query_where'];
        $results = MySBDB::query( $sql_all,
            "MySBDBMFExportCSV::htmlResultOutput()",
            false, 'dbmf3');

        $export_upd_sql =   "UPDATE ".MySB_DBPREFIX."dbmfcontacts ".
                            "SET ".$this->update_sql." ".
                            "WHERE ".$_SESSION['dbmf_query_where'];
                            //"WHERE ".$app->dbmf_export_whereclause;
        echo '
<p>
'._G('DBMF_exportupdate_results').': '.MySBDB::num_rows($results).'<br>
SQL query:<br>
<small>'.$export_upd_sql.'</small>
</p>
';
        MySBDB::query(  $export_upd_sql,
                        "MySBDBMFExportUpdate::htmlResultOutput()",
                        true, 'dbmf3' );
    }

}

?>
