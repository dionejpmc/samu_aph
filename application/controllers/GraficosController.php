<?php

class GraficosController extends Zend_Controller_Action
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
        $periodo = $config->fetchAll();
        $this->view->periodo = $periodo;
        $this->view->dadosficha = $select;
        $tipo = new Application_Model_DbTable_Tipoocorrencia();
        $resulttipo = $tipo->fetchAll();
        $this->view->tipo=$resulttipo;
        $natureza = new Application_Model_DbTable_Natureza();
        $resultnatureza = $natureza->fetchAll();
        $this->view->natureza=$resultnatureza;
        $viaturas = new Application_Model_DbTable_Viaturas();
        $resultviaturas = $viaturas->fetchAll();
        $this->view->viaturas=$resultviaturas;
        $bairro = new Application_Model_DbTable_Bairro();
        $resultbairro = $bairro->fetchAll();
        $this->view->bairro=$resultbairro;
        $des = new Application_Model_DbTable_Origemdestino();
        $resultdes = $des->fetchAll();
        $this->view->destino=$resultdes;
        $or = new Application_Model_DbTable_Origemdestino();
        $resultor = $or->fetchAll();
        $this->view->origem=$resultor;
        $achados = new Application_Model_DbTable_Achado();
        $resultachados = $achados->fetchAll();
        $this->view->achados=$resultachados;

        $whereanual = $ficha->select()
        ->where('status = ?',1)
        //->where('data >= ?',date("01/01/Y"))
       // ->where('data <= ?',date("30/01/Y"));
        ->where('data >= ?',date($range01))
        ->where('data <= ?',date($range02));
        $selectanual = $ficha->fetchAll($whereanual);
        $this->view->selectanual = $selectanual;



        $db = Zend_Db_Table::getDefaultAdapter();
        //EXPLICAÇÃO DE COMO CONTAR VALORES REPETIDOS
        //https://php.eduardokraus.com/um-pouco-mais-de-como-usar-o-zend-db-select
        $stmt01 =$db->select ()
                  ->from ('fichaaph', array( 'idviatura', 'COUNT(idviatura) AS qtde' ) )
                  ->where('status = ?',1)
                  ->where('data >= ?',$range01)
                  ->where('data <= ?',$range02)
                  ->group( 'idviatura' )
                  ->query ();
        $resultviatura  = $stmt01->fetchAll();
        $this->view->countviatura = $resultviatura;

        $stmt02 =$db->select ()
                  ->from ('fichaaph', array( 'idnatureza', 'COUNT(idnatureza) AS qtde' ) )
                  ->where('status = ?',1)
                  ->where('data >= ?',$range01)
                  ->where('data <= ?',$range02)
                  ->group('idnatureza' )
                  ->query ();
        $resultnatu  = $stmt02->fetchAll();
        $this->view->countnatureza = $resultnatu;

        $stmt03 =$db->select ()
                  ->from ('fichaaph', array( 'idbairro', 'COUNT(idbairro) AS qtde' ) )
                  ->where('status = ?',1)
                  ->where('data >= ?',$range01)
                  ->where('data <= ?',$range02)
                  ->group('idbairro' )
                  ->query ();
        $resultbairro  = $stmt03->fetchAll();
        $this->view->countbairro = $resultbairro;

        $stmt04 =$db->select ()
                  ->from ('fichaaph', array( 'idorigem', 'COUNT(idorigem) AS qtde' ) )
                  ->where('status = ?',1)
                  ->where('data >= ?',$range01)
                  ->where('data <= ?',$range02)
                  ->group('idorigem' )
                  ->query ();
        $resultor  = $stmt04->fetchAll();
        $this->view->countor = $resultor;

        $stmt05 =$db->select ()
                  ->from ('fichaaph', array( 'iddestino', 'COUNT(iddestino) AS qtde' ) )
                  ->where('status = ?',1)
                  ->where('data >= ?',$range01)
                  ->where('data <= ?',$range02)
                  ->group('iddestino' )
                  ->query ();
        $resultdes  = $stmt05->fetchAll();
        $this->view->countdes = $resultdes;

        //http://zendgeek.blogspot.com.br/2009/07/zend-framework-sql-joins-examples.html
        //http://www.diogomatheus.com.br/blog/zend-framework/realizando-joins-no-zend-framework/
        //http://docs.huihoo.com/php/zend/ZendFramework-0.1.5/documentation/end-user/pt-br/zend.db.select.html
        $stmt06 =$db->select ()
                  ->from(array('f'=>'fichaaph'), array('idnatureza'))
                  ->joinInner(array('n'=>'natureza'),'n.id = f.idnatureza')
                  ->joinInner(array('t'=>'tipoocorrencia'), 't.id = n.idtipoocorrencia', array('t.id','t.descricao'))
                  ->where('f.status = ?',1)
                  ->where('f.data >= ?',$range01)
                  ->where('f.data <= ?',$range02)
                  ->query();
        $resultt  = $stmt06->fetchAll();
        $this->view->counttipo = $resultt;

        unset($config);
        unset($natureza);
        unset($viaturas);
        unset($bairro);
    }

    public function indexAction()
    {
        // action body
    }

    public function ocorrenciaporsexoAction()
    {
        // action body
    }

    public function ocorrenciapormesAction()
    {
        // action body
    }

    public function ocorrenciaporidadeAction()
    {
        // action body
    }

    public function ocorrenciapornaturezaAction()
    {
        // action body
    }

    public function achadosclinicosAction()
    {
        // action body
    }

    public function atendimentoporviaturaAction()
    {
        // action body
    }

    public function tempoderespostaAction()
    {
        // action body
    }

    public function atendimentopordestinoAction()
    {
        // action body
    }

    public function atendimentopororigemAction()
    {
        // action body
    }

    public function atendimentoporbairroAction()
    {
        // action body
    }

    public function ocorrenciaportipoAction()
    {
        // action body
    }


}























