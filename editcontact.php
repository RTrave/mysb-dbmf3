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


$contact = $app->tpl_currentcontact;


echo '
<h1>'._G("DBMF_contact_edition").'</h1>
<h2>'.$contact->lastname.' '.$contact->firstname.' ('.$contact->id.')</h2>';

$date_creat = new MySBDateTime($contact->date_creat);
$date_modif = new MySBDateTime($contact->date_modif);

echo '
<form action="?mod=dbmf3&amp;tpl=editcontact&amp;contact_id='.$contact->id.'" method="post">

<div class="table_support" align="center">
<table><tbody>

<tr class="title" >
    <td colspan="2">'._G("DBMF_contact_contact_infos").'</td>
</tr>
<tr>
    <td><b>'._G("DBMF_date_creat").':</b></td>
    <td>'.$date_creat->strAEBY_l().'</td>
</tr>
<tr>
    <td><b>'._G("DBMF_date_modif").':</b></td>
    <td>'.$date_modif->strAEBY_l().'</td>
</tr>

<tr class="title" >
    <td colspan="2">'._G("DBMF_contact_common_infos").'</td>
</tr>
<tr>
    <td><b>'._G("DBMF_lastname").':</b></td>
    <td><input type="text" name="lastname" size="24" maxlength="64" value="'.$contact->lastname.'"></td>
</tr>
<tr>
    <td><b>'._G("DBMF_firstname").':</b></td>
    <td><input type="text" name="firstname" size="24" maxlength="64" value="'.$contact->firstname.'"></td>
</tr>
<tr>
    <td><b>'._G("DBMF_organism").':</b></td>
    <td><input type="text" name="organism" size="56" maxlength="64" value="'.$contact->organism.'"></td>
</tr>
<tr>
    <td><b>'._G("DBMF_function").':</b></td>
    <td><textarea name="function" cols="64" rows="1">'.$contact->function.'</textarea></td>
</tr>
<tr>
    <td><b>'._G("DBMF_adress_1").':</b></td>
    <td><textarea name="adress_1" cols="64" rows="3">'.$contact->adress_1.'</textarea></td>
</tr>
<tr>
    <td><b>'._G("DBMF_adress_2").':</b></td>
    <td><textarea name="adress_2" cols="64" rows="3">'.$contact->adress_2.'</textarea></td>
</tr>
<tr>
    <td><b>'._G("DBMF_tel_pro").':</b></td>
    <td><input type="text" name="tel_pro" size="16" maxlength="16" value="'.$contact->tel_pro.'"></td>
</tr>
<tr>
    <td><b>'._G("DBMF_tel_dom").':</b></td>
    <td><input type="text" name="tel_dom" size="16" maxlength="16" value="'.$contact->tel_dom.'"></td>
</tr>
<tr>
    <td><b>'._G("DBMF_tel_fax").':</b></td>
    <td><input type="text" name="tel_fax" size="16" maxlength="16" value="'.$contact->tel_fax.'"></td>
</tr>
<tr>
    <td><b>'._G("DBMF_mail").':</b></td>
    <td><input type="text" name="mail" size="56" maxlength="64" value="'.$contact->mail.'">
    <input type="hidden" name="mail_old" value="'.$contact->mail.'"></td>
</tr>
<tr>
    <td><b>'._G("DBMF_comments").':</b></td>
    <td><textarea name="comments" cols="64" rows="3">'.$contact->comments.'</textarea></td>
</tr>';



echo '
<tr>
    <td colspan="2" align="center">
        <input type="hidden" name="contact_edit" value="1">
        <input type="submit" value="'._G('DBMF_contact_edition_submit').'" class="submit">
    </td>
</tr>

</tbody></table>
</div>
</form>
<br>
';

?>
