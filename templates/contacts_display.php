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

function ContactDisplayPlugins( $sblockref,$contact,$class_string ) {
            if( $sblockref->type==MYSB_VALUE_TYPE_TEXT or 
            $sblockref->type==MYSB_VALUE_TYPE_VARCHAR512 ) 
            $disp_class = 'w180';
        elseif( $sblockref->type==MYSB_VALUE_TYPE_VARCHAR64 or 
                $sblockref->type==MYSB_VALUE_TYPE_VARCHAR64_SELECT ) 
            $disp_class = 'w120';
        elseif( $sblockref->type==MYSB_VALUE_TYPE_TEL or 
                $sblockref->type==MYSB_VALUE_TYPE_URL ) 
            $disp_class = 'w80';
        else $disp_class = 'w60';
        echo '
    <div    class="cell_plug '.$class_string.' '.$disp_class.'" 
            style="display: inline-block;">
    <table>
        <tr><td class="title">
            '.$sblockref->getReducedName().'
        </td></tr>
        <tr><td class="text">';
    $column_name = $sblockref->keyname;
            if( $sblockref->type==MYSB_VALUE_TYPE_VARCHAR64_SELECT ) 
                $column_value = _G($contact->$column_name);
            elseif( $sblockref->type==MYSB_VALUE_TYPE_TEL or
                    $sblockref->type==MYSB_VALUE_TYPE_URL ) 
                $column_value = '<div style="vertical-align: middle; display: inline-block;">'.$sblockref->htmlFormNonEditable('',$contact->$column_name,MySBUtil::str2abbrv(_G($sblockref->lname),4,4)).'</div>';
            elseif( $sblockref->type==MYSB_VALUE_TYPE_INT ) 
                { $column_value = $contact->$column_name; if( $column_value=='' ) $column_value = '0'; }
            elseif( $sblockref->type==MYSB_VALUE_TYPE_BOOL )
                $column_value = $sblockref->htmlFormNonEditable('',$contact->$column_name );
            else $column_value = MySBUtil::str2html($contact->$column_name);
            echo '
        '.$column_value.'
        
        </td></tr>
    </table>';
        echo '
    </div>';

}

$search_result = $app->tpl_dbmf_searchresult;
$pluginsDisplay = MySBPluginHelper::loadByType('DBMFDisplay');
$showcols_blockrefs = array();
$showcols = new MySBCSValues($app->auth_user->dbmf_showcols);
foreach($showcols->values as $br_id) {
    $showblockref = MySBDBMFBlockRefHelper::getByID($br_id);
    if( isset($showblockref) and $showblockref->isActive() )
        $showcols_blockrefs[] = $showblockref;
}

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
<div class="cell roundtop roundbottom" id="contact'.$contact->id.'">
<table class="cell"><tbody>

<tr class="cell">
    <td style="width: 20px; text-align: left;">
<!--
        <a  id="contact'.$anchor_nb.'"
            href="javascript:editwinopen(\'index_wom.php?mod=dbmf3&amp;tpl=editcontact&amp;contact_id='.$contact->id.'\',\'contactinfos\')">
        <img    src="images/icons/text-editor.png" 
                alt="Edition '.$contact->id.'" 
                title="'._G('DBMF_edit').' '.$contact->lastname.' '.$contact->firstname.' ('.$contact->id.')"
                style="width: 24px"></a>
-->
        <a  id="contact'.$anchor_nb.'"
            class="overlayed"
            href="blank.php?mod=dbmf3&amp;tpl=editcontact&amp;contact_id='.$contact->id.'">
        <img    src="images/icons/text-editor.png" 
                alt="Edition '.$contact->id.'" 
                title="'._G('DBMF_edit').' '.$contact->lastname.' '.$contact->firstname.' ('.$contact->id.')"
                style="width: 24px"></a>
    </td>
    <td style="width: 26px; text-align: left;">';
    if( $contact->mail!='' ) echo '
            <a href="mailto:'.$contact->mail.'">
            <img src="images/icons/mail-unread.png" 
                 alt="'._G('DBMF_mailto').' '.$contact->id.'" 
                 title="'._G('DBMF_mailto').' '.$contact->lastname.' '.$contact->firstname.' ('.$contact->id.')"></a>';

    echo '
    </td>
    <td colspan="2" class="cell_names" style="text-align: left;">
        <b>'.$contact->lastname.'</b><br>'.$contact->firstname.'
    </td>
    <td rowspan="2" class="cell_plugins">
        <div class="incell_hide">';
    
    $as_showlist = MySBDBMFBlockRefHelper::loadAlwaysShown(MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_ASPLUG);
    foreach($as_showlist as $sblockref) 
        ContactDisplayPlugins( $sblockref, $contact, 'incell_hide' );

    foreach($showcols_blockrefs as $sblockref) 
        ContactDisplayPlugins( $sblockref, $contact, 'incell_hide' );
 
    echo '
        </div>
    </td>
    <td style="width: 20px; height: 20px; text-align: right; vertical-align: top;">
        <form   action="blank.php?mod=dbmf3&amp;tpl=delcontact&amp;contact_id='.$contact->id.'" 
                method="post" 
                class="hidelayed"
                data-overconfirm="'.MySBUtil::str2strict(sprintf(_G('DBMF_confirm_contact_delete'),$contact->lastname, $contact->firstname )).'">
            <input  type="hidden" name="dbmf_contact_delete" value="'.$contact->id.'">
            <input  type="hidden" name="dbmf_request_reuse" value="1">
            <input  src="images/icons/user-trash.png"
                    type="image"
                    alt="'._G('DBMF_contact_delete').'"
                    title="'.sprintf(_G('DBMF_contact_delete'),$contact->lastname, $contact->firstname ).'">
        </form>
    </td>
</tr>

<tr class="cell">
    <td colspan="3" rowspan="2" class="cell_infos">
        <div class="cell_hide"><small><i>'.sprintf(_G('DBMF_days_old'),$daysold).'</i></small></div>
        ';

    $as_textonlylist = MySBDBMFBlockRefHelper::loadAlwaysShown(MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_ASTEXTONLY);
    foreach($as_textonlylist as $sblockref) {
        $column_name = $sblockref->keyname;
        if( $contact->$column_name!='' )
            echo '
            '.$sblockref->htmlFormNonEditable('',$contact->$column_name,_G($sblockref->lname)).'<br>';
    }
    echo '
        <div class="cell_hide">';
    $as_textlist = MySBDBMFBlockRefHelper::loadAlwaysShown(MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_ASTEXT);
    foreach($as_textlist as $sblockref) {
        $column_name = $sblockref->keyname;
        if( $contact->$column_name!='' )
            echo '
            '.$sblockref->htmlFormNonEditable('',$contact->$column_name,MySBUtil::str2abbrv(_G($sblockref->lname),3,3)).'<br>';
    }

    echo '
        </div>
    </td>
    <td class="cell_ext">
    </td>
    <td>
    </td>
</tr>

<tr class="cell">
    <td colspan="3" style="" class="cell_icons">
        <div class="cell_hide">
';
    foreach($pluginsDisplay as $plugin) 
        echo $plugin->displayIcons(1,$contact);
    echo '
        </div>
        ';

    $as_hidelist = MySBDBMFBlockRefHelper::loadAlwaysShown(MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_ASTEXT);
    $as_showlist = MySBDBMFBlockRefHelper::loadAlwaysShown(MYSB_DBMF_BLOCKREF_ALWAYSSHOWN_ASPLUG);
    $size_cell = 50*( count($as_hidelist) + count($as_showlist) );
    echo '
        <div class="incell_show" style="text-align: right; width: '.$size_cell.'px;">';
    
    foreach($as_hidelist as $sblockref) 
        ContactDisplayPlugins( $sblockref, $contact, 'incell_floatshow' );

    foreach($as_showlist as $sblockref) 
        ContactDisplayPlugins( $sblockref, $contact, '' );

    echo '
        </div>
    </td>
</tr>

</tbody></table>
</div>';

    }

echo '
</div>
';

?>
