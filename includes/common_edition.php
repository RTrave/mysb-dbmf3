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

<div class="row" style="text-align: center;">
    <small>
        <b>'._G('DBMF_date_creat').': </b>'.$date_creat->strAEBY_l().' /
        <b>'._G('DBMF_date_modif').': </b>'.$date_modif->strAEBY_l().'
    </small>
</div>

<div class="row">
    <div class="right">';
if( $isEditor ) echo '<input type="text" name="lastname" size="24" maxlength="64" value="'.$contact->lastname.'">';
else echo $contact->lastname;
echo '</div>
    <b>'._G('DBMF_common_lastname').'</b>';
    if(MySBConfigHelper::Value('dbmf_ln_infos','dbmf3')!='')
        echo '<br><span class="help">'.MySBConfigHelper::Value('dbmf_ln_infos','dbmf3').'</span>';
echo '</div>

<div class="row">
    <div class="right">';
if( $isEditor ) echo '<input type="text" name="firstname" size="24" maxlength="64" value="'.$contact->firstname.'">';
else echo $contact->firstname;
echo '</div>
    <b>'._G('DBMF_common_firstname').'</b>';
    if(MySBConfigHelper::Value('dbmf_fn_infos','dbmf3')!='')
        echo '<br><span class="help">'.MySBConfigHelper::Value('dbmf_fn_infos','dbmf3').'</span>';
echo '</div>';

function mail_input( $i_mail, $email, $isEditor, $isLast=false, $style='' ) {
    echo '
<div class="row" '.$style.'>
    <div class="right">';
if( $isEditor ) {
    if( $isLast ) echo '
        <img src="images/icons/list-add.png"
             alt="'._G('DBMF_common_addmail').'"
             title="'._G('DBMF_common_addmail').'"
             id="dbmfmailaddicon"
             style="height: 20px; vertical-align: middle;"
             onClick="show(\'dbmfmailadd\');hide(\'dbmfmailaddicon\')">';
    echo '<input type="email" name="mail'.$i_mail.'" size="24" maxlength="64" value="'.$email.'">';
} else echo $email;
echo '</div>
    <b>'._G('DBMF_common_mail').' '.$i_mail.'</b>
</div>';
}

$cmail = explode(',',$contact->mail);
$i_mail = 0;
if( count($cmail)==0 ) mail_input( 1, '', $isEditor );
foreach($cmail as $email) {
    $i_mail++;
    if( count($cmail)==$i_mail ) mail_input( $i_mail, $email, $isEditor, true );
    else mail_input( $i_mail, $email, $isEditor );
}
$i_mail++;
mail_input( $i_mail, '', $isEditor, false, 'style="display: none;" id="dbmfmailadd"' );


echo '
<div class="title">
    <b>'._G('DBMF_contact_mementos_infos').'</b>
</div>

<div class="row">
';
$mementos = MySBDBMFMementoHelper::load($contact->id);
foreach($mementos as $memento) {
    //$memento_date = new MySBDateTime($memento->date_memento);
    if($memento->isActive()) $Active = true;
    else $Active = false;
    if($Active) $memclass = 'mem_active';
    elseif(!$Active and $memento->date_process!='') $memclass = 'mem_processed';
    else $memclass='';
    $m_user = MySBUserHelper::getByID($memento->user_id);
    if($memento->memcatg_id!=0) $memcatg = MySBDBMFMementoCatgHelper::getByID($memento->memcatg_id);
    else $memcatg = null;

    if($memento->isEditable())
        echo '
            <a  href="index.php?mod=dbmf3&amp;tpl=memento_edit&amp;memento_id='.$memento->id.'"
                class="overlayed"
                style="text-decoration: none;"
                data-overconfirm="'.MySBUtil::str2strict(_G('DBMF_confirm_memento_edition')).'">';
    echo '
    <div class="boxed" style="font-size: 90%; width: 90%; margin-bottom: 2px;">
    <div class="title roundtop '.$memclass.'" style="font-size: 90%; padding: 4px 4px 3px; min-height: 0px;" >
        <b>'.$memento->getDate().'</b>';
    if($memcatg!=null) $m_catgname = $memcatg->name;
    else $m_catgname = '<i>'.$m_user->login.'</i>';
    echo ' <div style="float: right;">'.$m_catgname.'</div>
        </div>
        <div class="row '.$memclass.'" style="font-size: 90%; padding: 2px 4px 0px; background: #eeeeee;">'.$memento->comments.'<br>
        </div>
    </div>';
    if($memento->isEditable())
        echo '</a>';
}

echo '
    <div class="row" style="font-size: 90%; text-align: center; border-bottom: 0px; background: transparent;">
        <a  href="index.php?mod=dbmf3&amp;tpl=memento_edit&amp;contact_id='.$contact->id.'"
            class="button overlayed"
            data-overconfirm="'.MySBUtil::str2strict(_G('DBMF_confirm_memento_edition')).'">
            '._G('DBMF_contact_mementos_create').'</a>
     </div>
</div>';

?>
