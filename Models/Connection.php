<?

class Conexion{
	private $sql;
	private $datos = array(
			"host" => "localhost",
			"user" => "root",
			"pass" => "",
			"db" => "Giampiero_TempBuddy"
			);
	private $con;
		
	function __construct(){
		$this->con = new mysqli($this->datos['host'],$this->datos['user'],
							   $this->datos['pass']);
		$sql = "CREATE DATABASE IF NOT EXISTS Giampiero_TempBuddy";
		$this->con->query($sql);

		$this->con = new mysqli($this->datos['host'],$this->datos['user'],
							   $this->datos['pass'],$this->datos['db']);
		}

	function consultaSimple($sql){
		$this->con->query($sql);
		}

	function consultaRetorno_row($sql){
		$datos = $this->con->query($sql);
		$row = $datos->fetch_assoc();
		return $row;
		}
	function consultaRetorno($sql){
		$datos = $this->con->query($sql);
		return $datos;
		}		

	}
	
?>