<?
class worker{
	public $userID;
	public $name;
	public $Jobs   = array();
	public $Breaks = array();
	public $Evento = array();
	public $con;

	function __construct(){
		$this->con = new Conexion();
	}

	function APItoDB($APIarray){
	// Bring data from the API to the local DB
		// Create Table Time_Event (Job or Break) if not exists
		$sql = "CREATE TABLE IF NOT EXISTS Time_Event  (
			  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			  `userID` int(11) NOT NULL,
			  `tipe` enum('Job','Break') NOT NULL,
			  `event_start` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `event_end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
			) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
		$this->con->consultaSimple($sql);
		// Create Table Worker (Job or Break) if not exists
		$sql = "CREATE TABLE IF NOT EXISTS Worker (
			  `userID` int(11) NOT NULL,
			  `name` varchar(50) NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=latin1";
		$this->con->consultaSimple($sql);

		// Bring API data to a new array to know the position of the information
		// Found differences in the properties names from the API received object
		foreach ($APIarray as $key => $value) { // found differences in the properties name 
			$objPart[] = $value;
		}
		// Seting the API information in this object in the precise properties 
		$this->userID = $objPart[0];
		$this->name   = $objPart[1];
		$this->add(); // Add the worker to the Workers Table

		$this->Event  = array('Job' => $objPart[2] , 'Break' => $objPart[3]);
		// add each event Job or Break to the Time_Event Table
		foreach ($this->Event as $key => $JobBreak) 
			foreach ($JobBreak as $value) { // Jobs
				$Event = new time_event();
				$Event->userID = $this->userID;
				$Event->tipe = $key;
				$Event->event_start = $Event->Time_To_GMT($value['start']);
				$Event->event_end   = $Event->Time_To_GMT($value['end']);
				$Event->add(); // Add the event
		}
		
		header("location: index.php?userID=".$this->userID);
	}

	function add(){ 
	// add Worker to the local DB
		//  delete if exists 
		$sql = "DELETE FROM Worker WHERE userID = '{$this->userID}'";
		$this->con->consultaSimple($sql); 
		// Insert it 
		$sql = "INSERT INTO Worker (userID, name)
				values ('{$this->userID}','{$this->name}')";
		$this->con->consultaSimple($sql);
	}

	function view(){
	// view worker basic personal data
		$sql = "SELECT * FROM Worker WHERE userID = '{$this->userID}'";
		$datos = $this->con->consultaRetorno($sql);
		$row = mysql_fetch_assoc($datos);
		return $row;
	}
	function view_name(){
	// view worker basic personal data
		$sql = "SELECT * FROM Worker WHERE userID = '{$this->userID}'";
		$datos = $this->con->consultaRetorno_row($sql);
		return $datos['name'];
	}

	function view_all(){
	// return all workers personal data
		$sql = "SELECT * FROM Worker ORDER BY name ";
		$datos = $this->con->consultaRetorno($sql);
		return $datos;
	}

}

?>