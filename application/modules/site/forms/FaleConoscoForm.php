<?php

class Site_Form_FaleConoscoForm extends Zend_Form{

    protected $_errorMessages = array(
        'isEmpy' => 'Este campo é obrigatório',
        'digits' => 'Digite apenas numeros',
        'emailValidator' => 'E-mail inválido',
        'foneValidator' => 'Este campo deve possuir pelo menos 8 digitos',
        'dddValidator' => 'Este campo deve possuir pelo menos 2 digitos'
    );

    public function __construct(){
        // Validators --------------------------
        $notEmpty = new Zend_Validate_NotEmpty(array(true));
        $notEmpty->setMessage($this->_errorMessages['isEmpy']);

        $digits = new Zend_Validate_Digits();
        $digits->setMessage($this->_errorMessages['digits']);

        $emailValidator = new Zend_Validate_EmailAddress();
        $emailValidator->setMessage($this->_errorMessages['emailValidator']);

        $foneValidator = new Zend_Validate_StringLength();
        $foneValidator->setMin(8);
        $foneValidator->setMessage($this->_errorMessages['foneValidator']);

        $dddValidator = new Zend_Validate_StringLength();
        $dddValidator->setMin(2);
        $dddValidator->setMessage($this->_errorMessages['foneValidator']);

        //--------------------------------------

        $nome = new Zend_Form_Element_Text('nome');
        $nome->setAttrib('class', 'form-control');
        $nome->setAttrib('required', true);
        $nome->setAttrib('accesskey', 'n');
        $nome->setRequired(true);
        $nome->addValidator($notEmpty, true);
        //--------------------------------------------------------
        $fone = new Zend_Form_Element_Text('fone');
        $fone->setAttrib('class', 'form-control');
        $fone->setAttrib('required', true);
        $fone->setAttrib('accesskey', 'f');
        $fone->setRequired(true);
        $fone->addValidator($notEmpty, true);
        $fone->addValidator($digits, true);
        $fone->addValidator($foneValidator, true);
        //--------------------------------------------------------
        $ddd = new Zend_Form_Element_Text('ddd');
        $ddd->setAttrib('class', 'form-control');
        $ddd->setAttrib('required', true);
        $ddd->setAttrib('accesskey', 'd');
        $ddd->setRequired(true);
        $ddd->addValidator($notEmpty, true);
        $ddd->addValidator($digits, true);
        $ddd->addValidator($dddValidator, true);
        //--------------------------------------------------------
        $email = new Zend_Form_Element_Text('email');
        $email->setAttrib('class', 'form-control');
        $email->setAttrib('required', true);
        $email->setAttrib('accesskey', 'e');
        $email->setRequired(true);
        $email->addValidator($notEmpty, true);
        $email->addValidator($emailValidator, true);
        //--------------------------------------------------------
        $mensagem = new Zend_Form_Element_Textarea('mensagem');
        $mensagem->setAttrib('class', 'form-control');
        $mensagem->setAttrib('required', true);
        $mensagem->setAttrib('accesskey', 'm');
        $mensagem->setAttrib('cols', 1);
        $mensagem->setAttrib('rows', 1);
        $mensagem->setRequired(true);
        $mensagem->addValidator($notEmpty, true);
        //--------------------------------------------------------
        $this->addElement($nome);
        $this->addElement($fone);
        $this->addElement($ddd);
        $this->addElement($email);
        $this->addElement($mensagem);

        $this->setElementDecorators(array(
            'ViewHelper',
            'Errors',
        ));
    }
}