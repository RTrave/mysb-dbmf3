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

_IncI('autosubs_head','dbmf3');

echo '
<script>
function LoadMatchContacts() {
    var x = document.getElementById("FormA");
    var text = "";
    var i;
    /*
    for (i = 0; i < x.length ;i++) {
        text += x.elements[i].value + "<br>";
    }
    */
    text = x.elements[0].value + "<br>";
    document.getElementById("proposed").innerHTML = text;
}
</script>
';

echo '
<form action="index.php?mod=dbmf3&amp;tpl=autosubs2&amp;blanklay=1&amp;contact_id=-1&amp;pid='.$pid.'" 
          method="post"
          class="overlayed"
          id="FormA">
<div class="boxed">
    <div class="title roundtop"><b>'._G('DBMF_autosubs_newcontact').'</b></div>
    <div class="row">Email
    <div class="right"><input   autofocus
                                type="email" 
                                name="email'.$pid.'" size="34" maxlength="64" 
                                value=""
                                onInput1="LoadMatchContacts();"></div>
    </div>
<div class="row" style="text-align: center;">
        <input  type="submit" 
                value="'._G('DBMF_autosubs_submitmail').'"
                style="font-size: 130%;">
    </div>
<p id="proposed"></p>
</div>
</form>';

_IncI('autosubs_foot','dbmf3');


?>
