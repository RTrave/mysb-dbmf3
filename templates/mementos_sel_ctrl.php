<?php
/***************************************************************************
 *
 *   phpMySandBox/DBMF3 module - TRoman<abadcafe@free.fr> - 2026
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License', or
 *   ('at your option) any later version.
 *
 ***************************************************************************/

// No direct access.
defined('_MySBEXEC') or die;

global $app;
global $mementos_sel;
global $memento_curnb;

if (!MySBRoleHelper::checkAccess('dbmf_editor'))
    return;

$mementos_all = MySBDBMFMementoHelper::load();
$mementos_sel = [];
$mementos_nb = 0;
if (isset($_POST['memsel_ope'])) {
    foreach ($mementos_all as $memento) {
        if (
            isset($_POST["mem_select" . $memento->id]) &&
            $_POST["mem_select" . $memento->id] != ""
        ) {
            $mementos_nb++;
            $mementos_sel[] = $memento;
        }
    }
    if ($mementos_nb == 0) {
        $app->displayStopAlert("No selection", 0, false);
    }
    if ($_POST["memsel_action"] == "")
        $app->displayStopAlert("No select action", 0, false);
}
if (isset($_POST['mementos_sel_submit'])) {
    $memento_curnb = 1;
    $comment_add = '';
    if ($_POST["memento_comments"] != "") {
        // echo 'COMMENT<br>';
        $comment_add = $_POST["memento_comments"];
    }
    // if ($_POST["mementos_sel_action_type"] == "process") {
    //     echo 'PROCESS<br>';
    // } elseif ($_POST["mementos_sel_action_type"] == "unprocess") {
    //     echo 'UNPROCESS<br>';
    // }

    while (
        isset($_POST["mementos_sel_mem" . $memento_curnb]) &&
        $_POST["mementos_sel_mem" . $memento_curnb] != ''
    ) {
        $memento_cur = new MySBDBMFMemento($_POST["mementos_sel_mem" . $memento_curnb]);
        $mementos_sel[] = $memento_cur;
        // echo 'Mem' . $memento_curnb . ': ' . $memento_cur->id;
        if ($comment_add != '') {
            $comment_new = $memento_cur->comments . $_POST['memento_comments'];
            if($_POST["com_place"]=="top")
                $comment_new =  $_POST['memento_comments'].$memento_cur->comments;
            $memento_cur->update(array(
                'comments' => $comment_new,
            ));
        }
        if ($_POST["memento_type"] != "memtype2") {

            if ($_POST['memento_type'] == 'memtype0')
                $memtype = 0;
            elseif ($_POST['memento_type'] == 'memtype1')
                $memtype = 1;
            // $memento->setCategory($_POST['memento_category']);
            $memento_date = MySBDateTimeHelper::html_formLoad('memento_date_');
            if ($memtype == MYSB_DBMF_MEMENTO_TYPE_PUNCTUAL) {
                $memento_cur->update(array(
                    'type' => $memtype,
                    'date_memento' => $memento_date->date_string,
                    'monthofyear_memento' => MYSB_DBMF_MEMENTO_TYPE_PUNCTUAL
                ));
            } elseif ($memtype == MYSB_DBMF_MEMENTO_TYPE_MONTHOFYEAR) {
                $memento_cur->update(array(
                    'type' => $memtype,
                    'monthofyear_memento' => $_POST['memento_moy'],
                    'date_memento' => '0000-00-00 00:00:00'
                ));
            }
        }
        if ($_POST["mementos_sel_action_type"] == "process") {
            $memento_cur->process();
        } elseif ($_POST["mementos_sel_action_type"] == "unprocess") {
            $memento_cur->unprocess();
        } elseif ($_POST["mementos_sel_action_type"] == "delete") {
            // MySBDBMFMementoHelper::delete($memento_cur->id);
        }
        $memento_curnb++;
    }
}

include(_pathT('mementos_sel', 'dbmf3'));

?>