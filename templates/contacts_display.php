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
<table style="font-size: 85%; width: 100%;"><tbody>';

echo '
<tr class="title">
    <td style="width: 24px;" class="title"></td>';

$pluginsDisplay = MySBPluginHelper::loadByType('DBMFDisplay');
$showcols_blockrefs = array();
$showcols = new MySBCSValues($app->auth_user->dbmf_showcols);
foreach($showcols->values as $br_id)
    $showcols_blockrefs[] = MySBDBMFBlockRefHelper::getByID($br_id);

foreach($pluginsDisplay as $plugin) 
    echo $plugin->displayTDHeader(1);
echo '
    <td><b>'._G("DBMF_common_lastname").'</b> '._G("DBMF_common_firstname").'</td>';
foreach($pluginsDisplay as $plugin) 
    echo $plugin->displayTDHeader(2);
foreach($showcols_blockrefs as $sblockref) {
        if( $sblockref->type==MYSB_VALUE_TYPE_TEXT or 
            $sblockref->type==MYSB_VALUE_TYPE_VARCHAR512 ) 
            $disp_px = '160';
        elseif( $sblockref->type==MYSB_VALUE_TYPE_VARCHAR64 or 
                $sblockref->type==MYSB_VALUE_TYPE_VARCHAR64_SELECT ) 
            $disp_px = '120';
        else $disp_px = '40';
        echo '
    <td style="width: '.$disp_px.'px;"><small>'.$sblockref->getReducedName().'</small></td>';
}

echo '
<td style="width: 24px;" class="title"></td>
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
    echo '<br><small>'.sprintf(_G('DBMF_days_old'),$daysold).'</small>';
    echo '
    </td>';
    foreach($pluginsDisplay as $plugin) 
        echo $plugin->displayTD(2,$contact);
    foreach($showcols_blockrefs as $sblockref) {
            $column_name = $sblockref->keyname;
            if($sblockref->type==MYSB_VALUE_TYPE_VARCHAR64_SELECT) $column_value = _G($contact->$column_name);
            else $column_value = MySBUtil::str2html($contact->$column_name);
            echo '
    <td>'.$column_value.'</td>';
    }

    echo '
    <td style="vertical-align: middle;">
    <form   action="#contact'.($anchor_nb-1).'" method="post" 
            OnSubmit="return mysb_confirm(\''.MySBUtil::str2strict(sprintf(_G('DBMF_confirm_contact_delete'),$contact->lastname, $contact->firstname )).'\')">
        <input  type="hidden" name="dbmf_contact_delete" value="'.$contact->id.'">
        <input  type="hidden" name="dbmf_request_reuse" value="1">
        <input  border="0" src="modules/dbmf3/images/delete_icon24.png"
                type="image">
    </form>
    </td>
</tr>';
}

echo '
</tbody></table>
</div>';

?>
