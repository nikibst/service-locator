<?php

namespace Bastas\ServiceLocator;


use Bastas\ServiceLocator\Exception\ServiceLocatorException;

/**
 * Class ServiceLocator
 * @package Bastas\ServiceLocator
 */
final class ServiceLocator
{
    /**
     * @var array
     */
    private static $services = [];

    /**
     * @param string $name
     * @return bool
     */
    private static function serviceExists(string $name) : bool
    {
        return isset(ServiceLocator::$services[$name]);
    }

    /**
     * @param string $name
     * @param string $factoryClassName
     * @throws ServiceLocatorException
     */
    public static function registerService(string $name, string $factoryClassName)
    {
        if (ServiceLocator::serviceExists($name)) {
            throw new ServiceLocatorException("Service with name: '" . $name . "' already exists");
        }

        ServiceLocator::$services[$name] = [];
        ServiceLocator::$services[$name]['class'] = $factoryClassName;
        ServiceLocator::$services[$name]['instance'] = null;
    }

    /**
     * @param string $name
     */
    public static function unregisterService(string $name)
    {
        if (ServiceLocator::serviceExists($name)) {
            unset(ServiceLocator::$services[$name]);
        }
    }

    /**
     * @param string $name
     * @return mixed
     * @throws ServiceLocatorException
     */
    private function instantiateService(string $name)
    {
        $service = new ServiceLocator::$services[$name]['class'];

        if (!$service instanceof ServiceLocatorFactoryInterface) {
            throw new ServiceLocatorException(
                "Service must implement: '" . ServiceLocatorFactoryInterface::class . "'"
            );
        }

        $instance = $service->createService($this);

        if (!is_object($instance)) {
            throw new ServiceLocatorException(
                "A service must always return an object. " . gettype($instance) . " returned instead"
            );
        }

        ServiceLocator::$services[$name]['instance'] = $service->createService($this);

        return ServiceLocator::$services[$name]['instance'];
    }

    /**
     * @param string $name
     * @return mixed
     * @throws ServiceLocatorException
     */
    public function get(string $name)
    {
        if (!ServiceLocator::serviceExists($name)) {
            throw new ServiceLocatorException("There is no registered service with name: '" . $name . "'");
        }

        if (null !== ServiceLocator::$services[$name]['instance']) {
            return ServiceLocator::$services[$name]['instance'];
        }

        return $this->instantiateService($name);
    }

    /**
     * @param string $name
     * @return mixed
     * @throws ServiceLocatorException
     */
    public function getNewInstance(string $name)
    {
        if (!ServiceLocator::serviceExists($name)) {
            throw new ServiceLocatorException("There is no registered service with name: '" . $name . "'");
        }

        return $this->instantiateService($name);
    }
}