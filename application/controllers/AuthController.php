<?php

class AuthController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
         $msg='';
    }

    public function indexAction()
    {
        // action body
        $msg='';
    }

    public function loginAction()
    {
        // action body
        $msg='';
        if( !$this->_request->isPost() )
            return false;
        $usuario = $_POST['user'];
        $senhamd5=md5($_POST['password']);
        $senha=md5($_POST['password']);
        //$data = $this->getRequest()->getPost();
       // $this->view->form = $data;
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
         //Dados do banco a serem referenciados
        $authAdapter->setTableName('usuario');
        $authAdapter->setIdentityColumn('user');
        $authAdapter->setCredentialColumn('password');
         //Dados do formulario a serem referenciados
        $authAdapter->setIdentity($usuario);
        $authAdapter->setCredential($senha);
        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($authAdapter);
        if ($result->isValid()) 
        {
            $info = $authAdapter->getResultRowObject(null, 'password');
            $storage = $auth->getStorage();
            $storage->write($info);
            $this->_redirect('/menu');
        }else
        {
            $auth = Zend_Auth::getInstance();
            $auth->clearIdentity();
            Zend_Auth::getInstance()->clearIdentity();
            $msg='Usuário ou senha incorretos';
            $this->_redirect('/?msg=' . $msg);
        }
    }

    public function logoutAction()
    {
        // action body
    $auth = Zend_Auth::getInstance();
    $auth->clearIdentity();
    Zend_Auth::getInstance()->clearIdentity();
    $this->_redirect('/');
  
    }


}





