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
<table width="100%" style="font-size: 85%;"><tbody>';

echo '
<tr class="title">
    <td width="24px" class="title"></td>';
$pluginsDisplay = MySBPluginHelper::loadByType('DBMFDisplay');
foreach($pluginsDisplay as $plugin) 
    echo $plugin->displayTDHeader(1);
echo '
    <td><b>'._G("DBMF_common_lastname").'</b> '._G("DBMF_common_firstname").'</td>';
foreach($pluginsDisplay as $plugin) 
    echo $plugin->displayTDHeader(2);
if(isset($app->tpl_display_columns)) 
foreach($app->tpl_display_columns as $column) {
    if($column->type==MYSB_VALUE_TYPE_TEXT) $disp_px = '200';
    else $disp_px = '70';
    echo '
    <td width="'.$disp_px.'px">'._G($column->lname).'</td>';
}
echo '
<td width="24px" class="title"></td>
</tr>';

$odd = 'odd';
$anchor_nb = 0;
while($data_print = MySBDB::fetch_array($search_result)) {

    $anchor_nb++;
    $contact = new MySBDBMFContact(null,$data_print);
    if($odd=='odd') {$odd='notodd';}
    else {$odd='odd';}; 
    echo '
<tr class='.$odd.'>
    <td>
        <a  name="contact'.$anchor_nb.'"
            href="javascript:editwinopen(\'index_wom.php?mod=dbmf3&amp;tpl=editcontact&amp;contact_id='.$contact->id.'&amp;mode=screen\',\'contactinfos\')">
        <img src="modules/dbmf3/images/edit_icon24.png" alt="Edition '.$contact->id.'" title="'._G('DBMF_edit').' '.$contact->lastname.' '.$contact->firstname.' ('.$contact->id.')">
        </a>
    </td>';
    foreach($pluginsDisplay as $plugin) 
        echo $plugin->displayTD(1,$contact);
    echo '
    <td>
        <b>'.$contact->lastname.'</b> '.$contact->firstname;
    $date_modif = new MySBDateTime($contact->date_modif);
    $daysold = $date_modif->getRest();
    //if($days_old!='' OR $days_new!='') 
        echo '<br><small>'.sprintf(_G('DBMF_days_old'),$daysold).'</small>';
    echo '
    </td>';
    foreach($pluginsDisplay as $plugin) 
        echo $plugin->displayTD(2,$contact);
    if(isset($app->tpl_display_columns)) 
    foreach($app->tpl_display_columns as $column) {
        $column_name = $column->keyname;
        if($column->type==MYSB_VALUE_TYPE_VARCHAR64_SELECT) $column_value = _G($contact->$column_name);
        else $column_value = MySBUtil::str2html($contact->$column_name);
        echo '
    <td>'.$column_value.'</td>';
    }
    echo '
    <td>
    <form   action="#contact'.($anchor_nb-1).'" method="post" 
            OnSubmit="return mysb_confirm(\''.MySBUtil::str2strict(sprintf(_G('DBMF_confirm_contact_delete'),$contact->lastname, $contact->firstname )).'\')">
        <input  type="hidden" name="dbmf_contact_delete" value="'.$contact->id.'">
        <input  type="hidden" name="dbmf_request_reuse" value="1">
        <input  border="0" src="modules/dbmf3/images/delete_icon24.png"
                type="image" value="submit" align="middle">
    </form>
    </td>
</tr>';
}

echo '
</tbody></table>
</div>';

?>
