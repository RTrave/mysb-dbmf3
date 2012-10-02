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
<table width="100%"><tbody>';

echo '
<tr class="title">
    <td width="24px" class="title"></td><td width="24px"></td>
    <td><b>'._G("DBMF_common_lastname").'</b> '._G("DBMF_common_firstname").'</td>
    <td width="200px"><i>'._G("DBMF_common_function").'</i><br>'._G("DBMF_common_organism").'</td>
    <td width="180px">Tel</td>';
if(isset($app->tpl_display_columns)) 
foreach($app->tpl_display_columns as $column) {
    if($column->type==MYSB_VALUE_TYPE_TEXT) $disp_px = '200';
    else $disp_px = '70';
    echo '
    <td width="'.$disp_px.'px">'._G($column->lname).'</td>';
}
echo '
</tr>';

$odd = 'odd';
while($data_print = MySBDB::fetch_array($search_result)) {

    $contact = new MySBDBMFContact(null,$data_print);
    if($odd=='odd') {$odd='notodd';}
    else {$odd='odd';}; 
    echo '
<tr class='.$odd.'>
    <td>
        <a href="javascript:editwinopen(\'index_wom.php?mod=dbmf3&amp;tpl=editcontact&amp;contact_id='.$contact->id.'&amp;mode=screen\',\'contactinfos\')">
        <img src="modules/dbmf3/images/edit_icon24.png" alt="Edition '.$contact->id.'" title="'._G('DBMF_edit').' '.$contact->lastname.' '.$contact->firstname.' ('.$contact->id.')">
        </a>
    </td>
    <td>';
    if($contact->b1r08!='') 
        echo '
        <a href="mailto:'.$contact->b1r08.'">
            <img src="modules/dbmf3/images/mail_icon24.png" alt="'._G('DBMF_mailto').' '.$contact->id.'" title="Mail to '.$contact->lastname.' '.$contact->firstname.' ('.$contact->id.')">
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
        <i>'.$contact->b1r02.'</i><br>'.$contact->b1r03.'
    </td>
    <td>
        <i>'._G('DBMF_common_tel_1').':</i> '.$contact->b1r05.'<br><i>'._G('DBMF_common_tel_2').':</i> '.$contact->b1r06.'
    </td>';
    if(isset($app->tpl_display_columns)) 
    foreach($app->tpl_display_columns as $column) {
        $column_name = $column->keyname;
        if($column->type==MYSB_VALUE_TYPE_VARCHAR64_SELECT) $column_value = _G($contact->$column_name);
        else $column_value = MySBUtil::str2html($contact->$column_name);
        echo '
    <td>'.$column_value.'</td>';
    }
    echo '
</tr>';
}

echo '
</tbody></table>
</div>';

?>
