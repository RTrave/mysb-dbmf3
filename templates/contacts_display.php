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
$pluginsDisplay = MySBPluginHelper::loadByType('DBMFDisplay');
$showcols_blockrefs = array();
$showcols = new MySBCSValues($app->auth_user->dbmf_showcols);
foreach($showcols->values as $br_id)
    $showcols_blockrefs[] = MySBDBMFBlockRefHelper::getByID($br_id);

echo '
<div class="list_support">';


$odd = 'odd';
$anchor_nb = 0;
while($data_print = MySBDB::fetch_array($search_result)) {

    $contact = new MySBDBMFContact(null,$data_print);
    $date_modif = new MySBDateTime($contact->date_modif);
    $daysold = $date_modif->absDiff();

    $anchor_nb++;
    echo '
<div class="cell roundtop roundbottom">
<table class="cell"><tbody>

<tr class="cell">
    <td style="width: 20px; text-align: right;">
        <a  name="contact'.$anchor_nb.'"
            href="javascript:editwinopen(\'index_wom.php?mod=dbmf3&amp;tpl=editcontact&amp;contact_id='.$contact->id.'&amp;mode=screen\',\'contactinfos\')">
        <img src="images/icons/text-editor.png" alt="Edition '.$contact->id.'" title="'._G('DBMF_edit').' '.$contact->lastname.' '.$contact->firstname.' ('.$contact->id.')"></a>
    </td>
    <td style="width: 20px; text-align: right;">';
    if( $contact->mail!='' ) echo '
            <a href="mailto:'.$contact->mail.'">
            <img src="images/icons/mail-unread.png" 
                 alt="'._G('DBMF_mailto').' '.$contact->id.'" 
                 title="'._G('DBMF_mailto').' '.$contact->lastname.' '.$contact->firstname.' ('.$contact->id.')"></a>';
    echo '
    </td>
    <td class="cell_names" style="text-align: left;">
        <b>'.$contact->lastname.'</b> '.$contact->firstname.'
        <div class="cell_show" style="text-align: right;">
        <i><a href="tel:'.$contact->b1r05.'">'.$contact->b1r05.'</a></i>
        </div>
    </td>
    <td rowspan="2" class="cell_plugins" style="text-align: right;">
        <div class="cell_hide">';
        
    foreach($showcols_blockrefs as $sblockref) {
        if( $sblockref->type==MYSB_VALUE_TYPE_TEXT or 
            $sblockref->type==MYSB_VALUE_TYPE_VARCHAR512 ) 
            $disp_px = '180';
        elseif( $sblockref->type==MYSB_VALUE_TYPE_VARCHAR64 or 
                $sblockref->type==MYSB_VALUE_TYPE_VARCHAR64_SELECT ) 
            $disp_px = '120';
        else $disp_px = '60';
        echo '
    <div class="cell_plug" style="width: '.$disp_px.'px;">
    <div style="background: #999999;">
        <small><small>'.$sblockref->getReducedName().'</small><br>';
    $column_name = $sblockref->keyname;
            if($sblockref->type==MYSB_VALUE_TYPE_VARCHAR64_SELECT) $column_value = _G($contact->$column_name);
            elseif($sblockref->type==MYSB_VALUE_TYPE_TEL or
                   $sblockref->type==MYSB_VALUE_TYPE_URL ) $column_value = '<div style="vertical-align: middle;">'.$sblockref->htmlFormNonEditable('',$contact->$column_name).'</div>';
            else $column_value = MySBUtil::str2html($contact->$column_name);
            echo '
    <b>'.$column_value.'</b>';
        echo '
        </small>
    </div>
    </div>';
    }

        
    echo '
        </div>
    </td>
    <td style="width: 20px; height: 20px; text-align: right; vertical-align: top;">
        <form   action="#contact'.($anchor_nb-1).'" method="post" 
                OnSubmit="return mysb_confirm(\''.MySBUtil::str2strict(sprintf(_G('DBMF_confirm_contact_delete'),$contact->lastname, $contact->firstname )).'\')">
            <input  type="hidden" name="dbmf_contact_delete" value="'.$contact->id.'">
            <input  type="hidden" name="dbmf_request_reuse" value="1">
            <input  border="0" src="images/icons/user-trash.png"
                    type="image">
        </form>
    </td>
</tr>

<tr class="cell">
    <td colspan="3" rowspan="2" style="text-align: left; vertical-align: top;">
        <div class="cell_hide">
            <small><i>'.sprintf(_G('DBMF_days_old'),$daysold).'</i></small><br>';
    if( $contact->b1r02!='' ) echo '
            <i>'.$contact->b1r02.'</i><br>';
    echo '
            '.$contact->b1r03.'<br>';
    if( $contact->b1r05!='' ) echo '
            <i><a href="tel:'.$contact->b1r05.'">'.$contact->b1r05.'</a></i><br>';
    if( $contact->b1r06!='' ) echo '
            <i><a href="tel:'.$contact->b1r06.'">'.$contact->b1r06.'</a></i>';
    echo '
        </div>
    </td>
</tr>

<tr class="cell">
    <td colspan="2" style="max-height: 20px; text-align: right; vertical-align: bottom;">
        <div class="cell_hide">
';
    foreach($pluginsDisplay as $plugin) 
        echo $plugin->displayIcons(1,$contact);
    echo '
        </div>
    </td>
</tr>

</tbody></table>
</div>';

    }

/*
foreach($pluginsDisplay as $plugin) 
    echo $plugin->displayIcons(1,$contact);
*/

echo '
</div>
';
/*
MySBDB::data_seek($search_result,0);

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
    <td style="width: 24px;"></td>
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
    <td>';
    if( $contact->mail!='' ) 
        echo '
        <a href="mailto:'.$contact->mail.'">
            <img src="modules/dbmf3/images/mail_icon24.png" 
                 alt="'._G('DBMF_mailto').' '.$contact->id.'" 
                 title="'._G('DBMF_mailto').' '.$contact->lastname.' '.$contact->firstname.' ('.$contact->id.')">
        </a>';
    echo '
    </td>
    <td>
        <b>'.$contact->lastname.'</b> '.$contact->firstname;
    $date_modif = new MySBDateTime($contact->date_modif);
    $daysold = $date_modif->absDiff();
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
*/
?>
