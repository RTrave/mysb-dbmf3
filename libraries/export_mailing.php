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
        MySBEditor::activate();
$output = MySBEditor::initCode().'
<p>
    '._G('DBMF_exportmailing_firstid').':
    <input type="text" name="dbmf_exportmailing_firstid" value="" size="8"><br>
    '._G('DBMF_exportmailing_subject').':
    <input type="text" name="dbmf_exportmailing_subject" value="" size="24"><br>
    '._G('DBMF_exportmailing_body').':<br>
    <textarea name="dbmf_exportmailing_body" cols="60" rows="8" class="mceEditor"></textarea><br>
    '._G('DBMF_exportmailing_attachment').' 1:
    <input type="hidden" name="MAX_FILE_SIZE" value="2000000" />
    <input name="dbmf_exportmailing_att1" type="file" /><br>
    '._G('DBMF_exportmailing_attachment').' 2:
    <input type="hidden" name="MAX_FILE_SIZE" value="2000000" />
    <input name="dbmf_exportmailing_att2" type="file" /><br>
    '._G('DBMF_exportmailing_attachment').' 3:
    <input type="hidden" name="MAX_FILE_SIZE" value="2000000" />
    <input name="dbmf_exportmailing_att3" type="file" />
</p>';
        return $output;
    }

    public function htmlParamProcess() {
        global $app;
        $this->mailing_firstid = $_POST['dbmf_exportmailing_firstid'];
        $this->mailing_subject = $_POST['dbmf_exportmailing_subject'];
        $this->mailing_body = $_POST['dbmf_exportmailing_body'];
        $uploaddir = MySB_ROOTPATH.'/modules/dbmf3/files/';
        if( !empty($_FILES['dbmf_exportmailing_att1']['name']) and 
            !empty($_FILES['dbmf_exportmailing_att1']['tmp_name']) ) {
            move_uploaded_file($_FILES['dbmf_exportmailing_att1']['tmp_name'],$uploaddir.$_FILES['dbmf_exportmailing_att1']['name']);
            $this->mailing_att1 = $uploaddir.$_FILES['dbmf_exportmailing_att1']['name'];
        }
        if( !empty($_FILES['dbmf_exportmailing_att2']['name']) and 
            !empty($_FILES['dbmf_exportmailing_att2']['tmp_name']) ) {
            move_uploaded_file($_FILES['dbmf_exportmailing_att2']['tmp_name'],$uploaddir.$_FILES['dbmf_exportmailing_att2']['name']);
            $this->mailing_att2 = $uploaddir.$_FILES['dbmf_exportmailing_att2']['name'];
        }
        if( !empty($_FILES['dbmf_exportmailing_att3']['name']) and 
            !empty($_FILES['dbmf_exportmailing_att3']['tmp_name']) ) {
            move_uploaded_file($_FILES['dbmf_exportmailing_att3']['tmp_name'],$uploaddir.$_FILES['dbmf_exportmailing_att3']['name']);
            $this->mailing_att3 = $uploaddir.$_FILES['dbmf_exportmailing_att3']['name'];
        }
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
<div id="rsvp_mailing_displaymail">
<p><b>'.$this->mailing_subject.'</b></p>
<br>
'.MySBUtil::str2html($this->mailing_body).'
<br>
</div>
<p>';
        if( $this->mailing_firstid!='' )
            $recup_flag = true;
        else 
             $recup_flag = false;
        $modulo_index = 0;
        $firstid = 0;
        while($data_result = MySBDB::fetch_array($results)) {

            $contact = new MySBDBMFContact(null,$data_result);

            if( $recup_flag==true and $contact->id!=$this->mailing_firstid )
                continue;
            if( $recup_flag==true and $contact->id==$this->mailing_firstid )
                $recup_flag = false;

            if($this->config_array['modulo']!='' and $modulo_index>=$this->config_array['modulo']) {
                $modulo_index = 0;
                if( !$current_mail->send() ) {
                    echo $current_mail->getError().'<br>';
                    echo 'Last ID tried: <b>'.$firstid.'</b><br>';
                    unset($current_mail);
                    return;
                }
                unset($current_mail);
                $output .= _G('DBMF_exportmailing_sendingnew')."!\n<br>";
            }

            if($contact->b1r08!='') {
                 $modulo_index++;
                if(!isset($current_mail)) {
                    $firstid = $contact->id;
                    $current_mail = new MySBMail('mail_mailing','dbmf3');
                    $current_mail->unset_footer();
                    $current_mail->data['body'] = $this->mailing_body;
                    $current_mail->data['subject'] = $this->mailing_subject;
                    if($this->mailing_att1!='') $current_mail->addAttachment($this->mailing_att1);
                    if($this->mailing_att2!='') $current_mail->addAttachment($this->mailing_att2);
                    if($this->mailing_att3!='') $current_mail->addAttachment($this->mailing_att3);
                }
                 $current_mail->addBCC($contact->b1r08,$contact->firstname.' '.$contact->lastname);
            } else {
                $output .= _G('DBMF_exportmailing_sendingnomail').': '.$contact->firstname.' '.$contact->lastname.' (id:'.$contact->id.')<br>';
            }

        }
        if(isset($current_mail)) 
            if( !$current_mail->send() ) {
                echo $current_mail->getError().'<br>';
                echo 'Last ID tried: <b>'.$firstid.'</b><br>';
            }
        $output .= _G('DBMF_exportmailing_sendinglast')."!\n<br>";
        $output .= '
</p>';
        if($this->mailing_att1!='') unlink($this->mailing_att1);
        if($this->mailing_att2!='') unlink($this->mailing_att2);
        if($this->mailing_att3!='') unlink($this->mailing_att3);
        return $output;
    }

}

?>
