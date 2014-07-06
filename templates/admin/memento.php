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
<div id="mysbMenuLevel">
<ul>
    <li><a href="index.php?mod=dbmf3&amp;tpl=admin/structure">'._G('DBMF_blocks_config').'</a></li>
    <li><a href="index.php?mod=dbmf3&amp;tpl=admin/memento">'._G('DBMF_mementos_config').'</a></li>
</ul>
</div>

<h1>'._G('DBMF_config').': '._G('DBMF_mementos_config').'</h1>

<h2>'._G('DBMF_memcatg_config').'</h2>

<div class="list_support" style="font-size: 90%;">
';

$memcatgs = MySBDBMFMementoCatgHelper::load();
$groups = MySBGroupHelper::load();
foreach( $memcatgs as $memcatg ) {
    echo '
    <div class="boxed" id="memcatg'.$memcatg->id.'" 
         style="width: 450px; margin: 10px auto 3px;">';
    $app->tpl_dbmf_currentmemcatg = $memcatg;
    _incI('admin/memcatg_display','dbmf3');
    echo '
    </div>';
}

echo '
<form action="index.php?mod=dbmf3&amp;tpl=admin/memento" method="post">
    <div class="boxed"
         style="width: 450px; margin-top: 35px;">

        <div class="title roundtop">
            <b>'._G('DBMF_memcatg_new').'</b>
        </div>

        <div class="row">
            <div class="right"><input type="text" name="memcatg_name_new" value=""></div>
            <b>'._G('DBMF_memcatg_name').':</b>
        </div>

        <div class="row" style="text-align: center;">
            <input type="hidden" name="memcatg_new" value="1">
            <input type="submit" value="'._G('DBMF_memcatg_newsubmit').'">
        </div>
    </div>
</form>

</div>
';

?>
