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

<div class="content list">
  <div class="row">
    <a class="col-auto btn-primary-light" href="javascript:void(0)"
       onClick="slide_toggle(\'memcatg_edit_'.$memcatg->id.'\');">
      <p><img src="images/icons/go-down.png" alt="go-down"
              style="position: absolute; right: 0;">
        <b>'.$memcatg->name.'</b><br>
        <span class="help">id: '.$memcatg->id.'</span>
      </p>
    </a>';
    if( $memcatg->id!=1 )
        echo '
    <form action="index.php?mod=dbmf3&amp;inc=admin/memcatg_display&amp;id='.$memcatg->id.'"
          method="post"
          data-overconfirm="'._G('DBMF_memcatg_confirm_delete').'"
          title="'._G('DBMF_memcatg_delete').'"
          class="hidelayed col-1 btn btn-danger">
      <input type="hidden" name="memcatg_del" value="1">
      <input  src="images/icons/user-trash.png"
              type="image" alt="">
    </form>
<!--
  <a class="hidelayed col-1 t-center btn-danger-light"
     href="index.php?mod=dbmf3&amp;inc=admin/memcatg_display&amp;id='.$memcatg->id.'"
     data-overconfirm="'._G('DBMF_memcatg_confirm_delete').'"
     title="'._G('DBMF_memcatg_delete').'">
    <img src="images/icons/user-trash.png" alt="">
  </a>
-->';
    echo '
  </div>
</div>
<!--
<div class="title roundtop">
    <b>'.$memcatg->name.'</b> ('.$memcatg->id.')
    <div style="float: right;">
        <img    src="images/icons/go-down.png"
                alt="go-down"
                style="cursor: pointer;"
                onClick="toggle_slide(\'memcatg_edit_'.$memcatg->id.'\');">
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
-->

<form action="index.php?mod=dbmf3&amp;inc=admin/memcatg_display&amp;id='.$memcatg->id.'"
      method="post" class="hidelayed">
<div id="memcatg_edit_'.$memcatg->id.'" class="slide">

  <div class="row label">
    <label class="col-sm-4" for="memcatg_name'.$memcatg->id.'">
      <b>'._G('DBMF_memcatg_name').'</b>
    </label>
    <div class="col-sm-8">
      <input type="text" name="memcatg_name'.$memcatg->id.'"
             id="memcatg_name'.$memcatg->id.'"
             value="'.$memcatg->name.'">
    </div>
  </div>

  <div class="row checkbox-list">';
    $gids = new MySBCSValues( $memcatg->group_ids );
    foreach( $groups as $group ) {
        if( $group->id==0 ) continue;
        $idcheck = '';
        if( $gids->have($group->id) )
            $idcheck = ' checked="checked" ';
        echo '
    <label for="memcatg_mc'.$memcatg->id.'g'.$group->id.'">
      <input type="checkbox" name="memcatg_mc'.$memcatg->id.'g'.$group->id.'"
                   '.$idcheck.' id="memcatg_mc'.$memcatg->id.'g'.$group->id.'">
      <i>'.$group->comments.'</i>
    </label>
<!--
        <div style="display: inline-block; background: #dddddd; margin: 2px; padding: 2px;">
            <input type="checkbox" id="memcatg_mc'.$memcatg->id.'g'.$group->id.'" name="memcatg_mc'.$memcatg->id.'g'.$group->id.'" '.$idcheck.'>
            <label for="memcatg_mc'.$memcatg->id.'g'.$group->id.'">'.$group->comments.'</label>
        </div>
-->';
    }

    echo '
  </div>
  <div class="row">
    <div class="col-sm-6"></div>
    <div class="col-sm-6">
            <input type="hidden" name="memcatg_submit" value="1">
            <input type="submit"  class="btn-primary"
                   value="'._G('DBMF_memcatg_submit').'">
    </div>
  </div>

</div>
</form>

<script>
show(\'memcatg'.$memcatg->id.'\');
</script>';


?>
