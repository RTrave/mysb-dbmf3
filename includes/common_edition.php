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


if( !MySBRoleHelper::checkAccess('dbmf_user') ) return;

$contact = $app->tpl_currentcontact;
$date_creat = new MySBDateTime($contact->date_creat);
$date_modif = new MySBDateTime($contact->date_modif);

if( MySBRoleHelper::checkAccess('dbmf_editor',false) ) $isEditor = true;
else $isEditor = false;

echo '

<div class="row">
    <p class="col-12 t-center">
        <span class="help"><b>'._G('DBMF_date_creat').': </b>'.$date_creat->strAEBY_l().' /
        <b>'._G('DBMF_date_modif').': </b>'.$date_modif->strAEBY_l().'</span>
    </p>
</div>

<div class="row label">
  <label class="col-sm-4" for="lastname">
    '._G('DBMF_common_lastname').'<br>
    <span class="help">'.MySBConfigHelper::Value('dbmf_ln_infos','dbmf3').'</span>
  </label>
  <div class="col-sm-8">';
if( $isEditor ) echo '
    <input type="text" name="lastname" id="lastname"
           size="24" maxlength="64" value="'.$contact->lastname.'">';
else echo $contact->lastname;
echo '
  </div>
</div>

<div class="row label">
  <label class="col-sm-4" for="firstname">
    '._G('DBMF_common_firstname').'<br>
    <span class="help">'.MySBConfigHelper::Value('dbmf_fn_infos','dbmf3').'</span>
  </label>
  <div class="col-sm-8">';
if( $isEditor ) echo '
    <input type="text" name="firstname" id="firstname"
           maxlength="64" value="'.$contact->firstname.'">';
else echo $contact->firstname;
echo '
  </div>
</div>';


function mailRow( $i_mail, $email, $isEditor, $style='' ) {
  echo '
<div class="row label'.$style.'">
  <label class="col-sm-4" for="mail'.$i_mail.'">
    '._G('DBMF_common_mail').' '.$i_mail.'
  </label>';
  if( $isEditor ) {
    echo '
  <div class="col-sm-8">
    <input type="email" name="mail'.$i_mail.'" id="mail'.$i_mail.'"
           maxlength="64" value="'.$email.'">
  </div>';
  } else {
    echo '
  <div class="col-sm-8">
    '.$email.'
  </div>';
  }
  echo '
</div>';
}

$cmail = explode(',',$contact->mail);
$i_mail = 0;
if( count($cmail)==0 ) mailRow( 1, '', $isEditor );
foreach($cmail as $email) {
    $i_mail++;
    mailRow( $i_mail, $email, $isEditor );
}
$i_mail++;
mailRow( $i_mail, '', $isEditor, ' d-hide" id="mailAdded' );
if( $isEditor ) {
    echo '
<div class="row" id="mailAdd">
  <label class="col-sm-4" for="mail'.$i_mail.'">
    '._G('DBMF_common_addmail').'
  </label>
  <a href="javascript:void(0);"
     class="col-sm-8 btn btn-primary-light t-center"
     style="padding: 0;"
     title="'._G('DBMF_common_addmail').'"
     onClick="hide(\'mailAdd\');setTimeout(function(){ show(\'mailAdded\'); },300);">
        <img src="images/icons/list-add.png"
             alt="">
  </a>
</div>';
}

echo '
<h2 class="border-top">'._G('DBMF_contact_mementos_infos').'</h2>';
$mementos = MySBDBMFMementoHelper::load($contact->id);
foreach($mementos as $memento) {
    //$memento_date = new MySBDateTime($memento->date_memento);
    if($memento->isActive()) $Active = true;
    else $Active = false;
    if($Active) $memclass = 'mem_active';
    elseif(!$Active and $memento->date_process!='') $memclass = 'mem_processed';
    else $memclass='mem_processed';
    $m_user = MySBUserHelper::getByID($memento->user_id);
    if($memento->memcatg_id!=0) $memcatg = MySBDBMFMementoCatgHelper::getByID($memento->memcatg_id);
    else $memcatg = null;

    echo '
<div class="row contact-mementolist '.$memclass.'">';
    if($memento->isEditable())
        echo '
  <a  href="index.php?mod=dbmf3&amp;tpl=memento_edit&amp;memento_id='.$memento->id.'"
      class="overlayed col-12 btn-light"
      data-overconfirm="'.MySBUtil::str2strict(_G('DBMF_confirm_memento_edition')).'">';
    echo '
    <div>
      <div>
        <b>'.$memento->getDate().'</b>';
    if($memcatg!=null) $m_catgname = $memcatg->name;
    else $m_catgname = '<i>'.$m_user->login.'</i>';
    echo '
        <div class="f-right">'.$m_catgname.'</div>
      </div>
      <div>
        '.$memento->comments.'
      </div>
    </div>';
    if($memento->isEditable())
        echo '
  </a>';
    echo '
</div>';
}

echo '
<div class="row" style="padding-top: 0.5rem; padding-bottom: 0.5rem">
  <div class="col-sm-3"></div>
  <div class="col-sm-6 t-center">
    <a class="overlayed btn btn-primary"
       href="index.php?mod=dbmf3&amp;tpl=memento_edit&amp;contact_id='.$contact->id.'"
       data-overconfirm="'.MySBUtil::str2strict(_G('DBMF_confirm_memento_edition')).'">
      '._G('DBMF_contact_mementos_create').'
    </a>
  </div>
  <div class="col-sm-3"></div>
</div>';

?>
