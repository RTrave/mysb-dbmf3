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

if( !isset($_SESSION["dbmf_memcatg_sort"]) ) 
    $_SESSION["dbmf_memcatg_sort"] = 0;
if( isset($_GET["sort"]) ) 
    $_SESSION["dbmf_memcatg_sort"] = $_GET["sort"];


echo '
<div class="sort_actions">
'._G('DBMF_mementos_sortby').':
<select name="dbmf_mementos_sortby" onchange="changesort(this.value);">
    <option value="0">'._G('DBMF_mementos_sortby_all').'</option>';
$memcatgs = MySBDBMFMementoCatgHelper::loadAvailable();
foreach( $memcatgs as $memcatg ) {
    echo '
    <option value="'.$memcatg->id.'" '.MySBUtil::form_isselected($memcatg->id,$_SESSION["dbmf_memcatg_sort"]).'>'.$memcatg->name.'</option>';
}
echo '
</select>
</div>

<br><br><br>';



if( isset($_GET['filter']) and $_GET['filter']=='all' ) {
    $mementos_p = MySBDBMFMementoHelper::load(null,$_SESSION["dbmf_memcatg_sort"]);
    echo '
<h2>'._G('DBMF_mementos_all').' ('.count($mementos_p).')</h2>';
} else {
    $_GET['filter']='';
    $mementos_p = MySBDBMFMementoHelper::loadActives($_SESSION["dbmf_memcatg_sort"]);
    echo '
<h2>'._G('DBMF_mementos_actives').' ('.count($mementos_p).')</h2>
';
}

echo '
<div id="dbmfMementoList">';

//_incI("mementos_sort","dbmf3");

$memento_type = -1;
$anchor_nb = 0;

foreach($mementos_p as $memento) {

    if($memento->type!=$memento_type) {
        if($memento_type!=-1) echo '
</div>';
        $memento_type = $memento->type;
        if($memento_type==0) $h3 = 'DBMF_memento_type_punctual';
        elseif($memento_type==1) $h3 = 'DBMF_memento_type_monthofyear';
        echo '
<h3>'._G($h3).'</h3>

<div class="list_support">';
    }

    //if( $_SESSION["dbmf_memcatg_sort"]!=0 and $memento->memcatg_id!=$_SESSION["dbmf_memcatg_sort"] ) continue;

    $contact = new MySBDBMFContact($memento->contact_id);
    $m_user = MySBUserHelper::getByID($memento->user_id);
    if($memento->memcatg_id!=0) $memcatg = MySBDBMFMementoCatgHelper::getByID($memento->memcatg_id);
    else $memcatg = null;
    if($memento->isActive()) $Active = true;
    else $Active = false;
    if($Active) $memclass = 'mem_active';
    elseif(!$Active and $memento->date_process!='') $memclass = 'mem_processed';
    else $memclass='';
    $anchor_nb++;

    echo '
<div class="cell roundtop roundbottom '.$memclass.'" id="memento'.$memento->id.'">
<table style="width: 100%; background-color: transparent;"><tbody>
<tr>
    <td class="infos">
        <div class="date floatingcell">';
    if($memento->isEditable()) echo '
        <a  href="index.php?mod=dbmf3&amp;tpl=memento_edit&amp;memento_id='.$memento->id.'"
            class="overlayed"><b>'.$memento->getDate().'</b></a>';
    else echo '
        <b>'.$memento->getDate().'</b>';
    if($memcatg!=null) $m_catgname = $memcatg->name;
    else $m_catgname = '<i>'.$m_user->login.'</i>';
    echo '<br>
        <span class="cell_hidem"><small>'.$m_catgname.'</small></span>
        </div>
        <div class="name floatingcell">
        <div style="float: left;">
        <a  id="memento'.$anchor_nb.'"
            href="index.php?mod=dbmf3&amp;tpl=contact_edit&amp;contact_id='.$contact->id.'"
            class="overlayed">
            <img    src="images/icons/text-editor.png" 
                    alt="Edition '.$contact->id.'" 
                    title="'._G('DBMF_edit').' '.$contact->lastname.' '.$contact->firstname.' (memento '.$memento->id.')">
        </a>
        </div>
        <b>'.MySBUtil::str2abbrv($contact->lastname,10,10).'</b><br>
        '.MySBUtil::str2abbrv($contact->firstname,10,10).'
        </div>
    </td>
    <td class="comments">
        <table style="width: 100%; background-color: transparent;"><tbody><tr>
            <td style="min-width: 20%;">'.$memento->comments.'</td>
            <td style="min-width: 20%;">'.$memento->comments2.'</td>
        </tr></tbody></table>
    </td>
    <td class="actions">';
    if($Active) {
        echo '
        <form action="#memento'.($anchor_nb-1).'" method="post">
            <input type="hidden" name="memento_process" value="'.$memento->id.'">
            <input type="submit" value="'._G('DBMF_memento_process_submit').'">
        </form>';
    } elseif(!$Active and $memento->date_process!='') {
        echo '
        <form action="#memento'.($anchor_nb-1).'" method="post">
            <input type="hidden" name="memento_unprocess" value="'.$memento->id.'">
            <input type="submit" value="'._G('DBMF_memento_unprocess_submit').'">
        </form>';
    }
    echo '
    </td>
</tr>
</tbody></table>
</div>
';

}

if( count($mementos_p)!=0 )
    echo '
</div>';




echo '
<script>
show("results");
function changesort(selvalue) {
    loadItem( "results", "index.php?mod=dbmf3&inc=mementos_sort&sort="+selvalue+"&filter='.$_GET['filter'].'" );
}
</script>';


?>