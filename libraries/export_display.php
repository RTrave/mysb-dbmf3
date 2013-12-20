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
class MySBDBMFExportDisplay extends MySBDBMFExport {

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
        global $app;
        $showfields_colsnb = MySBConfigHelper::Value('dbmf_showfields_colsnb', 'dbmf3');
        $showcols = new MySBCSValues($app->auth_user->dbmf_showcols);
        $output = '';
        $blocks = MySBDBMFBlockHelper::load();
        $output .= '
<div class="table_support">
'._G('DBMF_display_orderby').':
<select name="dbmf_exportdisplay_orderby'.$this->id.'">
    <option value="lastname">'._G('DBMF_common_lastname').'</option>
    <option value="date_modif">'._G('DBMF_date_modif').'</option>';
        $blockref_orderby = MySBDBMFBlockRefHelper::load();
        foreach($blockref_orderby as $oblockref) {
            if($oblockref->orderby=='1') $output .= '
    <option value="'.$oblockref->keyname.'">'._G($oblockref->lname).'</option>';
        }
        $output .= '
</select><br>

'._G('DBMF_display_showfield').':<br>
<table style="border: 0px; font-size: 80%; margin-left: 0px;"><tbody>
<tr>';

        $col_nb = 0;
        foreach($blocks as $block) {
            if($block->isViewable()) {
                $col_nb++;
                $output .= '
<td style="vertical-align: top;">
<table style="width: 100%; font-size: 90%;"><tbody>
<tr class="title" >
    <td colspan="2">';
                $output .= _G($block->lname).'
    </td>
</tr>';
                foreach($block->blockrefs as $blockref) {
                    if($blockref->isActive()) {
                        $refname = $blockref->keyname;
                        $output .= '
<tr>
    <td style="vertical-align: top; text-align: right;">'._G($blockref->lname).':</td>
    <td>';
                        if($showcols->have($blockref->id)) $colsshow_check = 'checked';
                        else $colsshow_check = '';
                        $output .= '<input type="checkbox" name="display_'.$blockref->id.'" '.$colsshow_check.'>';
                        $output .= '
    </td>
</tr>';
                    }
                }
            $output .= '
</tbody></table>
</td>';
            }
            if($col_nb==$showfields_colsnb) {
                $output .= '
</tr><tr>';
                $col_nb = 0;
            }
        }
        if($col_nb!=0) {
        while($col_nb<($showfields_colsnb)) {
            $output .= '<td></td>';
            $col_nb++;
        }
        }
        $output .= '
</tr>
</tbody></table>
</div>';
        return $output;
    }

    public function htmlParamProcess() {
        global $app;
        //$app->tpl_display_columns = array();
        $showcols = new MySBCSValues();
        $blocks = MySBDBMFBlockHelper::load();
        foreach($blocks as $block) {
            if($block->isViewable()) {
                foreach($block->blockrefs as $blockref) {
                    if($blockref->isActive() and isset($_POST['display_'.$blockref->id]) and $_POST['display_'.$blockref->id]=='on') {
                        //$app->tpl_display_columns[] = $blockref;
                        $showcols->add($blockref->id);
                    }
                }
            }
        }
        $app->auth_user->update( array( 'dbmf_showcols' => $showcols->csstring() ) );
    }

    public function requestOrderBy() {
        return $_POST["dbmf_exportdisplay_orderby$this->id"];
    }


    /**
     * Search result output
     * @param   
     */
    public function htmlResultOutput($results) {
        global $app;
        echo '
'.MySBEditor::init("simple").'
<p>
'.MySBDB::num_rows($results).' results<br>
</p>
';
        $app->tpl_dbmf_searchresult = $results;
        _T('templates/contacts_display.php','dbmf3');

    }

}

?>
