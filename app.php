<?php 

     class Dashboard 
     {
     	public $dataInicio;
     	public $dataFim;
     	public $numVendas;
     	public $totalVendas;
     	public $clienteAtivo;
     	public $clienteInativo;

     	public function __get($atributo)
     	{
         return $this->$atributo;
     	}

     	public function __set($atributo, $valor)
     	{
     		$this->$atributo = $valor;
     		return $this;
     	}
     }

   
     class Conexao
     {
     	private $host =  'localhost';
     	private $dbname = 'dashboard';
     	private $user = 'root';
     	private $pass = '';
        

     	public function conect()
     	{
     		try {

     			$conexao = new PDO(
                        "mysql:host =$this->host;dbname=$this->dbname",
                        "$this->user",
                        "$this->pass"
     			);

     			$conexao->exec('set charset utf8');

     			return $conexao;
     			
     		} catch (PDOException $e) {
     			echo '<p>'. $e->getMessege() . '</p>';
     		}
     	}

     }

    
     class Bd
     {
     	private $conexao;
     	private $dashboard;

     	public function __construct(Conexao $conexao, Dashboard $dashboard)
     	{
     		$this->conexao = $conexao->conect();
     		$this->dashboard =  $dashboard;
     	}

     	public function getNumVendas()
     	{
     		$query = 'select
     		              count(*) as numero_vendas
     		          from
     		              tb_vendas
     		          where
     		              data_venda between :dataInicio and :dataFim ';

     		$stmt = $this->conexao->prepare($query);
     		$stmt->bindValue(':dataInicio', $this->dashboard->__get('dataInicio'));
     		$stmt->bindValue(':dataFim', $this->dashboard->__get('dataFim'));
     		$stmt->execute();

     		return $stmt->fetch(PDO:: FETCH_OBJ)->numero_vendas;
     	}

     	public function getTotalVendas()
     	{
     		$query = 'select
     		              SUM(total) as total_vendas
     		          from
     		              tb_vendas
     		          where
     		              data_venda between :dataInicio and :dataFim ';

     		$stmt = $this->conexao->prepare($query);
     		$stmt->bindValue(':dataInicio', $this->dashboard->__get('dataInicio'));
     		$stmt->bindValue(':dataFim', $this->dashboard->__get('dataFim'));
     		$stmt->execute();

     		return $stmt->fetch(PDO:: FETCH_OBJ)->total_vendas;
     	}

     	public function getClienteAtivo()
     	{
     		$query = 'select
     		               count(*) as cliente_ativo
     		          from 
     		               tb_clientes
     		          where 
     		               cliente_ativo = 1
                           ';

           $stmt = $this->conexao->prepare($query);
            $stmt->execute();
           
           $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

           return $resultado['cliente_ativo'];
     	}

     	public function getClienteInativo()
     	{
     		$query = 'select
     		               count(*) as cliente_ativo
     		          from 
     		               tb_clientes
     		          where 
     		               cliente_ativo = 0';

           $stmt = $this->conexao->prepare($query);
            $stmt->execute();
           
           $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

           return $resultado['cliente_ativo'];
     	}


     }

     $dashboard = new Dashboard();
    

     $conexao = new Conexao();

     $competencia = explode('-', $_GET['competencia']);
     $ano = $competencia[0];
     $mes = $competencia[1];

     $mesDia = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

     $dashboard->__set('dataInicio', $ano .'-'. $mes . '-01');
     $dashboard->__set('dataFim', $ano .'-'. $mes . '-'. $mesDia);

     $bd = new Bd($conexao, $dashboard );

     $dashboard->__set('numVendas', $bd->getNumVendas());
     $dashboard->__set('totalVendas', $bd->getTotalVendas());
     $dashboard->__set('clienteAtivo', $bd->getClienteAtivo());
     $dashboard->__set('clienteInativo', $bd->getClienteInativo());

     echo json_encode($dashboard);
     

     

 ?>