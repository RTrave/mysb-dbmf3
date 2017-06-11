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

global $app;

// Process id
if(isset($_GET['pid']))
    $pid = $_GET['pid'];
else 
    $pid = '';

_IncI('autosubs_head','dbmf3');

if( !isset($_POST['autosubs_modifs']) )
    echo '
<form action="index.php?mod=dbmf3&amp;tpl=autosubs2&amp;blanklay=1" 
          method="post"
          class="overlayedA"
          id="FormA">';
echo '
<div class="boxed" style="float: left;">';

$autosubs_id = '';

while($data_wcheck = MySBDB::fetch_array($app->dbmf_req_wcheck)) {

    if($autosubs_id!='') $autosubs_id .= ',';
    $autosubs_id .= $data_wcheck['id'];
    $mails = str_replace(',','<br>',$data_wcheck['mail']);
    echo '
    <div class="title" id="contact'.$data_wcheck['id'].'"><b>'.$mails.'</b></div>
    <div class="row">
    <div class="right">';
    if( !isset($_POST['autosubs_modifs']) )
        echo '<input type="text" name="'.$data_wcheck['id'].'lastname" size="24" maxlength="64" value="'.$data_wcheck['lastname'].'">';
    else 
        echo $data_wcheck['lastname'];
    echo '</div>
    <b>'._G('DBMF_common_lastname').'</b>';
    if(MySBConfigHelper::Value('dbmf_ln_infos','dbmf3')!='')
        echo '<br><span class="help">'.MySBConfigHelper::Value('dbmf_ln_infos','dbmf3').'</span>';
    echo '
    </div>
    <div class="row">
    <div class="right">';
    if( !isset($_POST['autosubs_modifs']) )
        echo '<input type="text" name="'.$data_wcheck['id'].'firstname" size="24" maxlength="64" value="'.$data_wcheck['firstname'].'">';
    else 
        echo $data_wcheck['firstname'];
    echo '</div>
    <b>'._G('DBMF_common_firstname').'</b>';
    if(MySBConfigHelper::Value('dbmf_fn_infos','dbmf3')!='')
        echo '<br><span class="help">'.MySBConfigHelper::Value('dbmf_fn_infos','dbmf3').'</span>';
    echo '
    </div>';
    
    $blockrefs = MySBDBMFBlockRefHelper::load();
    foreach( $blockrefs as $blockref ) {
        if($blockref->autosubs==1) {
            if($blockref->getType()!='text') {
                echo '
    <div class="row">';
                echo '
        <div class="right">';
                if( !isset($_POST['autosubs_modifs']) )
                    echo $blockref->htmlForm($data_wcheck['id'].'blockref',$data_wcheck[$blockref->keyname],'('.$data_wcheck['lastname'].' '.$data_wcheck['firstname'].')',false);
                else
                    echo $blockref->htmlFormNonEditable($data_wcheck['id'].'blockref',$data_wcheck[$blockref->keyname],'',false,false);
                echo '</div>
        <b>'._G($blockref->lname).':</b>';
        if( $blockref->infos!='' )
                    echo '<br><span class="help">'.$blockref->infos.'</span>';
            } else {
                echo '
<div class="row" style="text-align: right;">
    <div style="float: left;"><b>'._G($blockref->lname).':</b>';
                if( $blockref->infos!='' )
                    echo '<br><span class="help">'.$blockref->infos.'</span>';
                echo '</div>
    <div style="display: inline-block; margin: 0px 0px 0px auto;">';
                if( !isset($_POST['autosubs_modifs']) )
                    echo $blockref->htmlForm($data_wcheck['id'].'blockref',$data_wcheck[$blockref->keyname],'('.$data_wcheck['lastname'].' '.$data_wcheck['firstname'].')');
                else
                    echo $blockref->htmlFormNonEditable($data_wcheck['id'].'blockref',$data_wcheck[$blockref->keyname],'',false,false);
                echo '</div>';
            }
            
        echo '
    </div>';
        }
    }
}
echo '
</div>';

if( !isset($_POST['autosubs_modifs']) )
    echo '
<div>
<div style="text-align: center; float: right;">
        <input type="hidden" name="autosubs_modifs" value="'.$autosubs_id.'">
        <input type="hidden" name="email" value="'.$_POST['email'.$pid].'">
        <input  type="submit" 
                value="'._G('DBMF_autosubs_modifsubmit').'"
                style="font-size: 130%;">
    </div>
</div></form>';

if( isset($_POST['autosubs_modifs']) )
    echo '
<div>
<form action="index.php?mod=dbmf3&amp;tpl=autosubs2&amp;blanklay=1" 
          method="post"
          class="overlayedA"
          id="FormA">
    <div style="text-align: center; float: right;">
        <input type="hidden" name="new_email" value="'.$_POST['email'.$pid].'">
        <input  type="submit" 
                value="'._G('DBMF_autosubs_submitadd').'"
                style="font-size: 130%;">
    </div><br><br><br><br>
    <div style="text-align: center; float: right;">
        <a  href="index.php?mod=dbmf3&amp;tpl=autosubs1&blanklay=1"
            class="button" 
            style="font-size: 130%;">'._G('DBMF_autosubs_restart').'</a>
    </div>
</form>
</div>';

echo '
</form>';

_IncI('autosubs_foot','dbmf3');

?>
