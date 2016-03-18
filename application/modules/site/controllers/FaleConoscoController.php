<?php

class FaleConoscoController extends Zend_Controller_Action{

    public function indexAction(){
        $vista = Services::get('vista_rest');
        $this->view->listas = $vista->getListasBusca();

        $form = new Site_Form_FaleConoscoForm();
        $this->view->form = $form;
        $this->view->bodyClass = 'pg-interna';
        $params = $this->_request->getParams();
        $this->view->headScript()->appendFile($this->view->serverUrl().BASEDIR.'/res/js/faleconosco.js');

        if($this->_request->isPost()){
            try{
                $vista = Services::get('vista_rest');
                $vista->getAuthEmail();
                $smtpData = $vista->getResult();

                $config = array('auth' => 'login',
                    'username' => $smtpData['user'],
                    'password' => $smtpData['pass'],
                    'port' => $smtpData['port']
                );

                $transport = new Zend_Mail_Transport_Smtp($smtpData['smtp'], $config);
                Zend_Mail::setDefaultTransport($transport);

                $html = new Zend_View();
                $html->setScriptPath(APPLICATION_PATH . '/modules/site/views/scripts/fale-conosco/');

                $html->data = $params;

                $emailBody = $html->render('email-body.phtml');

                $mail = new Zend_Mail();

                $configData = Gravi_Service_ImochatService::getSiteConfig();
                $config = $configData['config'];

                $mail->setBodyHtml($emailBody);
                $mail->setFrom($config['contact_email'], $params['nome']);
                $mail->addTo($config['contact_email'], 'Terrasul');

                //  Teste Local
//                $mail->addTo('elvis@gravi.com.br', 'Terrasul');

                $subjects = array(
                    'contato'   => 'SITE TERRASUL - FORMULÁRIO DE CONTATO',
                    'interesse' => 'SITE TERRASUL - INTERESSE EM IMÓVEL',
                    'oferta'    => 'SITE TERRASUL - OFERTA',
                    'ligamos'   => 'SITE TERRASUL - LIGAMOS PARA VOCÊ'
                );

                $subject = 'contato';

                if (isset($params['subject']) && isset($subjects[$params['subject']])) $subject = $params['subject'];

                if (isset($params['curriculo'])) {

                    $filename = $params['curriculo-name'];
                    $ext      = pathinfo($filename, PATHINFO_EXTENSION);
                    $allowed  = array('doc', 'docx', 'pdf', 'xls', 'xlsx', 'odt', 'zip', 'rar');

                    if (!in_array($ext, $allowed)) {
                        $this->_helper->layout()->disableLayout();
                        $this->_helper->viewRenderer->setNoRender(true);
                        echo 'O currículo enviado está em um formato não aceito!';
                        return;
                    }

                    $file     = explode(',', $params['curriculo']);
                    $base64   = $file[1];
                    $data     = explode(':', $file[0]);
                    $mime     = str_replace(';base64', '', $data[1]);

                    $at = $mail->createAttachment(base64_decode($base64));
                    $at->type        = $mime;
                    $at->disposition = Zend_Mime::DISPOSITION_INLINE;
                    $at->encoding    = Zend_Mime::ENCODING_BASE64;
                    $at->filename    = $filename;

                }

                $location = Gravi_Geolocation::getVisitorLocation();

                $contactData = array(
                    'client_name' => $params['nome'],
                    'email'       => $params['email'],
                    'phone'       => $params['fone'],
                    'message'     => $params['mensagem']
                );

                foreach (array(
                             'city'     => 'city',
                             'region'   => 'region',
                             'lat'      => 'lat',
                             'lon'      => 'lng',
                             'isp'      => 'isp',
                             'query'    => 'ip'
                         ) as $info => $dest)

                    !isset($location[$info]) || $contactData[$dest] = $location[$info];

                if (in_array($params['subject'], array('interesse', 'oferta'))) {
                    $contactData['property_id'] = $params['imovel'];
                    Gravi_Service_ImochatService::SaveSiteOffer($contactData);
                } else {
                    Gravi_Service_ImochatService::SaveSiteContact($contactData);
                }

                $mail->setSubject($subjects[$subject]);

                $mail->send();

                $this->view->success = true;

                if ($this->_request->isXmlHttpRequest()) {
                    $this->_helper->layout()->disableLayout();
                    $this->_helper->viewRenderer->setNoRender(true);
                    echo 'Sua mensagem foi enviada. Obrigado!';
                    return;
                }

            } catch(Exception $e){
                print_r($e->getMessage());exit;
            }
        }
    }
}