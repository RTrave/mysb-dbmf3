<?php
/***************************************************************************
 *
 *   phpMySandBox/DBMF3 module - TRoman<abadcafe@free.fr> - 2012
 *   blockref program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License', or
 *   ('at your option) any later version.
 *
***************************************************************************/

// No direct access.
defined('_MySBEXEC') or die;

global $app;

# global $_GET;
# if ( !isset($_GET['tpl']) or $_GET['tpl']=='' )
#   $_GET['tpl'] = 'admin/structure';

# function isActive($tpl_code) {
#   if( $_GET['tpl']==$tpl_code )
#     return 'no-collapse';
#   else return '';
# }
?>


<?php

if( isset($_GET['blockref_edit']) or
    isset($_POST['blockref_edit_process']) or
    isset($_POST['blockref_add']) ) {

$blockref = $app->tpl_blockref_edit;
echo '
<div class="content">
<h1>'._G($blockref->lname).' <span class="help">('.$blockref->keyname.')</span></h1>

<form action="'.$hrefconfig.'" method="post">

  <div class="row">
    <p class="col-6">
      '._G('DBMF_blockref_type').'
    </p>
    <p class="col-6">
      '.$blockref->getType().'
    </p>
  </div>
  <div class="row label">
    <label class="col-6" for="lname">
      '._G('DBMF_blockref_lname').'
    </label>
    <div class="col-6">
      <input type="text" name="lname" id="lname"
             value="'.$blockref->lname.'">
    </div>
  </div>
  <div class="row label">
    <label class="col-6" for="switchorderby">
      '._G('DBMF_blockref_option_orderby').'
    </label>
    <div class="col-6">
      <select name="switchorderby" id="switchorderby">
        <option value="0">'._G('DBMF_blockref_orderby_no').'</option>
        <option value="1" '.MySBUtil::form_isselected($blockref->orderby,1).'>'._G('DBMF_blockref_orderby_ok').'</option>
      </select>
    </div>
  </div>
  <div class="row label">
    <label class="col-6" for="blockref_alwaysshown">
      '._G('DBMF_blockref_option_alwaysshown').'
    </label>
    <div class="col-6">
      <select name="blockref_alwaysshown" id="blockref_alwaysshown">
        <option value="'.MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_NO.'"
                '.MySBUtil::form_isselected($blockref->alwaysshown,MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_NO).'>'._G('DBMF_blockref_alwaysshown_no').'</option>
        <option value="'.MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_ASTEXT.'"
                '.MySBUtil::form_isselected($blockref->alwaysshown,MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_ASTEXT).'>'._G('DBMF_blockref_alwaysshown_bottom').'</option>
        <option value="'.MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_ASPLUG.'"
                '.MySBUtil::form_isselected($blockref->alwaysshown,MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_ASPLUG).'>'._G('DBMF_blockref_alwaysshown_plugins').'</option>
        <option value="'.MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_ASTEXTONLY.'"
                '.MySBUtil::form_isselected($blockref->alwaysshown,MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_ASTEXTONLY).'>'._G('DBMF_blockref_alwaysshown_txtonly').'</option>
      </select>
    </div>
  </div>
  <div class="row label">
    <label class="col-6" for="infos">
      '._G('DBMF_blockref_infos').'
    </label>
    <div class="col-6">
      <textarea name="infos" id="infos">'.$blockref->infos.'</textarea>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-3"></div>
    <div class="col-sm-6">
      <input type="hidden" name="blockref_edit_process" value="'.$blockref->id.'">
      <input type="submit" class="btn-primary"
             value="'._G('DBMF_blockref_edition_process').'">
    </div>
    <div class="col-sm-3"></div>
  </div>
</form>';

if($blockref->type==MYSB_VALUE_TYPE_VARCHAR64_SELECT) {
    echo '
  <h2 class="border-top">Options</h2>';
    $req_options = MySBDB::query("SELECT * from ".MySB_DBPREFIX."valueoptions ".
        "WHERE value_keyname='".$blockref->grp."-".$blockref->keyname."' ".
        "ORDER BY value0",
        "MySBDBMFBlockRef::htmlOptionFormTR()");
    while($option = MySBDB::fetch_array($req_options)) {
        echo '
  <div class="row label">

    <label class="col-3" for="blockref_mod_option'.$option['value0'].'">
      Option '.$option['value0'].'
    </label>
    <form action="'.$hrefconfig.'&amp;blockref_edit='.$blockref->id.'" method="post">
    <div class="col-5">
      <input type="text" name="blockref_mod_option" id="blockref_mod_option'.$option['value0'].'"
             value="'.$option['value1'].'">
    </div>
    <div class="col-2" title="'._G('DBMF_blockref_mod_option').'">
      <input type="hidden" name="blockref_option_id" value="'.$option['value0'].'">
      <input type="hidden" name="blockref_edit" value="'.$blockref->id.'">
      <input type="submit" class="btn-primary-light"
             value="'._G('DBMF_blockref_mod_option').'">
    </div>
    </form>
    <form action="'.$hrefconfig.'&amp;blockref_edit='.$blockref->id.'"
        method="post"  class="col-2"
        title="'._G('DBMF_blockref_del_option').'">
      <input type="hidden" name="blockref_del_option" value="1">
      <input type="hidden" name="blockref_option_id" value="'.$option['value0'].'">
      <input type="hidden" name="blockref_edit" value="'.$blockref->id.'">
      <input type="submit" class="btn-danger-light"
             value="'._G('DBMF_blockref_del_option').'">
    </form>

  </div>
<!--
  <div class="row">
    <form action="'.$hrefconfig.'" method="post">
    <div class="right">
        <input type="hidden" name="blockref_del_option" value="'.$option['value1'].'">
        <input type="hidden" name="blockref_edit" value="'.$blockref->id.'">
        <input type="hidden" name="blockref_option_id" value="'.$option['value0'].'">
        <input type="submit" value="'._G('DBMF_blockref_del_option').'">
        </div>
    </form>
    Option '.$option['value0'].'
</div>
<div class="row">
    <form action="'.$hrefconfig.'" method="post">
    <div>
        <input type="hidden" name="blockref_option_id" value="'.$option['value0'].'">
        <input type="hidden" name="blockref_edit" value="'.$blockref->id.'">
        <input type="submit" value="'._G('DBMF_blockref_mod_option').'">
    </div>
    <input type="text" name="blockref_mod_option" value="'.$option['value1'].'">
    </form>
</div>
-->';
    }
    echo '
  <div class="row label">
  <form action="'.$hrefconfig.'&amp;blockref_edit='.$blockref->id.'" method="post">
    <label class="col-3" for="blockref_new_option">
      '._G('DBMF_blockref_newoption').'
    </label>
    <div class="col-5">
      <input type="text" name="blockref_new_option"
             id="blockref_new_option"value="">
    </div>
    <div class="col-4">
      <input type="hidden" name="blockref_edit" value="'.$blockref->id.'">
      <input type="submit"  class="btn-primary-light"
             value="'._G('DBMF_blockref_add_option').'">
    </div>
  </form>
  </div>
<!--
  <h3 class="border-top">'._G('DBMF_blockref_newoption').'</h3>
  <div class="row">
    <form action="index.php?mod=dbmf3&amp;tpl=admin/structure" method="post">
    <div class="right">
        <input type="hidden" name="blockref_edit" value="'.$blockref->id.'">
        <input type="submit" value="'._G('DBMF_blockref_add_option').'">
    </div>
    <input type="text" name="blockref_new_option" value="">
    </form>
</div>
-->';
}
echo '
</div>
</div>';
return;
}


echo '
';

$blocks = MySBDBMFBlockHelper::load();
foreach($blocks as $block) {

    $group_edit = MySBGroupHelper::getByID($block->groupedit_id);

    echo '
<div class="content" id="a_block'.$block->id.'">

  <h1>'._G('DBMF_config').': '._G($block->lname).'</h1>

  <div class="row">
<div class="content list"><div class="row">

  <form action="'.$hrefconfig.'#a_block'.$block->id.'"
        method="post"  class="col-1 btn btn-primary"
        title="&darr;">
    <input type="hidden" name="block_orderdown" value="'.$block->id.'">
    <input src="images/icons/go-down.png"
           type="image" alt="">
  </form>
  <form action="'.$hrefconfig.'#a_block'.$block->id.'"
        method="post"  class="col-1 btn btn-primary"
        title="&uarr;">
    <input type="hidden" name="block_orderup" value="'.$block->id.'">
    <input src="images/icons/go-up.png"
           type="image" alt="">
  </form>

  <div class="col-9">
    <p><b>'._G($block->lname).'</b>
    <span class="help">(ID:'.$block->id.')</span></p>
  </div>

  <form action="'.$hrefconfig.'"
        method="post"  class="col-1 btn btn-danger"
        OnClick="return mysb_confirm(\''.MySBUtil::str2strict(sprintf(_G('DBMF_confirm_block_delete'), $block->lname, $group_edit->comments )).'\')"
        title="'._G('DBMF_block_delete').'">
    <input type="hidden" name="block_del" value="'.$block->id.'">
    <input src="images/icons/user-trash.png"
           type="image" alt="">
  </form>

</div></div>
  </div>

  <form action="'.$hrefconfig.'#a_block'.$block->id.'" method="post">
  <div class="row label">
    <label class="col-sm-6" for="mod_lname'.$block->id.'">
      <b>'._G('DBMF_block_lname').'</b>
    </label>
    <div class="col-sm-6">
      <input type="text" name="lname" id="mod_lname'.$block->id.'"
             value="'.$block->lname.'">
    </div>
  </div>
  <div class="row label">
    <label class="col-sm-6" for="group_id'.$block->id.'">
      <b>'._G('DBMF_block_groupedit').'</b>
    </label>
    <div class="col-sm-6">
      <select name="group_id" id="group_id'.$block->id.'">';
    $groups = MySBDBMFGroupHelper::load();
    foreach($groups as $group)
      if($group->dbmf_priority>0)
        echo '
        <option value="'.$group->id.'" '.MySBUtil::form_isselected($group->id,$group_edit->id).'>'.$group->comments.'</option>';
      echo '
      </select>
    </div>
  </div>
  <div class="row border-bottom" id="a_blockrefs'.$block->id.'">
    <div class="col-sm-3"></div>
    <div class="col-sm-6">
      <input type="hidden" name="block_edition" value="'.$block->id.'">
      <input type="submit" class="btn-primary"
             value="'._G('DBMF_block_edition').'">
    </div>
    <div class="col-sm-3"></div>
  </div>
    </form>
    ';

    foreach($block->blockrefs as $blockref) {
        if($blockref->status==MYSB_DBMF_BLOCKREF_STATUS_ACTIVE)
            $class_bref = ' blockref_active';
        else $class_bref = ' blockref_inactive';
        echo '
  <div class="content list">
  <div class="row'.$class_bref.'">

  <form action="'.$hrefconfig.'#a_blockrefs'.$block->id.'"
        method="post"  class="col-1 btn btn-dark"
        title="&darr;">
    <input type="hidden" name="blockref_orderdown" value="'.$blockref->id.'">
    <input src="images/icons/go-down.png"
           type="image" alt="">
  </form>

  <form action="'.$hrefconfig.'#a_blockrefs'.$block->id.'"
        method="post"  class="col-1 btn btn-dark"
        title="&uarr;">
    <input type="hidden" name="blockref_orderup" value="'.$blockref->id.'">
    <input src="images/icons/go-up.png"
           type="image" alt="">
  </form>

  <a href="'.$hrefconfig.'&amp;blockref_edit='.$blockref->id.'"
     class="col-auto btn btn-dark t-left"
     title="'._G('DBMF_blockref_edition').'">
    <b>'._G($blockref->lname).'</b><br>
    <span class="help">'.$blockref->keyname.' ('.$blockref->getType().')</span>
  </a>
  <form action="'.$hrefconfig.'#a_blockrefs'.$block->id.'"
        method="post"  class="col-2 btn"
        title="">
    <input type="hidden" name="blockref_switchactive" value="'.$blockref->id.'">';
        if($blockref->status==MYSB_DBMF_BLOCKREF_STATUS_ACTIVE)
          echo '
    <input type="submit" class="btn-danger-light"
           value="'._G('DBMF_blockref_desactive').'">';
        else
          echo '
    <input type="submit" class="btn-success-light"
           value="'._G('DBMF_blockref_active').'">';
        echo '
  </form>
<!--
    <a href="index.php?mod=dbmf3&amp;tpl=admin/config&amp;page=structure&amp;block_orderup='.$block->id.'#a_block'.$block->id.'"
       class="col-2 t-center btn btn-success-light"
       title="&uarr;">
      Activer?
    </a>
    <a href="'.$hrefconfig.'&amp;blockref_del='.$blockref->id.'#a_block'.$block->id.'"
     class="col-1 t-center btn btn-danger"
     data-overconfirm=""
     title="'._G('DBMF_block_delete').'">
      <img src="images/icons/user-trash.png" alt="">
    </a>
-->
  <form action="'.$hrefconfig.'#a_blockrefs'.$block->id.'"
        method="post"  class="col-1 btn btn-danger-light"
        OnSubmit="return mysb_confirm(\''.MySBUtil::str2strict(sprintf(_G('DBMF_confirm_blockref_delete'), $blockref->lname, $blockref->keyname )).'\')"
        title="'._G('DBMF_blockref_delete').'">
    <input type="hidden" name="block_id" value="'.$block->id.'">
    <input type="hidden" name="blockref_del" value="'.$blockref->id.'">
    <input src="images/icons/user-trash.png"
           type="image" alt="">
  </form>

  </div>
  </div>
<!--
    <div class="row'.$class_bref.'">
        <div style="float: left;">
        <form action="index.php?mod=dbmf3&amp;tpl=admin/structure" method="post">
        <input type="hidden" name="blockref_edit" value="'.$blockref->id.'">
        <input  src="images/icons/text-editor.png"
                    type="image"
                    alt="'._G('DBMF_blockref_edition').'"
                    title="'._G('DBMF_blockref_edition').'">
        </form>
        </div>
        <div style="float: right;">
        <form action="index.php?mod=dbmf3&amp;tpl=admin/structure#a_block'.$block->id.'" method="post"
        OnSubmit="return mysb_confirm(\''.MySBUtil::str2strict(sprintf(_G('DBMF_confirm_blockref_delete'), $blockref->lname, $blockref->keyname )).'\')">
        <input type="hidden" name="block_id" value="'.$block->id.'">
        <input type="hidden" name="blockref_del" value="'.$blockref->id.'">
        <input  src="images/icons/user-trash.png"
                    type="image"
                    alt="'._G('DBMF_blockref_delete').'"
                    title="'._G('DBMF_blockref_delete').'">
        </form>
        </div>
        <div style="float: right;">
        <form action="index.php?mod=dbmf3&amp;tpl=admin/structure#a_block'.$block->id.'" method="post">
        <input type="hidden" name="blockref_switchactive" value="'.$blockref->id.'">';

            if($blockref->status==MYSB_DBMF_BLOCKREF_STATUS_ACTIVE)
                echo '
        <input type="submit" value="'._G('DBMF_blockref_desactive').'">';
            else
                echo '
        <input type="submit" value="'._G('DBMF_blockref_active').'">';

            echo '
        </form>
        </div>
        <div style="float: left; width: 80px; text-align: center;">
        <div style="display: inline-block;">
        <form action="index.php?mod=dbmf3&amp;tpl=admin/structure#a_block'.$block->id.'" method="post">
        <input type="hidden" name="blockref_orderdown" value="'.$blockref->id.'">
        <input  src="images/icons/go-down.png"
                    type="image"
                    alt="&darr;"
                    title="&darr;">
        </form>
        </div>
        <div style="display: inline-block;">
        <form action="index.php?mod=dbmf3&amp;tpl=admin/structure#a_block'.$block->id.'" method="post">
        <input type="hidden" name="blockref_orderup" value="'.$blockref->id.'">
        <input  src="images/icons/go-up.png"
                    type="image"
                    alt="&uarr;"
                    title="&uarr;">
        </form>
        </div>
        </div>
        '.$blockref->keyname.'<span class="cell_show"><br></span> <b>'._G($blockref->lname).'</b><br>
         <small>'.$blockref->getType().'</small>
    </div>
-->';
    }

    echo '
  <form action="'.$hrefconfig.'#a_blockrefs'.$block->id.'" method="post">
  <div class="row label border-top">
    <label class="col-sm-6" for="lname'.$block->id.'">
      '._G('DBMF_blockref_name').'
    </label>
    <div class="col-sm-6">
      <input type="text" name="lname" id="lname'.$block->id.'" value="">
    </div>
  </div>
  <div class="row label">
    <label class="col-sm-6" for="type'.$block->id.'">
    '._G('DBMF_blockref_type').'
    </label>
    <div class="col-sm-6">
      <select name="type" id="type'.$block->id.'">
        <option value="'.MYSB_VALUE_TYPE_INT.'" >int</option>
        <option value="'.MYSB_VALUE_TYPE_BOOL.'" >bool</option>
        <option value="'.MYSB_VALUE_TYPE_VARCHAR64.'" >varchar(64)</option>
        <option value="'.MYSB_VALUE_TYPE_VARCHAR512.'" >varchar(512)</option>
        <option value="'.MYSB_VALUE_TYPE_TEXT.'" >text/varchar(512)</option>
        <option value="'.MYSB_VALUE_TYPE_VARCHAR64_SELECT.'" >select/varchar(64)</option>
        <option value="'.MYSB_VALUE_TYPE_TEL.'" >tel/varchar(64)</option>
        <option value="'.MYSB_VALUE_TYPE_URL.'" >url/varchar(128)</option>
      </select>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-3"></div>
    <div class="col-sm-6">
        <input type="hidden" name="blockref_add" value="'.$block->id.'">
        <input type="submit" class="btn-primary"
               value="'._G('DBMF_blockref_add').'">
    </div>
    <div class="col-sm-3"></div>
  </div>
  </form>
</div>';
}

echo '
<div class="content">
<form action="'.$hrefconfig.'" method="post">
  <h1>'._G('DBMF_addblock').'</h1>
  <div class="row label">
    <label class="col-sm-6" for="addblock_name">
      '._G('DBMF_block_name').'
    </label>
    <div class="col-6">
      <input type="text" name="addblock_name" id="addblock_name">
    </div>
  </div>
  <div class="row">
    <div class="col-sm-3"></div>
    <div class="col-sm-6">
      <input type="hidden" name="block_add" value="1">
      <input type="submit" class="btn-primary"
             value="'._G('DBMF_block_add').'">
    </div>
    <div class="col-sm-3"></div>
  </div>
</form>
</div>';

?>
