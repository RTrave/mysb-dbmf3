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

if(!MySBRoleHelper::checkAccess('dbmf_config')) return;

global $_GET;
if ( !isset($_GET['page']) or $_GET['page']=='' )
  $_GET['page'] = 'structure';

function isActive($tpl_code) {
  if( $_GET['page']==$tpl_code )
    return 'no-collapse';
  else return '';
}
?>

<div class="row">

<div class="col-lg-3">

<div class="navbar" id="NavBarColumn">
<ul>
  <li class="<?= isActive('structure') ?>">
    <a href="index.php?mod=dbmf3&amp;tpl=admin/config&amp;page=structure">
      <?= _G('DBMF_blocks_config') ?></a>
  </li><li class="icon-responsive">
    <a href="javascript:void(0);"
       onclick="responsiveToggle('NavBarColumn','navbar')">
      <img src="images/icons/view-list.png" alt="view-list">
    </a>
  </li><li class="<?= isActive('memento') ?>">
    <a href="index.php?mod=dbmf3&amp;tpl=admin/config&amp;page=memento">
    <?= _G('DBMF_mementos_config') ?></a>
  </li>
</ul>
</div>

</div>
<div class="col-lg-9">


<?php

$hrefconfig = 'index.php?mod=dbmf3&amp;tpl=admin/config&amp;page='.$_GET['page'];

include( _pathT('admin/'.$_GET['page'].'_ctrl','dbmf3') );

?>
</div>
</div>
