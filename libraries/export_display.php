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
      $showcols = new MySBCSValues($app->auth_user->dbmf_showcols);
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
            if($blockref->isActive()) {
              if($showcols->have($blockref->id)) $colsshow_check = 'checked';
              else $colsshow_check = '';
              $output .= '
  <label for="display_'.$blockref->id.'">
    <p><i>'._G($blockref->lname).'</i></p>
    <input type="checkbox" name="display_'.$blockref->id.'"
           '.$colsshow_check.' id="display_'.$blockref->id.'">
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
        //return $_POST["dbmf_exportdisplay_orderby$this->id"];
        return '';
    }


    /**
     * Search result output
     * @param
     */
    public function htmlResultOutput() {
        global $app;
        $editor = new MySBEditor();
        echo '
'.$editor->init("simple").'
<div id="contacts_results" class="slide slide-toggled">';
        include( _pathI('contacts_sort_ctrl','dbmf3') );
        echo '
</div>';

    }

}

?>
