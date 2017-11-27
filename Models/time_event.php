<?

class time_event{
	public $userID;
	public $id;
	public $tipe;
	public $event_start;
	public $event_end;
	public $break_start;
	public $break_end;
	public $Weekend;
	public $start_break_date;
	public $start_break_time;
	public $end_break_date;
	public $end_break_time ;

	public $con;

	function __construct(){
		$this->con = new Conexion();
	}

	function Time_To_GMT($date){ 
	// Convert Client's time to Tenerife's time  
		$date_string = substr($date,0,10) . ' ' . substr($date,11,5);
		$Diff_Zone = substr($date,17,2)*3600 + substr($date,20,2)*60;
		if(substr($date,16,1) == "-")
			$Diff_Zone = $Diff_Zone * -1;
		$Time_Stamp = strtotime("$date_string") + $Diff_Zone;
		return date("Y-m-d H:i:s", $Time_Stamp);
	}

	function date_ddd_dd_mm_aaaa($_DateTime){
		// Returns the date of a DateTime String as ddd_dd_mm_aaaa	
		if(date("N",(strtotime($_DateTime))) >=6 )
			$this->Weekend = " WKND";
		else
			$this->Weekend = "";
		return date("D m/d/Y",(strtotime($_DateTime))).$this->Weekend;
	}
	function time_hh_mm($_DateTime){
		// Returns the date of a DateTime String as ddd_dd_mm_aaaa	
		return date("h:i",(strtotime($_DateTime)));
	}

	function view_date($_DateTime){
	// Returns the date of a DateTime String	
		return (substr($_DateTime, 0 , 10));
	}
	function view_time($_DateTime){
	// Returns the time of a DateTime String
		return (substr($_DateTime, 11 , 8));
	}

	function add(){ 
	// Inserts the Job or Break that comes from the API	

		//  delete Job-Break if exists
		$sql = "DELETE FROM Time_Event WHERE userID = '{$this->userID}' 
				AND tipe = '{$this->tipe}' 
				AND event_start = '{$this->event_start}' 
				AND event_end  = '{$this->event_end}' ";
		$this->con->consultaSimple($sql);
		// Insert Job-Break from the API
		$sql = "INSERT INTO Time_Event (userID, tipe, event_start, event_end)
				values ('{$this->userID}','{$this->tipe}','{$this->event_start}','{$this->event_end}')";
		$this->con->consultaSimple($sql);
	}

	function view_all(){ 
	// List all the Jobs for one worker	
		$sql = "SELECT * FROM Time_Event 
				WHERE userID = '{$this->userID}' 
				AND tipe = 'Job'
				ORDER BY event_start";
		$datos = $this->con->consultaRetorno($sql);
		return $datos;
	}
	function view_Break(){ 
	// Return Break event if any
		// Lookup the actual Job start and end time	
		$sql = "SELECT * FROM Time_Event WHERE id = '{$this->id}'";
		$row = $this->con->consultaRetorno_row($sql); //echo $sql;
		$this->userID = $row['userID'];
		$this->event_start = $row['event_start'];
		$this->event_end = $row['event_end'];

		// Lookup if there is a break that corresponds to the Job time	
		$sql = "SELECT * FROM Time_Event 
				WHERE userID = '{$this->userID}'
				AND event_start >= '{$this->event_start}'
				AND event_end <= '{$this->event_end}'
				AND tipe = 'Break'";
		$datos = $this->con->consultaRetorno_row($sql);
		$this->break_start = $datos['event_start'];
		$this->break_end   = $datos['event_end'];
		return $datos;
	}

	function deleteEvent($id){
	// Delete specific Job event
		$sql = "DELETE FROM Time_Event WHERE id = '$id'";
		$this->con->consultaSimple($sql);
		header("location: index.php?userID=".$_GET['userID']);
	}

	function insertEvent($insert_Form){
	// Insert Job and Break event from a form
		$this->userID 			= $insert_Form['userID'];
		$this->event_start 		= $insert_Form['start_date'].' '.$insert_Form['start_time'];
		$this->event_end 		= $insert_Form['end_date'].' '.$insert_Form['end_time'];
		$this->start_break_date = $insert_Form['start_break_date'];
		$this->start_break_time = $insert_Form['start_break_time'];
		$this->end_break_date 	= $insert_Form['end_break_date'];
		$this->end_break_time 	= $insert_Form['end_break_time'];	
		$this->event_start 		= $insert_Form['start_date'].' '.$insert_Form['start_time'];
		$this->event_end 		= $insert_Form['end_date'].' '.$insert_Form['end_time'];
		$this->start_break 		= $this->start_break_date . ' ' . $this->start_break_time;
		$this->break_end   		= $this->end_break_date   . ' ' . $this->end_break_time;
		$sql = "INSERT INTO Time_Event (userID, tipe, event_start, event_end) 
				values ('{$this->userID}','Job','{$this->event_start}','{$this->event_end}')";
		// insert Break if data is available
		if($this->start_break > ' ' and $this->break_end > ' '){
			$sql .= "('{$this->userID}','break','{$this->start_break}','{$this->break_end}')";
		}
		$this->con->consultaSimple($sql);
		header("location: index.php?userID=".$_GET['userID']);
	}


	function editEvent($edit_Form){
	// Update Job Break event from a form
		// update Job
		$this->id 				= $edit_Form['id_job'];
		$this->event_start 		= $edit_Form['start_date'].' '.$edit_Form['start_time'];
		$this->event_end 		= $edit_Form['end_date']  .' '.$edit_Form['end_time'];
		$sql = "UPDATE Time_Event SET
			    event_start = '{$this->event_start}',
				event_end   = '{$this->event_end}'
				WHERE id = {$this->id}";
		$this->con->consultaSimple($sql);
		
		// Update Break
		$this->id 				= $edit_Form['id_break'];
		$this->userID 			= $edit_Form['userID'];
		$this->event_start 		= $edit_Form['start_break_date'].' '.$edit_Form['start_break_time'];
		$this->event_end 		= $edit_Form['end_break_date']  .' '.$edit_Form['end_break_time'];
		
		$this->start_break_date = $edit_Form['start_break_date'];
		$this->start_break_time = $edit_Form['start_break_time'];
		$this->end_break_date 	= $edit_Form['end_break_date'];
		$this->end_break_time 	= $edit_Form['end_break_time'];	
		$this->start_break 		= $this->start_break_date . ' ' . $this->start_break_time;
		$this->break_end   		= $this->end_break_date   . ' ' . $this->end_break_time;

		// Break exist -> update
		if($this->id > ''){
			$sql = "UPDATE Time_Event SET
				    event_start = '{$this->event_start}',
					event_end   = '{$this->event_end}'
					WHERE id = {$this->id}";
			$this->con->consultaSimple($sql);
		}
		// Break does not in DB -> insert 
		elseif($this->start_break > ' ' and $this->break_end > ' '){
			$sql = "INSERT INTO Time_Event (userID, tipe, event_start, event_end) 
					values ('{$this->userID}','Break','{$this->event_start}','{$this->event_end}')";
			$this->con->consultaSimple($sql);
		}
		header("location: index.php?userID=".$_GET['userID']);
	}


}


?>