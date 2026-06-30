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

if(!MySBRoleHelper::checkAccess('dbmf_user')) return;

$editor = new MySBEditor();
//echo $editor->init("simple");

global $_GET;
if ( !isset($_GET['filter']) or $_GET['filter']=='' )
  $_GET['filter'] = 'actives';

function isActive($tpl_code) {
  if( $_GET['filter']==$tpl_code )
    return 'no-collapse';
  else return '';
}
?>

<div class="row">

<div class="col-lg-3">

<div class="navbar" id="NavBarColumn">
<ul>
  <li class="<?= isActive('actives') ?>">
    <a href="index.php?mod=dbmf3&amp;tpl=mementos">
      <?= _G('DBMF_mementos_actives') ?></a>
  </li><li class="icon-responsive">
    <a href="javascript:void(0);"
       onclick="responsiveToggle('NavBarColumn','navbar')">
      <img src="images/icons/view-list.png" alt="view-list">
    </a>
  </li><li class="<?= isActive('all') ?>">
    <a href="index.php?mod=dbmf3&amp;tpl=mementos&amp;filter=all">
    <?= _G('DBMF_mementos_all') ?></a>
  </li><li class="<?= isActive('bycontact') ?>">
    <a href="index.php?mod=dbmf3&amp;tpl=mementos&amp;filter=bycontact">
    <?= _G('DBMF_mementos_bycontact') ?></a>
  </li>
</ul>
</div>

</div>
<div class="col-lg-9">

<form   action="index.php?mod=dbmf3&amp;tpl=mementos_sel"
        method="post"
        class="overlayed">

<div id="mementos_results">
<?php 
if($_GET['filter']=='actives' || $_GET['filter']=='all')
  include( _pathT('mementos_sort','dbmf3') );
  //include( _pathI('mementos_sort_ctrl','dbmf3') );
elseif($_GET['filter']=='bycontact')
  include( _pathT('mementos_sortbycontact','dbmf3') );
?>

<div class="row bg-primary">
  <div class="col-sm-3">
    <h1 class="t-left">Selection:</h1>
  </div>
  <div class="col-sm-6 row">
    <select name="memsel_action">
        <option value=""><?= _G('DBMF_memento_actiontype_selection') ?></option>
        <option value="process"><?= _G('DBMF_memento_actiontype_process') ?></option>
        <option value="unprocess"><?= _G('DBMF_memento_actiontype_unprocess') ?></option>
        <option value="delete"><?= _G('DBMF_memento_actiontype_delete') ?></option>
    </select>
  </div>
  <div class="col-sm-3 row">
    <input type="hidden" name="memsel_ope" value="1">
    <input type="submit" class="btn-primary-light" value="<?= _G('DBMF_memento_action_submit') ?>">
  </div>
</div>

</div>

</form>

</div>
</div>
