<?php

class MenuController extends Zend_Controller_Action
{

    public function init()
    {
        
        
        error_reporting(E_NOTICE);
        ini_set('display_errors', 0 );
        date_default_timezone_set('America/Sao_Paulo');
       
        if (Zend_Auth::getInstance()->hasIdentity()) { 
            $this->view->msg = "Autenticado!";
            $auth = Zend_Auth::getInstance();
            $username = $auth->getIdentity();
            
            $this->view->user = $username->nome;
            $this->view->loguser = $username->user;
            $this->view->userid = $username->id;
            $this->view->usertype = $username->tipo;

        }else {
            $auth = Zend_Auth::getInstance();
            $auth->clearIdentity();
            Zend_Auth::getInstance()->clearIdentity();
            $msg='Usuário ou senha incorretos';
            $this->_redirect('/?msg=' . $msg);
        }
        $config = new Application_Model_DbTable_Configuracoes();
        $resultconfig = $config->fetchAll();
        $this->view->configdata = $resultconfig;


        $user = new Application_Model_DbTable_Usuario();
        $result = $user->fetchAll("status= '1'");
        $this->view->users = $result;

        $tipoocorren = new Application_Model_DbTable_Tipoocorrencia();
        $result02 =$tipoocorren->fetchAll("status= '1'");
        $this->view->datatipoocorrencia = $result02;


        $natureza = new Application_Model_DbTable_Natureza();
        $sl =  $natureza->select()
                       ->where("status= '1'")
                       ->order('descricao ASC');
        $resultnatureza = $natureza->fetchAll($sl);
        $this->view->natureza=$resultnatureza;

        
        //Natureza do tipo clínica
        $naturezatipo1 = new Application_Model_DbTable_Natureza();
        $cond01 = $naturezatipo1->select()
                       ->where("status= '1'")
                       ->where("idtipoocorrencia= '1'")
                       ->order('descricao ASC');//Ordem ascendente
        $resultnaturezatipo1 = $naturezatipo1->fetchAll($cond01);
        $this->view->natureza01=$resultnaturezatipo1;

        //Natureza do tipo traumática
        $naturezatipo2 = new Application_Model_DbTable_Natureza();
        $cond02 = $naturezatipo2->select()
                       ->where("status= '1'")
                       ->where("idtipoocorrencia= '2'")
                       ->order('descricao ASC');//Ordem ascendente
        $resultnaturezatipo2 = $naturezatipo2->fetchAll($cond02);
        $this->view->natureza02=$resultnaturezatipo2;

        $viaturas = new Application_Model_DbTable_Viaturas();
        $resultviaturas = $viaturas->fetchAll("status= '1'");
        $this->view->viaturas =$resultviaturas;

        $origemdestino = new Application_Model_DbTable_Origemdestino();
        $resultod = $origemdestino->fetchAll("status= '1'");
        $this->view->origemdestino=$resultod;


        $bairro = new Application_Model_DbTable_Bairro();
        $resultbairro = $bairro->fetchAll("status= '1'");
        $this->view->bairro=$resultbairro;

        $achado = new Application_Model_DbTable_Achado();
        $resultachado = $achado->fetchAll("status= '1'");
        $this->view->achado=$resultachado;
    }

    public function indexAction()
    {
       
    }

    public function cadastrarusuarioAction()
    {
        
        $user = new Application_Model_DbTable_Usuario();
        date_default_timezone_set('America/Sao_Paulo');
		$horario  =  date('H:i');
		$datahoje =  date('d-m-Y');

		$usuario = $_POST['user'];
		$senha   = md5($_POST['password']);
		$tipo    = $_POST['tipo'];
		$nome    = $_POST['name'];

		$dados = array(
        	            'nome'	   => $nome,
        	            'user'     => $usuario,
        	            'tipo'     => $tipo,
                        'status'   => 1,
        	            'password' => $senha
        	          );
		try {
		  $user->insert($dados);
		} catch (Zend_Exception $e) {
		  $msg=$e->getTace() . $e->getPrevious() ;
		  $this->_redirect('/error?msg=' . $msg);
		}
		
        unset($user);
        //$errors = $this->_getParam('error_handler');
       // $msg = = $errors->exception;
        if ($msg=="") {
        	# code...
        	$msg="Dados salvos com sucesso!";
        }
        $this->_redirect('/menu?msg=' . $msg);
    }

    public function editarusuarioAction()
    {
        $user = new Application_Model_DbTable_Usuario();
        
        $id = 0;
        $id = $_POST['idP'];
        $tipo = $_POST['tipo'];
        $senha = md5($_POST['passwd']);

        $data = array (
            'tipo'=> $tipo,
            'password' => $senha
        );
        $where = $user
                 ->getDefaultAdapter()
                 ->quoteInto('id = ?', $id);

        try {
            $user->update($data, $where);
            $msg = "Usuário editado com sucesso!";
        } catch (Zend_Exception $e) {
            $msg=$e->getTace() . $e->getPrevious() ;
            $this->_redirect('/error?msg=' . $msg);  
        }
        $this->_redirect('/menu?msg=' . $msg);
        
    }

    public function excluirusuarioAction()
    {
        
        $user = new Application_Model_DbTable_Usuario();
        $id = 0;
        $id = $_GET['idPessoa'];
        $data = array (
            'status'=> 0
        );
        // atualizando o email do usuário Diogo Matheus
        try {
            $where = $user
                ->getDefaultAdapter()
                ->quoteInto('id = ?', $id);
            $user->update($data, $where);
            $msg = "Usuário desativado!";
        } catch (Exception $e) {
            $msg=$e->getTace() . $e->getPrevious() ;
            $this->_redirect('/error?msg=' . $msg);
        }
        // redirecionando para lista de usuários
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('/?msg=' . $msg);
    }

    public function alterarsenhaAction()
    {
        $user = new Application_Model_DbTable_Usuario();
        $alias = $_POST['aliasuser'];
        $senha = md5($_POST['senha02']);
        $id    = $_POST['passwd02'];
        $data = array (
            'password'=> $senha
        );
        $where = $user
                 ->getDefaultAdapter()
                 ->quoteInto('id = ?', $id);
        try {
            $user->update($data, $where);
            $msg = "Senha editada com sucesso!";
        } catch (Zend_Exception $e) {
            $msg=$e->getTace() . $e->getPrevious() ;
            $this->_redirect('/error?msg=' . $msg); 
        }
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('/?msg=' . $msg);
    }

    public function cadastroviaturaAction()
    {
        $viatura = new Application_Model_DbTable_Viaturas();
        date_default_timezone_set('America/Sao_Paulo');
        $datahoje =  date('d-m-Y');
        $descricao = strtoupper($_POST['descviatura']);
        $tipo    = $_POST['tipoviatura'];
        $dados = array(
                    'descricao'     => $descricao,
                    'tipo'     => $tipo,
                    'datacad'     => $datahoje,
                    'status'   => 1
                );
        try {
          $viatura->insert($dados);
        } catch (Zend_Exception $e) {
          $msg=$e->getTace() . $e->getPrevious() ;
          $this->_redirect('/error?msg=' . $msg);
        }
        unset($viatura);
        if ($msg=="") {
            $msg="Dados salvos com sucesso!";
        }
        $this->_redirect('/menu?msg=' . $msg);
    }

    public function cadastrotipoocorrenciaAction()
    {
        $tipoocorrencia = new Application_Model_DbTable_Tipoocorrencia();
        date_default_timezone_set('America/Sao_Paulo');
        $datahoje =  date('d-m-Y');
        $descricao = strtoupper($_POST['descocorrencia']);
        $dados = array(
                    'descricao'=> $descricao,
                    'status'   => 1
                );
        try {
            $tipoocorrencia->insert($dados);
        } catch (Zend_Exception $e) {
            $msg=$e->getTace() . $e->getPrevious() ;
            $this->_redirect('/error?msg=' . $msg);
        }
        unset($tipoocorrencia);
        //$errors = $this->_getParam('error_handler');
       // $msg = = $errors->exception;
        if ($msg=="") {
            # code...
            $msg="Dados salvos com sucesso!";
        }
        $this->_redirect('/menu?msg=' . $msg);
    }

    public function cadastroorigemdestinoAction()
    {
        $origemdestino = new Application_Model_DbTable_Origemdestino();
        date_default_timezone_set('America/Sao_Paulo');
        $datahoje =  date('d-m-Y');
        $descricao =strtoupper($_POST['inputorigemdestino']);
        $dados = array(
                        'descricao'=> $descricao,
                        'status'   => 1
                      );
        try {
          $origemdestino->insert($dados);
        } catch (Zend_Exception $e) {
          $msg=$e->getTace() . $e->getPrevious() ;
          $this->_redirect('/error?msg=' . $msg);
        }
        unset($origemdestino);
        //$errors = $this->_getParam('error_handler');
       // $msg = = $errors->exception;
        if ($msg=="") {
            # code...
            $msg="Dados salvos com sucesso!";
        }
        $this->_redirect('/menu?msg=' . $msg);
    }

    public function cadastronaturezaAction()
    {
        
        $natureza = new Application_Model_DbTable_Natureza();
        date_default_timezone_set('America/Sao_Paulo');
        $datahoje =  date('d-m-Y');
        $descricao =strtoupper($_POST['inputnatureza']);
        $idto = $_POST['descnatureza'];
        $dados = array(
                        'descricao'=> $descricao,
                        'idtipoocorrencia'=>$idto,
                        'status'   => 1
                      );
        try {
          $natureza->insert($dados);
        } catch (Zend_Exception $e) {
          $msg=$e->getTace() . $e->getPrevious() ;
          $this->_redirect('/error?msg=' . $msg);
        }
        unset($natureza);
        //$errors = $this->_getParam('error_handler');
       // $msg = = $errors->exception;
        if ($msg=="") {
            # code...
            $msg="Dados salvos com sucesso!";
        }
        $this->_redirect('/menu?msg=' . $msg);
    }

    public function abrirocorrenciaAction()
    {
        
    }

    public function salvarocorrenciaAction()
    {
        
    }

    public function cadastrobairroAction()
    {
        
        $bairro = new Application_Model_DbTable_Bairro();
        date_default_timezone_set('America/Sao_Paulo');
        $datahoje =  date('d-m-Y');
        $descricao =strtoupper($_POST['inputbairro']);
        $obs =$_POST['inputobsbairro'];
        $dados = array(
                        'descricao'=> $descricao,
                        'observacao' =>$obs,
                        'status'   => 1
                      );
        try {
          $bairro->insert($dados);
        } catch (Zend_Exception $e) {
          $msg=$e->getTace() . $e->getPrevious() ;
          $this->_redirect('/error?msg=' . $msg);
        }
        unset($bairro);
        //$errors = $this->_getParam('error_handler');
       // $msg = = $errors->exception;
        if ($msg=="") {
            # code...
            $msg="Dados do bairro salvos com sucesso!";
        }
        $this->_redirect('/menu?msg=' . $msg);
    }

    public function consultarviaturasAction()
    {
        
    }

    public function consultarordesAction()
    {
        
    }

    public function consultartipoocorrenciaAction()
    {
        
    }

    public function consultarnaturezaAction()
    {
        
    }

    public function excluirviaturaAction()
    {
        $viatura = new Application_Model_DbTable_Viaturas();
        $idViatura = 0;
        $idViatura = $_GET['idViatura'];
        $data = array (
            'status'=> 0
        );
        $where = $viatura
                 ->getDefaultAdapter()
                 ->quoteInto('id = ?', $idViatura);
        $viatura->update($data, $where);
        $msg = "Viatura desativada!";

        unset($viatura);
        $this->_redirect('/menu?msg=' . $msg);
    }

    public function editarviaturaAction()
    {
        
        $viatura = new Application_Model_DbTable_Viaturas();
        $idViatura = 0;
        $idViatura =$_POST['idV'];
        $descricao = strtoupper($_POST['desc']);
        $tipo = $_POST['tipo'];
        $dados = array(
                'descricao'     => $descricao,
                'tipo'     => $tipo
                      );
        try {
         $where = $viatura
            ->getDefaultAdapter()
            ->quoteInto('id = ?', $idViatura);
        $viatura->update($dados, $where);
        $msg = "Viatura Editada com sucesso!";
        } catch (Zend_Exception $e) {

            $msg = $e->getTace() . $e->getMessage() ;
            $this->_redirect('/error?msg=' . $msg);
        }
        unset($viatura);
        $this->_redirect('/menu?msg=' . $msg);
    }

    public function excluirodAction()
    {
        $OD = new Application_Model_DbTable_Origemdestino();
        $idViatura = 0;
        $idOD= $_GET['idOD'];
        $data = array (
            'status'=> 0
        );
        try {
            $where = $OD
                ->getDefaultAdapter()
                ->quoteInto('id = ?', $idOD);
            $OD->update($data, $where);
            $msg = "Origem ou destino desativado com sucesso!";
        } catch (Zend_Exception $e) {
            $msg = $e->getTace() . $e->getMessage() ;
            $this->_redirect('/error?msg=' . $msg);
        }
        unset($OD);
        $this->_redirect('/menu?msg=' . $msg);
    }

    public function editarodAction()
    {
        
        $OD = new Application_Model_DbTable_Origemdestino();
        $idOD = 0;
        $idOD =$_POST['idOD'];
        $descricao = strtoupper($_POST['descod']);
        $dados = array(
                'descricao'     => $descricao
                      );
        try {
         $where = $OD
            ->getDefaultAdapter()
            ->quoteInto('id = ?', $idOD);
        $OD->update($dados, $where);
        $msg = "Origem ou destino editado com sucesso!" . $descricao;
        } catch (Zend_Exception $e) {

            $msg = $e->getTace() . $e->getMessage() ;
            $this->_redirect('/error?msg=' . $msg);
        }
         unset($OD);
        $this->_redirect('/menu?msg=' . $msg);
    }

    public function editartipoocorrenciaAction()
    {
        $tipo = new Application_Model_DbTable_Tipoocorrencia();
        $idTipo = 0;
        $idTipo =$_POST['idTipo'];
        $descricao = strtoupper($_POST['descocorrencia']);
        $dados = array(
                    'descricao'     => $descricao
                 );
        try {
         $where = $tipo
            ->getDefaultAdapter()
            ->quoteInto('id = ?', $idTipo);
        $tipo->update($dados, $where);
        $msg = "Tipo editado com sucesso!";
        } catch (Zend_Exception $e) {
            $msg = $e->getTace() . $e->getMessage() ;
            $this->_redirect('/error?msg=' . $msg);
        }
        unset($tipo);
        $this->_redirect('/menu?msg=' . $msg);
    }

    public function excluirtipoocorrenciaAction()
    {
        $tipo = new Application_Model_DbTable_Tipoocorrencia();
        $idTipo = 0;
        $idTipo= $_GET['idTipo'];
        $data = array (
            'status'=> 0
        );
        try {
            $where = $tipo
                ->getDefaultAdapter()
                ->quoteInto('id = ?', $idTipo);
            $tipo->update($data, $where);
            $msg = "Tipo desativado com sucesso!";
        } catch (Zend_Exception $e) {
            $msg = $e->getTace() . $e->getMessage() ;
            $this->_redirect('/error?msg=' . $msg);
        }
        unset($tipo);
        $this->_redirect('/menu?msg=' . $msg);
    }

    public function excluirnaturezaAction()
    {
        
        $natureza = new Application_Model_DbTable_Natureza();
        $idNatureza = 0;
        $idNatureza= $_GET['idNatureza'];
        $data = array (
            'status'=> 0
        );
        try {
            $where = $natureza
                ->getDefaultAdapter()
                ->quoteInto('id = ?', $idNatureza);
            $natureza->update($data, $where);
            $msg = "Natureza desativada com sucesso!";
        } catch (Zend_Exception $e) {
            $msg = $e->getTace() . $e->getMessage() ;
            $this->_redirect('/error?msg=' . $msg);
        }
        unset($natureza);
        $this->_redirect('/menu?msg=' . $msg);
    }

    public function editarnaturezaAction()
    {
        
        $natureza = new Application_Model_DbTable_Natureza();
        $idNatureza = 0;
        $idNatureza= $_POST['idNatureza'];
        $descricao =strtoupper($_POST['inputnatureza']);
        $idto = $_POST['tipoocorrencia'];

        $data = array(
                        'descricao'=> $descricao,
                        'idtipoocorrencia'=>$idto
                      );
        try {
            $where = $natureza
                ->getDefaultAdapter()
                ->quoteInto('id = ?', $idNatureza);
            $natureza->update($data, $where);
            $msg = "Natureza editada com sucesso!";
        } catch (Zend_Exception $e) {
            $msg = $e->getTace() . $e->getMessage() ;
            $this->_redirect('/menu?msg=' . $msg);
        }
        unset($natureza);
        $this->_redirect('/menu?msg=' . $msg);
    }

    public function testeAction()
    {
        
    }

    public function cadastroachadoAction()
    {
        
        $achado = new Application_Model_DbTable_Achado();
        $descricao =strtoupper($_POST['descachado']);
       
        $data = array(
                        'descricao'=> strtoupper($descricao),
                        'status' => 1
                      );
        try {
            $achado->insert($data);
            $msg = "Achado editado com sucesso!";
        } catch (Zend_Exception $e) {
            $msg = $e->getTace() . $e->getMessage() ;
            $this->_redirect('/error?msg=' . $msg."variable: ".$descricao);
        }
        unset($achado);
        $this->_redirect('/menu?msg=' . $msg);
    }

    public function excluirachadoAction()
    {
        
        $achado = new Application_Model_DbTable_Achado();
        $idachado = 0;
        $idachado = $_GET['idAchado'];
        $data = array (
            'status'=> 0
        );
        $where = $achado
                 ->getDefaultAdapter()
                 ->quoteInto('id = ?', $idachado);
        $achado->update($data, $where);
        $msg = "Achado desativado!";

        unset($achado);
        $this->_redirect('/menu?msg=' . $msg);
    }

    public function editarachadoAction()
    {
        
        $achado = new Application_Model_DbTable_Achado();
        $idachado = 0;
        $idachado= $_POST['idAchado'];
        $descricao =strtoupper($_POST['achado']);

        $data = array(
                        'descricao'=> $descricao
                      );
        try {
            $where = $achado
                ->getDefaultAdapter()
                ->quoteInto('id = ?', $idachado);
            $achado->update($data, $where);
            $msg = "Achado editado com sucesso!";
        } catch (Zend_Exception $e) {
            $msg = $e->getTace() . $e->getMessage() ;
            $this->_redirect('/error?msg=' . $msg);
        }
        unset($achado);
        $this->_redirect('/menu?msg=' . $msg);
    }

    public function consultarachadoAction()
    {
        
    }

    public function salvarconfiguracoesAction()
    {
        
        $config = new Application_Model_DbTable_Configuracoes();

        $range01 =  $_POST['range01'];
        $range02 =  $_POST['range02']; 
       
        $data = array(
                    'range01'=> $range01,
                    'range02'=> $range02
                );
        try {
            $where = $config
                ->getDefaultAdapter()
                ->quoteInto('id = ?', 1);
            $config->update($data, $where);
            $msg="Configurações alteradas com sucesso";
        } catch (\Exception $e) {
            $msg = $e->getTace() . $e->getMessage() ;
            $this->_redirect('/error?msg=' . $msg . ":range01:>" . $range01 . ":range02:>" . $range02);
        }
        unset($config);
        $this->_redirect('/menu?msg=' . $msg);
    }

    public function editarconfiguracoesAction()
    {
        
    }

    public function consultarbairroAction()
    {
        // action body
    }

    public function editarbairroAction()
    {
        // action body
        $Bairro = new Application_Model_DbTable_Bairro();
        $idBairro = 0;
        $idBairro= $_POST['idBairro'];
        $descricao =strtoupper($_POST['descod']);

        $data = array(
                        'descricao'=> $descricao
                      );
        try {
            $where = $Bairro
                ->getDefaultAdapter()
                ->quoteInto('id = ?', $idBairro);
            $Bairro->update($data, $where);
            $msg = "Bairro editado com sucesso!";
        } catch (Zend_Exception $e) {
            $msg = $e->getTace() . $e->getMessage() ;
            $this->_redirect('/error?msg=' . $msg);
        }
        unset($Bairro);
        $this->_redirect('/menu?msg=' . $msg);
    }

    public function excluirbairroAction()
    {
        // action body
        $bairro = new Application_Model_DbTable_Bairro();
        $idBairro = 0;
        if ( is_null($_GET['idBairro']) || ($_GET['idBairro'] != 0) ) {
            $idBairro = $_GET['idBairro'];
        }

        $data = array (
            'status'=> 0
        );
        try {
            $where = $bairro
                     ->getDefaultAdapter()
                     ->quoteInto('id = ?', $idBairro);
            $bairro->update($data, $where);
            $msg = "Bairro desativado!";
         } catch (Exception $e) {
            $msg = $e->getTace() . $e->getMessage() ;
            $this->_redirect('/error?msg=' . $msg);
        }
        unset($bairro);
        $this->_redirect('/menu?msg=' . $msg);
    }


}



































