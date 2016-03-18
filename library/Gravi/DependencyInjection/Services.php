<?php

/**
 * Class Services
 *
 * The service manager
 *
 * This class has an instance of the Dependency Injector
 * and will handle the instances of the called services
 *
 */
class Services {

    /**
     * DependencyInjection Static Object
     *
     * @var DependencyInjection
     */
    private static $services;

    /**
     * Static initialization
     */
    public static function init() {

        self::$services = new DependencyInjection();
    }

    /**
     * Retrieves a service instance
     *
     * @param   string      $service        - The service name
     * @return  mixed
     */
    public static function get($service) {

        return self::$services->get($service);
    }

}