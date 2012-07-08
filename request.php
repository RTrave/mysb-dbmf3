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

echo '
<h1>'._G('DBMF_request_title').'</h1>
<h2>'._G('DBMF_search').'</h2>

<form action="" method="post">
<input type="hidden" name="dbmf_request" value="1">
<ul>
<li>
    '._G('DBMF_search_lastname').': <br>
    <input type="text" name="search_name" size="24" maxlength="64" value="'.$_POST['search_name'].'">
</li>
<li>
    '._G('DBMF_search_all_fields').': <br>
    <input type="text" name="search_all" size="24" maxlength="64" value="'.$_POST['search_all'].'">
</li>
</ul>
<p>
<input type="submit" value="'._G('DBMF_search_submit').'" class="submit">
</p>
</form>
';

if(isset($_POST['dbmf_request'])) {
    echo '
<h3>'._G('DBMF_search_results').'</h3>
<p>
'.MySBDB::num_rows($app->dbmf_search_result).' results<br>
</p>
<div class="table_support">';

    echo '
</div>';
}

?>
