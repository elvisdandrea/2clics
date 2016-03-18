<?php

/**
 * Class DependencyInjection
 */
class DependencyInjection {

    /**
     * File that contains the services list
     */
    const ServicesFile = '/services.json';

    /**
     * Stored Services
     *
     * @var array
     */
    private $services    = array();

    /**
     * The services list names
     *
     * @var array
     */
    private $serviceList = array();

    public function __construct() {

        $this->loadServicesList();
    }

    /**
     * Loads the services names and locations
     */
    private function loadServicesList() {

        // Services file missing, program runs but with no services
        if (!file_exists(__DIR__ . self::ServicesFile)) return;

        $content = file_get_contents(__DIR__ . self::ServicesFile);

        // Services file broken, program runs but with no services
        if (!$content) return;

        $this->serviceList = json_decode($content, true);

    }

    /**
     * Returns an instance of a service
     *
     * @param   string      $service            - The service name
     * @param   array       $parameters         - The service parameters
     * @return  mixed
     * @throws  Zend_Exception
     */
    public function get($service, $parameters = array()) {

        if (!isset($this->serviceList[$service]))
            throw new Zend_Exception('Service ' . $service . ' not registered');

        if (isset($this->services[$service]))
                return $this->services[$service];

        if (isset($this->serviceList[$service]['requirements']))
            foreach($this->serviceList[$service]['requirements'] as $requirement) {
                $reqfile = __DIR__ . '/../' . $requirement . '.php';
                if (!file_exists($reqfile))
                    throw new Zend_Exception('Service ' . $service . ' requires ' . $requirement . ' but was not found.');

                require_once $reqfile;
            }

        $file = $this->serviceList[$service]['classfile'];
        $classFile = __DIR__ . '/../' . $file . '.php';

        if (!file_exists($classFile))
            throw new Zend_Exception('Service ' . $service . ' not registered');

        require_once $classFile;

        $classname = basename($file);
        $this->services[$service] = new $classname();

        return $this->services[$service];
    }


}