<?php
namespace Apix\Plugin\Auth;

interface Adapter
{

    /**
     * Performs an authentication attempt
     *
     * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
     * @return boolean
     */
    public function authenticate();

}