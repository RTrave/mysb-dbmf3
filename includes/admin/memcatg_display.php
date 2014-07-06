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

if(!MySBRoleHelper::checkAccess('dbmf_config')) return;

$groups = MySBGroupHelper::load();

if( isset($_GET['id']) )
    $memcatg = new MySBDBMFMementoCatg( $_GET['id'] );
else 
    $memcatg = $app->tpl_dbmf_currentmemcatg;

if( isset($_POST['memcatg_submit']) and $_POST['memcatg_submit']==1 ) {

    $groups_csv = new MySBCSValues();
    foreach( $groups as $group ) {
        if( isset($_POST['memcatg_mc'.$memcatg->id.'g'.$group->id]) and 
            $_POST['memcatg_mc'.$memcatg->id.'g'.$group->id]!='' ) 
            $groups_csv->add( $group->id );
    }
    $memcatg->update( array(
        'name' => $_POST['memcatg_name'.$memcatg->id],
        'group_ids' => $groups_csv->csstring() ) );

    echo '
<script>
loadItem(\'memcatg'.$memcatg->id.'\',\'index.php?mod=dbmf3&inc=admin/memcatg_display&id='.$memcatg->id.'\');
</script>';
    return;
}

if( isset($_POST['memcatg_del']) and $_POST['memcatg_del']==1 ) {
    MySBDBMFMementoCatgHelper::delete($memcatg->id);
    $app->pushMessage(_G('DBMF_memcatg_deleted'));
    echo '
<script>
hide(\'memcatg'.$memcatg->id.'\');
</script>';
    return;
}

    echo '
<div class="title roundtop">
    <b>'.$memcatg->name.'</b> ('.$memcatg->id.')
    <div style="float: right;">
        <img    src="images/icons/go-down.png" 
                alt="go-down"
                style="cursor: pointer;"
                onClick="show_auto(\'memcatg_edit_'.$memcatg->id.'\');">
    </div>
    <div style="float: right; margin-right: 7px;">
        <form   action="index.php?mod=dbmf3&amp;inc=admin/memcatg_display&amp;id='.$memcatg->id.'" 
                method="post"
                data-overconfirm="'._G('DBMF_memcatg_confirm_delete').'"
                class="hidelayed">
        <input type="hidden" name="memcatg_del" value="1">
        <input  src="images/icons/user-trash.png"
                type="image"
                alt="'._G('DBMF_memcatg_delete').'"
                title="'._G('DBMF_memcatg_delete').'">
        </form>
    </div>
</div>

    <form   action="index.php?mod=dbmf3&amp;inc=admin/memcatg_display&amp;id='.$memcatg->id.'"
            method="post"
            class="hidelayed">
    <div id="memcatg_edit_'.$memcatg->id.'" style="display: none; width: 100%;">
        <div class="row">
            <div class="right"><input type="text" name="memcatg_name'.$memcatg->id.'" value="'.$memcatg->name.'"></div>
            <b>'._G('DBMF_memcatg_name').':</b>
        </div>

        <div class="row">';
    $gids = new MySBCSValues( $memcatg->group_ids );
    foreach( $groups as $group ) {
        if( $group->id==0 ) continue;
        $idcheck = '';
        if( $gids->have($group->id) ) 
            $idcheck = ' checked="checked" ';
        echo '
        <div style="display: inline-block; background: #dddddd; margin: 2px; padding: 2px;">
            <input type="checkbox" id="memcatg_mc'.$memcatg->id.'g'.$group->id.'" name="memcatg_mc'.$memcatg->id.'g'.$group->id.'" '.$idcheck.'> 
            <label for="memcatg_mc'.$memcatg->id.'g'.$group->id.'">'.$group->comments.'</label>
        </div>';
    }
    
    echo '
        </div>
        <div class="row" style="text-align: center;">
            <input type="hidden" name="memcatg_submit" value="1">
            <input type="submit" value="'._G('DBMF_memcatg_submit').'">
        </div>
    </div>
    </form>
<script>
show(\'memcatg'.$memcatg->id.'\');
</script>';


?>
