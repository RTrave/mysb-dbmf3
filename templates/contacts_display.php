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


$search_result = $app->tpl_dbmf_searchresult;

echo '
<div class="table_support">
<table id="render" width="100%"><tbody>';

echo '
<tr class="title">
    <td width="24px" class="title"></td><td width="24px"></td>
    <td><b>'._G("DBMF_lastname").'</b> '._G("DBMF_firstname").'</td>
    <td width="200px"><i>'._G("DBMF_function").'</i><br>'._G("DBMF_organism").'</td>
    <td width="180px">Tel</td>
</tr>';

$odd = 'odd';
while($data_print = MySBDB::fetch_array($search_result)) {

    $contact = new MySBDBMFContact(null,$data_print);
    if($odd=='odd') {$odd='notodd';}
    else {$odd='odd';}; 
    echo '
<tr class='.$odd.'>
    <td width="24px">
        <a href="javascript:editwinopen(\'index_wom.php?mod=dbmf3&amp;tpl=editcontact&amp;contact_id='.$contact->id.'&amp;mode=screen\',\'contactinfos\')">
        <img src="modules/dbmf3/images/edit_icon24.png" alt="Edition '.$contact->id.'" title="Edition '.$contact->id.'">
        </a>
    </td>
    <td width="32px">';
    if($contact->mail!='') 
        echo '
        <a href="mailto:'.$contact->mail.'">
            <img src="modules/dbmf3/images/mail_icon24.png" alt="Mail to '.$contact->id.'" title="Mail to '.$contact->id.'">
        </a>';
    echo '
    </td>
    <td>
        <b>'.$contact->lastname.'</b> '.$contact->firstname;
    $date_modif = new MySBDateTime($contact->date_modif);
    $daysold = $date_modif->getRest();
    //if($days_old!='' OR $days_new!='') 
        echo '<br><small>'.sprintf(_G('DBMF_days_old'),$daysold).'</small>';
    echo '
    </td>
    <td>
        <i>'.$contact->function.'</i><br>'.$contact->organism.'
    </td>
    <td>
        <i>pro:</i>'.$contact->tel_pro.'<br><i>dom:</i>'.$contact->tel_dom.'
    </td>';
    echo '
</tr>';
}

echo '
</tbody></table>
</div>';

?>
