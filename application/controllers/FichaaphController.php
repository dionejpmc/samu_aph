<?php

class FichaaphController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $config = new Application_Model_DbTable_Configuracoes();
        $dataconfig = $config->fetchAll();
        $ficha = new Application_Model_DbTable_Fichaaph();
        date_default_timezone_set('America/Sao_Paulo');
        foreach ($dataconfig as $key => $value) {
            $range01 = $value['range01'];
            $range02 = $value['range02'];
        }
        $where = $ficha->select()
        ->from('fichaaph')
        ->where('status = ?',1)
        ->where('data >= ?',$range01)
        ->where('data <= ?',$range02);
        $select = $ficha->fetchAll($where);
        $this->view->fichaaph = $select;

    }

    public function indexAction()
    {
        // action body
    }

    public function salvarfichaaphAction()
    {
        // action body
        $ficha = new Application_Model_DbTable_Fichaaph();
        date_default_timezone_set('America/Sao_Paulo');

        if ($_POST['colonizado']== 1) {
            # code...
            $colonizado     = 1;
        }
        else{
            $colonizado     = 0;    
        }
        $horario        =  date('H:i');
        $datacad        =  date('Y-m-d');
        $codigo         = $_POST['codigo'];
        $data           = $_POST['data'];
        $hsaida         = $_POST['hsaida'];
        $hchegadalocal  = $_POST['hchegadalocal'];
        $hsaidalocal    = $_POST['hsaidalocal'];
        $hchegadafinal  = $_POST['hchegada'];
        $datacadastro   = $datacad;
        // $paciente       = strtoupper($_POST['paciente']);
        $sexo           = $_POST['sexo'];
        $idade          = $_POST['idade'];
        $idorigem       = $_POST['origem'];
        $iddestino      = $_POST['destino'];
        $idviatura      = $_POST['viatura'];
        $idnatureza     = $_POST['natureza'];
        $bairro         = $_POST['bairro'];
        if (strlen($_POST['achado']) <= 3) {
             $achado    = null;
        }else{
             $achado    = strtoupper($_POST['achado']);
        }
       

        $dbbairro = new Application_Model_DbTable_Bairro();
        $resultbairro = $dbbairro->fetchAll("status= '1'");
        foreach ($resultbairro as $key => $valueb) {
            if ($valueb['descricao']== $bairro) {
                $idbairro  =  $valueb['id'];
            }
        }
        // if (($idbairro=='0') || (is_null($idbairro))){
        //     $msg="ATENÇÃO : ERRO: Erro no campo bairro ENTRADA:". strtoupper($bairro) . ": INVÁLIDA";
        //     $this->_redirect('/?msg=' . $msg);
        // }
        if (($idbairro=='0') || (is_null($idbairro))){
            $idbairro=0;
        }
        
        $obito = '';
        if ($_POST['rating'] == 0 || $_POST['rating'] == '0' || $_POST['rating'] == "0") {
            $obito      = $_POST['ratingop'];
        }else {
             $obito = $_POST['rating'];
        }
        $auth = Zend_Auth::getInstance();
        $username = $auth->getIdentity();

        $dados = array(
                    'codigo'         => $codigo,
                    'data'           => $data,
                    'hsaida'         => $hsaida,
                    'hchegadalocal'  => $hchegadalocal,
                    'hsaidalocal'    => $hsaidalocal,
                    'hchegadafinal'  => $hchegadafinal,
                    'datacadastro'   => $datacadastro,
                    // 'paciente'       => $paciente,
                    'colonizado'     => $colonizado,
                    'sexo'           => $sexo,
                    'idade'          => $idade,
                    'idorigem'       => $idorigem,
                    'iddestino'      => $iddestino,
                    'idviatura'      => $idviatura,
                    'idnatureza'     => $idnatureza,
                    'idbairro'       => $idbairro,
                    'status'         => 1,
                    'achado'         => $achado,
                    'user'           => $username->nome . ",". $username->user,
                    'obito'          => $obito
                );

        $ficha->insert($dados);
        unset($ficha);
        unset($dbbairro);
       
    }

    public function consultarfichaaphAction()
    {
        // action body
    }

    public function editarfichaaphAction()
    {
        // action body
    }

    public function excluirfichaaphAction()
    {
        // action body
        $ficha = new Application_Model_DbTable_Fichaaph();
        $idficha= $_GET['codficha'];
        date_default_timezone_set('America/Sao_Paulo');
        $date = date('Y-m-d H:i');
        $auth = Zend_Auth::getInstance();
        $username = $auth->getIdentity();
        $data = array (
            'status'=> 0,
            'user'           => $username->user . " , ". $username->nome,
            'dataalteracao' => $date
        );
        try {
            $where = $ficha
                ->getDefaultAdapter()
                ->quoteInto('codigo = ?', $idficha);
            $ficha->update($data, $where);

            $msg ='Ficha dasativada com sucesso';           
        } catch (Zend_Exception $e) {
            $msg = $e->getTace() . $e->getMessage() ;
            $this->_redirect('/error?msg=' . $msg);
        }
        unset($ficha);
        $this->_redirect('/menu?msg=' . $msg);
    }

    public function getCodigoAction()
    {
  

        

    }


}











