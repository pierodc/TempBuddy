<?

class pay_time extends time_event{
	public $start_datetime;
	public $end_datetime;
	public $earlyMorning = array('start' => '00:00:00' , 'end' => '07:00:00' , 'rate_eur' => 12);
	public $Morning 	 = array('start' => '07:00:00' , 'end' => '15:00:00' , 'rate_eur' => 8);
	public $Evening 	 = array('start' => '15:00:00' , 'end' => '23:00:00' , 'rate_eur' => 10.5);
	public $Night   	 = array('start' => '23:00:00' , 'end' => '24:00:00' , 'rate_eur' => 12);
	public $Weekend_rate_eur = .5;
	
	public $HrMorning 	=0;
	public $HrEvening 	=0;
	public $HrNight 	=0;
	public $HrWeekend 	=0;

	public $PaymentMorning 	=0;
	public $PaymentEvening 	=0;
	public $PaymentNight 	=0;
	public $PaymentWeekend 	=0;

	function __construct()
	{	}

	function Time_spent($start,$end){
	// Returns time in hour with minutes as decimals between two DateTime
		$start_stamp = strtotime($start);
		$end_stamp   = strtotime($end);
		return ($end_stamp - $start_stamp)/3600;
	}

	function pay_time ($start_datetime , $end_datetime){
	// Intended to split the Job in differents blocks on times with differents rates values.

		// Look if Start DateTime is in weekend
		if( date("N",strtotime($start_datetime)) >=6 ){
			$Weekend = $this->Weekend_rate_eur;
		}
		else{
			$Weekend = 0;
		}	

		// Look in which hour of the day working time is in
		if ($this->view_time($start_datetime) < $this->earlyMorning['end']) {
   			// Look if time period ends after the actual time hourly-price
   			$end_datetime_this = min($this->view_date($start_datetime).' '.$this->earlyMorning['end'], $end_datetime);
			// Calculation of time period in hours with minutes represented in decimals of hour 
			$time_spent = $this->Time_spent($start_datetime , $end_datetime_this);
			// Set properties of time spent and value in this hourly-price
   			$this->HrNight += round($time_spent,2);
   			$this->PaymentNight += round($time_spent * ($this->earlyMorning['rate_eur'] + $Weekend),2);
   			// Set new start DateTime as the end of this hourly-price range to start a new cicle-function
   			$start_datetime = $this->view_date($start_datetime).' '.$this->earlyMorning['end'];
   			if($start_datetime < $end_datetime)
   				$this->pay_time($start_datetime, $end_datetime);
		}
		// Works similar as the previous 
		elseif($this->view_time($start_datetime) < $this->Morning['end']){
			$end_datetime_this = min($this->view_date($start_datetime).' '.$this->Morning['end'], $end_datetime);
			$time_spent = $this->Time_spent($start_datetime , $end_datetime_this);
   			$this->HrMorning += round($time_spent,2);
   			$this->PaymentMorning += round($time_spent * ($this->Morning['rate_eur'] + $Weekend),2);
   			$start_datetime = $this->view_date($start_datetime).' '.$this->Morning['end'];
   			if($start_datetime < $end_datetime)
   				$this->pay_time($start_datetime, $end_datetime);
   		}
   		// Works similar as the previous 
   		elseif($this->view_time($start_datetime) < $this->Evening['end']){
			$end_datetime_this = min($this->view_date($start_datetime).' '.$this->Evening['end'], $end_datetime);
			$time_spent = $this->Time_spent($start_datetime , $end_datetime_this);
   			$this->HrEvening += round($time_spent,2);
   			$this->PaymentEvening += round($time_spent * ($this->Evening['rate_eur'] + $Weekend),2);
   			$start_datetime = $this->view_date($start_datetime).' '.$this->Evening['end'];
			if($start_datetime < $end_datetime)
   				$this->pay_time($start_datetime, $end_datetime);
   		}
   		// Differs from the previous because in the Night changes the Date and the Weekend tariff 
   		elseif($this->view_time($start_datetime) < $this->Night['end']) {
   			$end_datetime_this = min($this->view_date($start_datetime).' '.$this->Night['end'], $end_datetime);
			$time_spent = $this->Time_spent($start_datetime , $end_datetime_this);
   			$start_datetime_stamp = strtotime($start_datetime);
			$this->HrNight += round($time_spent,2);
   			$this->PaymentNight += round($time_spent * ($this->Night['rate_eur'] + $Weekend),2);
   			$start_datetime = mktime(0, 0, 0, date("m",$start_datetime_stamp)  , date("d",$start_datetime_stamp)+1, date("Y",$start_datetime_stamp));
			$start_datetime = date("Y-m-d H:i:s" , $start_datetime );
			if($start_datetime < $end_datetime)
   				$this->pay_time($start_datetime, $end_datetime);
   		}

	}

}

?>