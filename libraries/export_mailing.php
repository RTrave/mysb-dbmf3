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
 * DBMF Mailing Export class
 * 
 */
class MySBDBMFExportMailing extends MySBDBMFExport {

    public function __construct($id=-1, $data_export = array()) {
        global $app;
        parent::__construct($id,(array) ($data_export));
    }

    public function htmlConfigForm() {
        if($this->config_array['modulo']!='') $modulo = $this->config_array['modulo'];
        else $modulo = 300;
        $str_res = '
'._G('DBMF_exportmailing_config_modulo').': 
    <input type="text" name="dbmf_exportmailing_config_modulo" value="'.$modulo.'"><br>';
        return $str_res;
    }

    public function htmlConfigProcess() {
        global $_POST;
        $str_res = 
            'modulo='.$_POST['dbmf_exportmailing_config_modulo'].';';
        return $str_res;
    }

    public function selectionProcess( $selection ) {
        
    }

    public function htmlParamForm() {
        $output = '
<p>'._G('DBMF_exportmailing_subject').':
    <input type="text" name="dbmf_exportmailing_subject" value="" size="24"><br>
    '._G('DBMF_exportmailing_body').':<br>
    <textarea name="dbmf_exportmailing_body" cols="60" rows="8"></textarea>
</p>';
        return $output;
    }

    public function htmlParamProcess() {
        global $app;
        $this->mailing_subject = $_POST['dbmf_exportmailing_subject'];
        $this->mailing_body = $_POST['dbmf_exportmailing_body'];
    }

    /**
     * Search result output
     * @param   
     */
    public function htmlResultOutput($results) {
        global $app;
        if( $this->mailing_subject=='' or $this->mailing_body=='' ) {
            echo '<p>'._G('DBMF_exportmailing_emptyfield').'</p>';
            return;
        }
        $output = '
<p>
'.MySBDB::num_rows($results).' results<br>
</p>
<h3>'._G('DBMF_exportmailing_sending').'</h3>
<p>
<b>'.$this->mailing_subject.'</b><br>
'.$this->mailing_body.'
<br>
<br>
';
        $modulo_index = 0;
        while($data_result = MySBDB::fetch_array($results)) {

            if($this->config_array['modulo']!='' and $modulo_index>=$this->config_array['modulo']) {
                $modulo_index = 0;
                $current_mail->send();
                unset($current_mail);
                $output .= _G('DBMF_exportmailing_sendingnew')."!\n<br>";
            }
            $contact = new MySBDBMFContact(null,$data_result);
            if($contact->b1r08!='') {
                 $modulo_index++;
                if(!isset($current_mail)) {
                    $current_mail = new MySBMail('mail_mailing','dbmf3');
                    $current_mail->unset_footer();
                    $current_mail->data['body'] = $this->mailing_body;
                    $current_mail->data['subject'] = $this->mailing_subject;
                }
                 $current_mail->addBCC($contact->b1r08,$contact->firstname.' '.$contact->lastname);
            } else {
                $output .= _G('DBMF_exportmailing_sendingnomail').': '.$contact->firstname.' '.$contact->lastname.' (id:'.$contact->id.')<br>';
            }

        }
        if(isset($current_mail)) 
            $current_mail->send();
        $output .= _G('DBMF_exportmailing_sendinglast')."!\n<br>";
        $output .= '
</p>';
        return $output;
    }

}

?>
