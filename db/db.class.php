<?php

@$GLOBALS['url'] = $url;

// Define a constante de diretório raiz de maneira adequada
define('_DIR_', '/');

// Define o fuso horário padrão
date_default_timezone_set('America/Sao_Paulo');

class db {
    private $host;
    private $dbName;
    private $user;
    private $pass;
    private $port;
    private $dbh;
    private $error;
    private $qError;
    private $stmt;

    public function __construct() {
        // Atribui valores das variáveis de ambiente
        $this->host   = getenv('DB_HOST');
        $this->dbName = getenv('DB_NAME');
        $this->user   = getenv('DB_USER');
        $this->pass   = getenv('DB_PASS');
        $this->port   = getenv('DB_PORT') ?: '3306'; // Padrão para a porta MySQL

        // Monta o DSN para conexão com MySQL
        $dsn = "mysql:host={$this->host};dbname={$this->dbName};port={$this->port};charset=utf8";

        $options = array(
            PDO::ATTR_PERSISTENT    => true,
            PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION
        );

        try {
            // Cria uma nova conexão PDO
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
            //echo "Conexão realizada com sucesso!";
        } catch (PDOException $e) {
            // Captura qualquer erro de conexão
            $this->error = $e->getMessage();
            echo "Erro de conexão: " . $this->error;
        }
    }
    



	//Aquis�o
  	public function query($query){
  		$this->stmt = $this->dbh->prepare($query);
  	}

	//Conecatar
  	public function bind($param, $value, $type = null){
  		if(is_null($type)){
  			switch (true){
  				case is_int($value):
  				$type = PDO::PARAM_INT;
  				break;
  				case is_bool($value):
  				$type = PDO::PARAM_BOOL;
  				break;
  				case is_null($value):
  				$type = PDO::PARAM_NULL;
  				break;
  				default:
  				$type = PDO::PARAM_STR;
  			}
  		}
  		$this->stmt->bindValue($param, $value, $type);
  	}

    //Executar
  	public function execute(){
  		return $this->stmt->execute();

  		$this->qError = $this->dbh->errorInfo();
  		if(!is_null($this->qError[2])){
  			echo $this->qError[2];
  		}
  		echo 'done with query';
  	}

	//Exibir em objeto
  	public function object(){
  		return $this->stmt->fetchObject();
  	}

	//Exibir Array
  	public function row(){
  		$this->execute();
  		return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
  	}

	//�nico
  	public function single(){
  		$this->execute();
  		return $this->stmt->fetch(PDO::FETCH_ASSOC);
  	}

	//lista de tabela
  	public function table(){
  		$this->execute();
  		return $this->stmt->fetchAll(PDO::FETCH_COLUMN);
  	}

	// Numero de Linha
  	public function rowCount(){ 
  		return $this->stmt->rowCount();
  	}

	//Ultimo Id cadastrado
  	public function lastInsertId(){
  		return $this->dbh->lastInsertId();
  	}

	//Iniciar Transa��o
  	public function beginTransaction(){
  		return $this->dbh->beginTransaction();
  	}

	//final transa��o
  	public function endTransaction(){
  		return $this->dbh->commit();
  	}

	//cancelar trasa��o
  	public function cancelTransaction(){
  		return $this->dbh->rollBack();
  	}

	//depurar params de despejo
  	public function debugDumpParams(){
  		return $this->stmt->debugDumpParams();
  	}

	//Erro query
  public function queryError() {
      $this->qError = $this->dbh->errorInfo(); // Armazena as informações de erro do PDO
      if (!is_null($this->qError[2])) { // O índice 2 contém a mensagem de erro, caso exista
          echo $this->qError[2];
      }
  }

	//***************************************************************




  }


//********************************************************************************************************
/*
try {

	$cli = new db();	   
	$cli->query("SELECT * FROM dados");
	$cli->execute();
  #$resultCli2 = $cli->row();
	$resultCli = $cli->object();

	print_r($cli->table());

	echo $resultCli->dados_id;
	echo "<br>";
//print_r($resultCli2);
//echo "<br>";
//echo $resultCli2[0]['dados_nome'];

} catch (PDOException $e) {
	echo $e->getMessage();
}
*/



/*Fun��o de cadastro*/
function cadastro($tabela, $postagem, $url){

  print_r($_POST);


  /*Verificar coluna no banco*/
  $ver = new db();
  $ver->query( "DESCRIBE ".$tabela );
  $ver->execute();
  $table_fields = $ver->table();

  foreach ($ver->table() as $key) {
    $tabelasSQL[$key] = $key;
  }

  /* Listar */
  foreach( $postagem AS $val => $key ){
    if( $val == $tabelasSQL[$val] ){
      $Into[]   = " {$val} " ;
      $Values[] = " :{$val}" ;
    }
  }

  /* Arrays Implode */
  $QueryInto   = @implode(',', $Into);
  $QueryValues = @implode(',', $Values);

  /* registro */
  try{
    $sql = "INSERT INTO {$tabela} ({$QueryInto}) VALUES ({$QueryValues})";
    $db = new db();
    $db->query($sql);

    foreach( $postagem AS $val => $key ){
      if( $val == $tabelasSQL[$val] ){
       $db->bind(":{$val}", $key);
     }
   }

   /* registro sucesso */
   if($db->execute()){
    echo notify('success');
  } else{
    echo notify('danger');
  }

  /* Redirecionar */
  if( !empty( $_POST['redirecionar'] ) ){
    echo '<meta http-equiv="refresh" content="1;URL='.$_POST['url'].'!/'.$tabela.'/'.$_POST['redirecionar'].'/'.$db->lastInsertId().'" />';
  }

} catch (PDOException $e) {
  throw new PDOException($e);
}

}

$tabela = '';
$coluna = '';
$valor = '';
$postagem = '';

/*Fun��o de editar*/
function editar($tabela, $postagem , $coluna, $valor, $url){


  /*Verificar coluna no banco*/
  $ver = new db();
  $ver->query( "DESCRIBE ".$tabela );
  $ver->execute();
  $table_fields = $ver->table();

  foreach ($ver->table() as $key) {
    $tabelasSQL[$key] = $key;
  }

  /* Listar */
  foreach( $postagem AS $val => $key ){
    if( $val == $tabelasSQL[$val] ){
      $Into[]  = " {$val} = :{$val}" ;
    }
  }

  /* Arrays Implode */
  $QueryInto   = @implode(',', $Into);


  /* Editar */
  try{

    $sql = "UPDATE {$tabela} SET {$QueryInto} WHERE {$coluna} = :{$coluna}";
    $db = new db();
    $db->query($sql);

    foreach( $postagem AS $val => $key ){
      if( $val == $tabelasSQL[$val] ){
       $db->bind(":{$val}", $key);
     }
   }


   /* Editado sucesso */
   if($db->execute()){
    echo notify('success');
  } else{
    echo notify('danger');
  }

  /* Redirecionar */
  if( !empty( $_POST['redirecionar'] ) ){
    echo '<meta http-equiv="refresh" content="1;URL='.$_POST['url'].'!/'.$tabela.'/'.$_POST['redirecionar'].'" />';
  }

} catch (PDOException $e) {
  throw new PDOException($e);
}

}


/*Fun��o de deletar*/
function deletar($tabela, $coluna, $valor, $url, $delPasta = NULL){

  try{ 

    $dir = 'images/'.$delPasta.'/'.$valor;
    $dirArray = array('p', 'm', 'g', 'u', 'original');

    $del = new db();
    $del->query("DELETE FROM {$tabela} WHERE {$coluna} = :{$coluna}");
    $del->bind(":{$coluna}" , $valor);

    /* registro sucesso */
    if($del->execute()){
      echo notify('success');

      /* deletar pasta */
      if( !empty($delPasta) ){

        foreach( $dirArray AS $extensoes ){

          $dirExt = '../'.$dir.'/'.$extensoes;

          if( is_dir($dirExt) ){

                     function apagar($dirDeletar)
                     {
                      if(is_dir($dirDeletar)){
                        if($handle = @opendir($dirDeletar)){
                          while(false !== ($file = @readdir($handle))){
                            if(($file == ".") or ($file == "..")){
                              continue;
                            }
                            if(is_dir($file)){
                              apagar($dirDeletar.'/'.$file);
                            }else{
                              unlink($dirDeletar.'/'.$file);
                            }
                          }
                        }else{
                          return false;
                        }

                        @closedir($handle);
                        @unlink($dirDeletar);
                        @rmdir($dirDeletar);
                      }
                      else
                      {
                        return false;
                      }
                    }

                    /* deletar */
                    apagar($dirExt);

        }

      }
      if(is_dir($dir)){
       /* deletar */
       apagar($dir);
      }

    }
    
  echo '<meta http-equiv="refresh" content="1;URL='.$_POST['url'].'!/'.$tabela.'/'.$_POST['redirecionar'].'" />';

    exit;


  }else{
    echo notify('danger');
  }

  echo '<meta http-equiv="refresh" content="1;URL='.$_POST['url'].'!/'.$tabela.'/'.$_POST['redirecionar'].'" />';

} catch (PDOException $e) {
  throw new PDOException($e);
}

}

