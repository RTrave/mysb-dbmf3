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
$pid = rand(10000,9999999);

/*
echo '
<script>
function LoadMatchContacts() {
    var x = document.getElementById("FormA");
    var text = "";
    var i;
    //for (i = 0; i < x.length ;i++) {
    //    text += x.elements[i].value + "<br>";
    //}
    text = x.elements[0].value + "<br>";
    document.getElementById("proposed").innerHTML = text;
}
</script>
';
*/

echo '
<div id="dbmfAutosubs">

<div class="col-md-8 col-unique">
<div class="content">

  <h1 class="bg-primary">'.MySBConfigHelper::Value('website_name').'</h1>

<form action="index.php?mod=dbmf3&amp;tpl=autosubs/step2&amp;contact_id=-1&amp;pid='.$pid.'"
      method="post">

  <h2>'._G('DBMF_autosubs_newcontact').'</h2>

  <div class="row label">
    <label class="col-sm-4" for="email'.$pid.'">
      Email
    </label>
    <div class="col-sm-8">
      <input type="email" autofocus
             name="email'.$pid.'" id="email'.$pid.'"
             maxlength="64" value="">
    </div>
  </div>

  <div class="row">
    <div class="col-sm-3"></div>
    <div class="col-sm-6">
      <input type="submit" class="btn-primary"
             value="'._G('DBMF_autosubs_submitmail').'">
    </div>
    <div class="col-sm-3"></div>
  </div>

</form>

</div>
</div>

</div>';

$app->view_menu(false);

?>
