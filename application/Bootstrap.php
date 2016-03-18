<?php

/**
 * Class Bootstrap
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    const GraviDir = '../library/Gravi';

    protected function _initLayout(){
        $layout = Zend_Layout::startMvc();
        Zend_Controller_Front::getInstance()->registerPlugin(new Module_LayoutPlugin());

        $http        = new Zend_Controller_Request_Http();
        $viewSession = $http->getCookie('viewsite');

        if (empty($viewSession)) {
            Gravi_Service_ImochatService::SaveSiteView();
            setcookie('viewsite', 'true', time() + (60 * 60), '/');
        }
    }

    /**
     * Initializes the dependency injector
     */
    protected function _initDependencyInjection() {

        require_once self::GraviDir . '/DependencyInjection/DependencyInjection.php';
        require_once self::GraviDir . '/DependencyInjection/Services.php';
        require_once self::GraviDir . '/String/String.php';
        Services::init();
    }

    protected function _initChatService(){
        $imochatService = new Gravi_Service_Imochat();
        $imochatService->auth();
    }

}

class Module_LayoutPlugin extends Zend_Controller_Plugin_Abstract{

    public function preDispatch(Zend_Controller_Request_Abstract $request){
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayoutPath(APPLICATION_PATH . '/modules/'.$request->getModuleName().'/views/layouts');
    }
}