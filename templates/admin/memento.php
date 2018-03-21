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

echo '
<div class="content">
  <h1>'._G('DBMF_memcatg_config').'</h1>

';

$memcatgs = MySBDBMFMementoCatgHelper::load();
$groups = MySBGroupHelper::load();
foreach( $memcatgs as $memcatg ) {
    echo '
    <div id="memcatg'.$memcatg->id.'">';
    $app->tpl_dbmf_currentmemcatg = $memcatg;
    include( _pathI('admin/memcatg_display_ctrl','dbmf3') );
    echo '
    </div>';
}

echo '
</div>

<form action="'.$hrefconfig.'" method="post">
<div class="content">

  <h1>'._G('DBMF_memcatg_new').'</h1>

  <div class="row label">
    <label class="col-6" for="">
      <b>'._G('DBMF_memcatg_name').':</b>
    </label>
    <div class="col-6">
        <input type="text" name="memcatg_name_new"
               value="" id="memcatg_name_new">
    </div>
  </div>

  <div class="row">
    <div class="col-sm-3"></div>
    <div class="col-sm-6">
      <input type="hidden" name="memcatg_new" value="1">
      <input type="submit" class="btn-primary"
             value="'._G('DBMF_memcatg_newsubmit').'">
    </div>
    <div class="col-sm-3"></div>
  </div>

</div>
</form>';

?>
