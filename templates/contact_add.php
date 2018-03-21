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
?>

<div class="col-lg-8 col-unique">
<div class="content">

<?php
if( isset($app->dbmf_req_wcheck) and $app->dbmf_req_wcheck!='' ) {

    echo '
<div id="newcontactselection" class="content list">
  <h1>'._G('DBMF_addcontact').'</h1>';
    $editor = new MySBEditor();
    echo $editor->init("simple");
    while($data_wcheck = MySBDB::fetch_array($app->dbmf_req_wcheck)) {
        echo '
  <div id="contact'.$data_wcheck['id'].'" class="row">
    <a href="index.php?mod=dbmf3&amp;tpl=contact_edit&amp;contact_id='.$data_wcheck['id'].'"
       class="overlayed col-12 btn-primary-light">
      <p>
      <img src="images/icons/text-editor.png"
           alt="" class="f-left">'._G('DBMF_addcontact_editentry').$data_wcheck['id'].':
        <b>'.$data_wcheck['lastname'].'</b> '.$data_wcheck['firstname'].'<br>
        &lt;'.$data_wcheck['mail'].'&gt;</p>
    </a>
  </div>';
    }
    $lastname = str_replace('"', '\'', $_POST['lastname']);
    $firstname = str_replace('"', '\'', $_POST['firstname']);
    $mail = str_replace('"', '\'', $_POST['mail']);
    echo '

  <div class="row border-top">
    <form action="index.php?mod=dbmf3&amp;tpl=contact_edit&amp;contact_id=-1"
          method="post"
          class="overlayed">
    <div class="col-sm-2"></div>
    <div class="col-sm-8">
      <input type="hidden" name="lastname" value="'.$lastname.'">
      <input type="hidden" name="firstname" value="'.$firstname.'">
      <input type="hidden" name="mail" value="'.$mail.'">
      <input  type="submit" class="btn-primary"
              value="'._G('DBMF_addcontact_newentry').':
'.$lastname.' '.$firstname.' <'.$mail.'>">
    </div>
    <div class="col-sm-2"></div>
    </form>
  </div>
</div>
<div id="newcontactok" style="display: none;">
    '._G('DBMF_addcontact_newentry').' OK!
</div>';

} else {

    echo '
<form action="index.php?mod=dbmf3&amp;tpl=contact_add" method="post">

  <h1>'._G('DBMF_addcontact').'</h1>

  <div class="row label">
    <label class="col-sm-4" for="lastname">
      '._G('DBMF_common_lastname').'<br>
      <span class="help">'.MySBConfigHelper::Value('dbmf_ln_infos','dbmf3').'</span>
    </label>
    <div class="col-sm-8">
    <input type="text" name="lastname" id="lastname"
           maxlength="64" value="">
    </div>
  </div>

  <div class="row label">
    <label class="col-sm-4" for="firstname">
      '._G('DBMF_common_firstname').'<br>
      <span class="help">'.MySBConfigHelper::Value('dbmf_fn_infos','dbmf3').'</span>
    </label>
    <div class="col-sm-8">
      <input type="text" name="firstname" id="firstname"
             maxlength="64" value="">
    </div>
  </div>

  <div class="row label">
    <label class="col-sm-4" for="mail">
      '._G('DBMF_common_mail').'
    </label>
    <div class="col-sm-8">
      <input type="email" name="mail" id="mail"
             maxlength="64" value="">
    </div>
  </div>

  <div class="row border-top">
    <div class="col-sm-3"></div>
    <div class="col-sm-6">
      <input type="submit" class="btn-primary"
             value="'._G('DBMF_addcontact_verify').'">
    </div>
    <div class="col-sm-3"></div>
  </div>

</form>
</div>';
}
?>

</div>
</div>
