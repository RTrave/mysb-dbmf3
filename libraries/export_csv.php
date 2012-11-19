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


/**
 * DBMF CSV Export class
 * 
 */
class MySBDBMFExportCSV extends MySBDBMFExport {

    public function __construct($id=-1, $data_export = array()) {
        global $app;
        parent::__construct($id,(array) ($data_export));
    }

/*
    public function update($data_export) {
        parent::update( $data_export );
    }
*/

    public function selectionProcess( $selection ) {
        
    }

    public function htmlParamForm() {
        $showfields_colsnb = MySBConfigHelper::Value('dbmf_showfields_colsnb', 'dbmf3');
        $output = '';
        $blocks = MySBDBMFBlockHelper::load();
        $output .= '
<div class="table_support">
'._G('DBMF_display_orderby').':
<select name="dbmf_exportdisplay_orderby'.$this->id.'">
    <option value="lastname">'._G('DBMF_common_lastname').'</option>
    <option value="b1r03">'._G('DBMF_common_organism').'</option>
    <option value="date_modif">'._G('DBMF_date_modif').'</option>
</select><br>
'._G('DBMF_display_showfield').':<br>
<table style="border: 0px; font-size: 80%"><tbody>
<tr>';

        $col_nb = 1;
        foreach($blocks as $block) {
            if($block->isViewable()) {
                $output .= '
<td style="vertical-align: top;">
<table style="width: 100%;"><tbody>
<tr class="title" >
    <td colspan="2">';
                $output .= _G($block->lname).'
    </td>
</tr>';
                foreach($block->blockrefs as $blockref) {
                    if($blockref->isActive()) {
                        $refname = $blockref->keyname;
                        $output .= '
<tr>
    <td style="vertical-align: top; text-align: right;"><b>'._G($blockref->lname).':</b></td>
    <td>';
                        $output .= '<input type="checkbox" name="display_'.$blockref->id.'">';
                        $output .= '
    </td>
</tr>';
                    }
                }
            }
            $output .= '
</tbody></table>
</td>';
            if($col_nb==$showfields_colsnb) {
                $output .= '
</tr><tr>';
                $col_nb = 0;
            }
            $col_nb++;
        }
        if($col_nb!=1) {
        while($col_nb!=($showfields_colsnb+1)) {
            $output .= '<td>&nbsp;</td>';
            $col_nb++;
        }
        }
        $output .= '
</tr>
</tbody></table>
</div>';
        return $output;
    }

    public function htmlParamProcess() {
        global $app;
        $app->tpl_display_columns = array();
        $blocks = MySBDBMFBlockHelper::load();
        foreach($blocks as $block) {
            if($block->isViewable()) {
                foreach($block->blockrefs as $blockref) {
                    if($blockref->isActive() and $_POST['display_'.$blockref->id]=='on') {
                        $app->tpl_display_columns[] = $blockref;
                    }
                }
            }
        }
    }

    public function requestOrderBy() {
        return $_POST["dbmf_exportdisplay_orderby$this->id"];
    }

    private function keyname2red($strdb) {
        if(strlen($strdb)<7) return $strdb;
        $newstr = '';
        $words = explode(' ',$strdb);
        foreach($words as $word) {
            if(strlen($word)>3) {
                $newstr .= $word[0];
                $newstr .= $word[1];
                $newstr .= $word[2];
                $newstr .= '. ';
            } else $newstr .= $word.' ';
        }
        return $newstr;
    }

    private function db2csv($strdb) {
        $str = str_replace( "\r\n", ' - ', $strdb );
        $str = str_replace( "\n", ' - ', $str );
        return $str;
    }

    /**
     * Search result output
     * @param   
     */
    public function htmlResultOutput($results) {
        global $app;
        echo '
<p>
CSV output: '.MySBDB::num_rows($results).' results<br>
</p>
';
/*
        $app->tpl_dbmf_searchresult = $results;
        _T('templates/contacts_display.php','dbmf3');
*/

        $csv_char = ';';
        $path_file = MySB_ROOTPATH.'/log/sendtable.csv';
        $ftable = fopen($path_file, 'w');

        $titles = '"Name"'.$csv_char.'"Firstname"';
        $blockrefs = MySBDBMFBlockRefHelper::load();
        foreach($blockrefs as $blockref) {
            $titles .= $csv_char.$this->keyname2red(_G($blockref->lname));
        }
        fwrite($ftable,$titles."\n");
        while($contact_data=MySBDB::fetch_array($results)) {
            $contact = new MySBDBMFContact(null,$contact_data);
            $contactline = $contact->lastname.$csv_char.$contact->firstname;
            foreach($blockrefs as $blockref) {
                $contactline .= $csv_char.$this->db2csv(_G($contact_data[$blockref->keyname]));
            }
            fwrite($ftable,$contactline."\n");
        }
        fclose($ftable);
        $stmail = new MySBMail('mail_sendtable','dbmf3');
        $stmail->addTO($app->auth_user->mail,$app->auth_user->firstname.' '.$app->auth_user->lastname);
        $stmail->addAttachment($path_file);
        $stmail->send();
        unlink($path_file);

    }

/*
    $event = new MySBRSVPEvent($_POST['sendtable_event']);
    $aprice = $event->loadPrices();
    $evdate = new MySBDateTime($event->date);
    $titles = '"'.$event->title.'"'.$csv_char.'"'.$evdate->strAEBY_l().'"'.$csv_char;
    fwrite($ftable,$titles."\n");
    $titles = '"Name"'.$csv_char.'"Mail"'.$csv_char.'"Infos"'.$csv_char.'"Number"'.$csv_char;
    foreach($aprice as $price) {
        $titles .= '"'.$price['name'].'"'.$csv_char;
    }
    fwrite($ftable,$titles."\n");
    while($data_sticket = MySBDB::fetch_array($req_stickets)) {
        $prices_str = new MySBCSValues($data_sticket['purchase']);
        $purchase_str = '';
        foreach($aprice as $price) {
            $i = 0;
            $pflag = 0;
            while(isset($prices_str->values[$i])) {
                if($price['active']==1 and $price['id']==$prices_str->values[$i]) {
                    $purchase_str .= '"'.$prices_str->values[$i+1].'"'.$csv_char;
                    $pflag = 1;
                }
                $i += 2;
            }
            if($pflag==0)  $purchase_str .= '"00"'.$csv_char;
        }
        //$price_total = $ticket->getPriceTotal($event);
        $req_user = MySBDB::query("SELECT mail from ".MySB_DBPREFIX.'users '.
            'WHERE id='.$data_sticket['user_id'],
            "boardvalid_process.php",
            false, 'rsvp');
        $data_user = MySBDB::fetch_array($req_user);
        $ticketinfos = '"'.$data_sticket['ref_name'].'"'.$csv_char.'"'.$data_user['mail'].'"'.$csv_char.'"'.$data_sticket['ref_comment'].'"'.$csv_char.'"'.$data_sticket['ticket_num'].'"'.$csv_char.''.$purchase_str.''."\n";
        fwrite($ftable,$ticketinfos);
    }
    fclose($ftable);
    $pomail = new MySBMail('mail_sendtable','rsvp');
    $pomail->addTO($app->auth_user->mail,$app->auth_user->firstname.' '.$app->auth_user->lastname);
    $pomail->data['title'] = $event->title;
    $pomail->data['date'] = $evdate->strAEBY_l();
    $pomail->addAttachment($path_file);
    $pomail->send();
    unlink($path_file);
    }
*/

}

?>
