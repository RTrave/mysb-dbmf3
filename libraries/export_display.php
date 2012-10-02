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
        $output = '';
        $blocks = MySBDBMFBlockHelper::load();
        $output .= '
<div class="table_support">
<table style="border: 0px;"><tbody>
<tr>';
        //$blocks = MySBDBMFBlockHelper::load();
        foreach($blocks as $block) {
            //$group_edit = MySBGroupHelper::getByID($block->groupedit_id);
            if($block->isViewable()) {
                $output .= '
<td style="vertical-align: top;">
<table><tbody>
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
    <td style="vertical-align: top; text-align: right;"><b>'._G($blockref->lname).':</b></td>
    <td>';
                        $output .= '<input type="checkbox" name="display_'.$blockref->id.'">';
                        $output .= '
    </td>
</tr>';
                    }
                }
            }
            $output .= '
</tbody></table>
</td>';
        }
        $output .= '
</tr>
</tbody></table>
</div>';
        return $output;
    }

    public function htmlParamProcess() {
        global $app;
        $app->tpl_display_columns = array();
        $blocks = MySBDBMFBlockHelper::load();
        foreach($blocks as $block) {
            if($block->isViewable()) {
                foreach($block->blockrefs as $blockref) {
                    if($blockref->isActive() and $_POST['display_'.$blockref->id]=='on') {
                        $app->tpl_display_columns[] = $blockref;
                    }
                }
            }
        }
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
';
        $app->tpl_dbmf_searchresult = $results;
        _T('templates/contacts_display.php','dbmf3');

    }

}

?>
