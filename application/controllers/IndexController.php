<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */

        error_reporting(E_NOTICE);
        ini_set('display_errors', 0 );

    }

    public function indexAction()
    {
        // action body
    }


}

