<?
$Events = new time_event();
$Events->userID = $_GET['userID'];
$Events_all = $Events->view_all();

$Worker = new worker();
$Worker->userID = $_GET['userID'];



if(isset($_POST['insert'])){
  $Events->insertEvent($_POST);  
}
 
if(isset($_POST['edit'])){
  $Events->editEvent($_POST);
}

?>
<h1><?= $Worker->view_name(); ?>'s - Jobs List and Payslip calculation</h1>
<form method="post">
  <table border="1" cellpadding="4">
  <tbody>
    <tr align="center">
      <td rowspan="2"><h3>No</h3>
        <? $No=0; ?></td>
      <td rowspan="2"><h3>Job start</h3></td>
      <td rowspan="2"><h3>Break start</h3></td>
      <td rowspan="2"><h3>Break end</h3></td>
      <td rowspan="2"><h3>Job end</h3></td>
      <td><? 
    // Initialize vars
		$PaymenTotal = $HrTotal = $Payment = $Hr = 0;
		$Payment1 = $Payment2 = $Payment3 = 0;
		 ?>&nbsp;</td>
      <td colspan="2"><h3>Job</h3></td>
      <td colspan="2"><h3>Break</h3></td>
      <td colspan="2"><h3>Net</h3></td>
      <td rowspan="2">&nbsp;</td>
      <td rowspan="2">&nbsp;</td>
    </tr>
    <tr align="center">
      <td>&nbsp;</td>
      <td>Hr</td>
      <td>€</td>
      <td>Hr</td>
      <td>€</td>
      <td>Hr</td>
      <td>€</td>
    </tr>

    <? 
    foreach ($Events_all as $key => $Event_one) { 
    // display all Job - Break event
        $Break = new time_event(); 
        $Break->id = $Event_one['id'];
        $Break = $Break->view_Break();
      
        //echo $Break->event_start;
        
        $one_row = new time_event();

        if(isset($_GET['edit_id']) and $Event_one['id'] == $_GET['edit_id']){ 
        // display edit-event FORM
          

       ?>

  
      <tr bgcolor="green">
        <td align="right"><h3><?= ++$No; ?></h3><a id="<?= $Event_one['id']; ?>"></a></td>

        <td>
            <input type="date" name="start_date" value="<?= $one_row->view_date($Event_one['event_start']); ?>" id="start_date">
            <br>
            <input type="time" name="start_time" value="<?= $one_row->view_time($Event_one['event_start']); ?>" id="start_time"></td>
        <td>
            <input type="date" name="start_break_date" value="<?= $one_row->view_date($Break['event_start']); ?>" id="start_break_date">
            <br>
            <input type="time" name="start_break_time" value="<?= $one_row->view_time($Break['event_start']); ?>" id="start_break_time"></td>
        <td>
            <input type="date" name="end_break_date" value="<?= $one_row->view_date($Break['event_end']); ?>" id="end_break_date">
            <br>
            <input type="time" name="end_break_time" value="<?= $one_row->view_time($Break['event_end']); ?>" id="end_break_time">
            <input type="hidden" name="id_break" value="<?= $Break['id'] ?>"  id="id_break">
        </td>
        <td>
            <input type="date" name="end_date" value="<?= $one_row->view_date($Event_one['event_end']); ?>" id="end_date">
            <br>
            <input type="time" name="end_time" value="<?= $one_row->view_time($Event_one['event_end']); ?>" id="end_time"></td>
        <td colspan="7" align="right"><h3>Save data</h3></td>
        <td>&nbsp;</td>
        <td>
          <input type="hidden" name="id_job" value="<?= $Event_one['id'] ?>" id="id_job" >
          <input type="hidden" name="userID" value="<?= $_GET['userID'] ?>" >
        <input type="submit" name="edit" id="button" value="submit"></td>
    </tr>
      <? } 
      else {
        // Displpay event data
       ?>
        <tr>
          <td rowspan="3" align="right" valign="top"><?= ++$No; ?><a id="<?= $Event_one['id']; ?>"></a></td>


          <td rowspan="3" align="center" valign="top"><? 
            echo $one_row->date_ddd_dd_mm_aaaa($Event_one['event_start']);
            echo "<br>". $one_row->time_hh_mm($Event_one['event_start']); 
            $PreviousDate = $one_row->view_date($Event_one['event_start']);
            ?></td>

          <td rowspan="3" align="center" valign="top"><? 
          if($Break['event_start']>''){
            if($PreviousDate != $one_row->view_date($Break['event_start']))
            echo $one_row->date_ddd_dd_mm_aaaa($Break['event_start']);
            echo "<br>". $one_row->time_hh_mm($Break['event_start']);
            $PreviousDate = $one_row->view_date($Break['event_start']);}
            ?></td>

          <td rowspan="3" align="center" valign="top"><? 
          if($Break['event_start']>''){
            if($PreviousDate != $one_row->view_date($Break['event_end']))
            echo $one_row->date_ddd_dd_mm_aaaa($Break['event_end']);
            echo "<br>". $one_row->time_hh_mm($Break['event_end']);
            $PreviousDate = $one_row->view_date($Break['event_end']);}
            ?></td>

          <td rowspan="3" align="center" valign="top"><? 
            if($PreviousDate != $one_row->view_date($Event_one['event_end']))
            echo $one_row->date_ddd_dd_mm_aaaa($Event_one['event_end']);
            echo "<br>". $one_row->time_hh_mm($Event_one['event_end']);
            ?></td>



          <td>Morning
            <?
          // Calculate Pay and time of Job event
          $Pay_time = new pay_time();
          $Pay_time->pay_time($Event_one['event_start'], $Event_one['event_end']);
          
          // Calculate Pay (for deduction) and time of a Break event
		      $Break_time = new pay_time();
          $Break_time->pay_time($Break['event_start'], $Break['event_end']);
          
          ?></td>
          <td align="right"><?= $Pay_time->HrMorning ?></td>
          <td align="right"><?= $Pay_time->PaymentMorning ?></td>
          <td align="right"><?= $Break_time->HrMorning ?></td>
          <td align="right"><?= $Break_time->PaymentMorning ?></td>
         

          <td align="right"><? 
    		  $Hr = round($Pay_time->HrMorning - $Break_time->HrMorning,2);
    		  echo $Hr;
    		  $HrTotal = $HrTotal+$Hr; ?></td>
          <td align="right"><? 
    		  $Payment1 = round($Pay_time->PaymentMorning - $Break_time->PaymentMorning,2);
			  $Payment2 = round($Pay_time->PaymentEvening - $Break_time->PaymentEvening,2);
		      $Payment3 = round($Pay_time->PaymentNight - $Break_time->PaymentNight,2);
		  	  echo $Payment1;
    		  $PaymenTotal += $Payment1; ?></td>
          <td rowspan="3" align="right" valign="bottom">&nbsp;<? 
		  echo round($Payment1 + $Payment2 + $Payment3,2);
		  ?></td>

      
          <td rowspan="3"> 
          <a href="index.php?userID=<?= $Event_one['userID']; ?>&delete_id=<?= $Event_one['id']; ?>" onClick="return confirm('are you shure DELETE?')">delete</a> | 
          <a href="index.php?userID=<?= $Event_one['userID']; ?>&edit_id=<?= $Event_one['id']; ?>#<?= $Event_one['id']; ?>">edit</a></td>
        </tr>
        <tr>
          <td>Evening</td>
          <td align="right"><?= $Pay_time->HrEvening ?></td>
          <td align="right"><?= $Pay_time->PaymentEvening ?></td>
          <td align="right"><?= $Break_time->HrEvening ?></td>
          <td align="right"><?= $Break_time->PaymentEvening ?></td>
          <td align="right"><? 
		  $Hr = round($Pay_time->HrEvening- $Break_time->HrEvening,2);
		  echo $Hr;
		  $HrTotal += $Hr; ?></td>
          <td align="right"><? 
		  $Payment2 = round($Pay_time->PaymentEvening - $Break_time->PaymentEvening,2);
		  echo $Payment2;
		  $PaymenTotal += $Payment2; ?></td>
        </tr>
        <tr>
          <td>Night</td>
          <td align="right"><?= $Pay_time->HrNight ?></td>
          <td align="right"><?= $Pay_time->PaymentNight ?></td>
          <td align="right"><?= $Break_time->HrNight ?></td>
          <td align="right"><?= $Break_time->PaymentNight ?></td>
          <td align="right"><? 
		  $Hr = round($Pay_time->HrNight- $Break_time->HrNight,2);
		  echo $Hr;
		  $HrTotal += $Hr; ?></td>
          <td align="right"><? 
		  $Payment3 = round($Pay_time->PaymentNight - $Break_time->PaymentNight,2);
		  echo $Payment3;
		  $PaymenTotal += $Payment3; ?></td>
        </tr>
      <? } ?>
    <? } ?>



<? if(!isset($_GET['edit_id'])){ ?>
    <tr>
      <td><h3>Add</h3></td>

      <td>
          <input type="date" name="start_date" id="textfield">
          <br>
          <input type="time" name="start_time" id="textfield" ></td>
      <td>
          <input type="date" name="start_break_date" id="textfield4">
          <br>
          <input type="time" name="start_break_time" id="textfield4"></td>
      <td>
          <input type="date" name="end_break_date" id="textfield3">
          <br>
          <input type="time" name="end_break_time" id="textfield3"></td>
      <td>
          <input type="date" name="end_date" id="textfield2" >
          <br>
          <input type="time" name="end_time" id="textfield2" ></td>
      <td colspan="7" align="right"> </td>
      <td align="center">&nbsp;</td>

      <td align="center">
        <input type="hidden" name="userID" value="<?= $_GET['userID'] ?>" >
        <input type="submit" name="insert" id="button" value="insert"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><h3></h3></td>
      <td colspan="7" align="right"><h3>Total Worked Hr <?= $HrTotal." Hr" ?></h3></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><h3></h3></td>
      <td colspan="7" align="right"><h3>Total Payment <?= "€ ". $PaymenTotal?></h3></td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <? } ?>
  </tbody>

</table>  
</form>