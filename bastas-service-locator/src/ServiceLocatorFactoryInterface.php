<?php

namespace Bastas\ServiceLocator;

/**
 * Interface ServiceLocatorFactoryInterface
 * @package Bastas\ServiceLocator
 */
interface ServiceLocatorFactoryInterface
{
    /**
     * @param ServiceLocator $sl
     * @return mixed
     */
    public function createService(ServiceLocator $sl);
}