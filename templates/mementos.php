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

echo '
<div id="mysbMenuLevel">
<ul>
    <li class="first"><a href="index.php?mod=dbmf3&amp;tpl=mementos">'._G('DBMF_mementos_actives').'</a></li>
    <li class="last"><a href="index.php?mod=dbmf3&amp;tpl=mementos&amp;filter=all">'._G('DBMF_mementos_all').'</a></li>
</ul>
</div>

<h1>'._G('DBMF_mementos_summary').'</h1>

<div id="mementos_results">
';


_incI('mementos_sort','dbmf3');

echo '
</div>

</div>';

?>
