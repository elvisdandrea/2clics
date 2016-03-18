<?php

class Gravi_Service_Imochat{

    const CFG_FILE = 'config.json';

    private $session;

    private $config;

    public function __construct() {

        $this->restClient = new Gravi_RestClient();
        $this->restClient->addHeader('Accept', 'application/json');
        $this->restClient->setFormat('json');
        $this->loadConfig();

        $this->validateToken();
    }

    private function loadConfig() {

        $configFile = __DIR__ . '/' . self::CFG_FILE;
        if (!is_file($configFile)) return false;

        $content = file_get_contents($configFile);
        $config  = json_decode($content, true);

        $this->config = $config;

    }

    public function validateToken() {

        $session = new Zend_Session_Namespace('imochat_token');
        $this->session = $session;

        if(empty($session->data)){
            return $this->auth();
        }

        if(!isset($this->session->data['access_token']) || !$this->checkToken($this->session->data['access_token'])){
            $this->authRequest($this->config['id'], $this->config['secret']);
            $this->session->data = $this->restClient->getResponse();
        }

    }

    public function auth() {

        $session = new Zend_Session_Namespace('imochat_token');
        $this->session = $session;

        if(empty($session->data)){
            $this->authRequest($this->config['id'], $this->config['secret']);

            $session->data = $this->restClient->getResponse();
            return false;
        }

        return true;
    }

    public function saveSearch($filters) {

        $acceptedParams = array(
            //'client_id',
            'Categoria' => 'tipo',
            //'cidade',
            'Bairro' => 'bairros',
            'Dormitorios' => 'dormitorios',
            'Vagas' => 'vagas',
            'BanheiroSocialQtd' => 'banheiros',
        );

        $session = new Zend_Session_Namespace('imochat_token');

        $token = $session->data['access_token'];

        $this->restClient->setUrl($this->config['url']);
        $this->restClient->setUri('/rest/savesearch?token=' . $token);
        $this->restClient->setMethod('post');

        $http     = new Zend_Controller_Request_Http();
        $ip       = $http->getServer('REMOTE_ADDR');
        $location = Gravi_Geolocation::getVisitorLocation();

        $this->restClient->addParam('sender_ip', $ip);

        foreach (array(
                     'city'      => 'sender_city',
                     'region'    => 'sender_region',
                     'lat'       => 'sender_lat',
                     'lon'       => 'sender_lng',
                     'isp'       => 'sender_isp'
                 ) as $locationField => $destination)
            !isset($location[$locationField]) || $this->restClient->addParam($destination, $location[$locationField]);



        foreach($filters as $key => $filter){
            if(!empty($filter) && array_key_exists($key, $acceptedParams)) {
                if (is_array($filter))
                    $this->restClient->addParam($acceptedParams[$key], implode(',', $filter));
                else
                    $this->restClient->addParam($acceptedParams[$key], $filter);
            }
        }

        if(isset($filters['ValorVenda'])){
            $this->restClient->addParam('valor_min', $filters['ValorVenda'][0]);
            $this->restClient->addParam('valor_max', $filters['ValorVenda'][1]);
        }

        $this->restClient->execute();
    }

    public function saveSiteView() {
        $session = new Zend_Session_Namespace('imochat_token');

        $token = $session->data['access_token'];

        $this->restClient->setUrl($this->config['url']);
        $this->restClient->setUri('/rest/newsiteview?token=' . $token);
        $this->restClient->setMethod('post');

        $http     = new Zend_Controller_Request_Http();
        $ip       = $http->getServer('REMOTE_ADDR');
        $location = Gravi_Geolocation::getVisitorLocation();

        $this->restClient->addParam('ip', $ip);

        foreach (array(
                    'city'      => 'city',
                    'region'    => 'region',
                    'lat'       => 'lat',
                    'lon'       => 'lng',
                    'isp'       => 'isp'
                 ) as $locationField => $destination)
            !isset($location[$locationField]) || $this->restClient->addParam($destination, $location[$locationField]);

        $this->restClient->execute();

    }

    public function savePropertyView($id) {
        $session = new Zend_Session_Namespace('imochat_token');

        $token = $session->data['access_token'];

        $this->restClient->setUrl($this->config['url']);
        $this->restClient->setUri('/rest/newpropertyview?token=' . $token);
        $this->restClient->addParam('property_id', $id);
        $this->restClient->setMethod('post');

        $http     = new Zend_Controller_Request_Http();
        $ip       = $http->getServer('REMOTE_ADDR');
        $location = Gravi_Geolocation::getVisitorLocation();

        $this->restClient->addParam('ip', $ip);

        foreach (array(
                     'city'      => 'city',
                     'region'    => 'region',
                     'lat'       => 'lat',
                     'lon'       => 'lng',
                     'isp'       => 'isp'
                 ) as $locationField => $destination)
            !isset($location[$locationField]) || $this->restClient->addParam($destination, $location[$locationField]);

        $this->restClient->execute();

    }

    protected function authRequest($id, $secret){

        $this->restClient->setUrl($this->config['url']);
        $this->restClient->setUri('/rest/auth');
        $this->restClient->addParam('id', $id);
        $this->restClient->addParam('secret', $secret);

        $this->restClient->execute();
    }

    protected function checkToken($token){

        $this->restClient->setUrl($this->config['url']);
        $this->restClient->setUri('/rest/checktoken');
        $this->restClient->addParam('token', $token);

        $this->restClient->execute();
        $response = $this->restClient->getResponse();

        return $response['status'] == 200;
    }

    public function getBanners() {

        $session = new Zend_Session_Namespace('imochat_token');
        $token = $session->data['access_token'];

        $this->restClient->setUrl($this->config['url']);
        $this->restClient->setUri('/rest/banners');
        $this->restClient->setMethod('get');

        $this->restClient->execute();
        return $this->restClient->getResponse();
    }

    public function getSiteConfig() {

        $session = new Zend_Session_Namespace('imochat_token');
        $token = $session->data['access_token'];

        $this->restClient->setUrl($this->config['url']);
        $this->restClient->setUri('/rest/siteconfig');
        $this->restClient->setMethod('get');

        $this->restClient->execute();
        return $this->restClient->getResponse();
    }

    public function getBairros($id = false) {

        $session = new Zend_Session_Namespace('imochat_token');
        $token = $session->data['access_token'];

        $this->restClient->setUrl($this->config['url']);
        $this->restClient->setUri('/rest/bairros');
        $this->restClient->setMethod('get');

        if ($id) $this->restClient->addParam('id', $id);

        $this->restClient->execute();

        return $this->restClient->getResponse();
    }

    public function getUsers($id = false) {

        $session = new Zend_Session_Namespace('imochat_token');
        $token = $session->data['access_token'];

        $this->restClient->setUrl($this->config['url']);
        $this->restClient->setUri('/rest/users');
        $this->restClient->setMethod('get');

        if ($id) $this->restClient->addParam('id', $id);

        $this->restClient->execute();

        return $this->restClient->getResponse();
    }

    public function saveSiteContact($params) {
        $session = new Zend_Session_Namespace('imochat_token');

        $token = $session->data['access_token'];

        $this->restClient->setUrl($this->config['url']);
        $this->restClient->setUri('/rest/newsitecontact?token=' . $token);
        $this->restClient->setMethod('post');

        foreach ($params as $param => $value) {
            $this->restClient->addParam($param, $value);
        }

        $this->restClient->execute();

    }

    public function saveSiteOffer($params) {
        $session = new Zend_Session_Namespace('imochat_token');

        $token = $session->data['access_token'];

        $this->restClient->setUrl($this->config['url']);
        $this->restClient->setUri('/rest/newsiteoffer?token=' . $token);
        $this->restClient->setMethod('post');

        foreach ($params as $param => $value) {
            $this->restClient->addParam($param, $value);
        }

        $this->restClient->execute();

    }

}
