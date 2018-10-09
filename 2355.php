$testing = true;
$testing = false;

$textASAP = false;
$callASAP = false;

$text_dnc = array();

$pull_date_Start = date("Y-m-d", strtotime("+1 days"));
$pull_date_End = date("Y-m-d", strtotime("+7 weekdays"));
$previousResults = array();
$previousList = array();

$conn = @mysql_connect("localhost","conversions","eyj3PXsV3fughX");
$str_sql = "SELECT location FROM remindme.main_donotcontact WHERE client_id IN (2327,0) AND active = 1 AND type = 'text'";
$query = mysql_query($str_sql, $conn) or die(mysql_error());
while($row = mysql_fetch_assoc($query)) {
    $phoneNumber = normalizePhone($row['location']);
	
	if(!in_array($phoneNumber, $text_dnc)){
		array_push($text_dnc, $phoneNumber);
	}
} 

$conn = mysql_connect("localhost","conversions","eyj3PXsV3fughX");
if (!$conn) { return; }
if (!mysql_select_db('remindme')) { return; }

global $appts;
$str_sql = "SELECT first_name, phone, appt_date
	FROM appointments_list AS a 
	WHERE a.client_id = \"2327\"
	AND a.appt_date >= \"$pull_date_Start\" 
	AND a.appt_date <= \"$pull_date_End\"
	AND a.call_status_id IN (1,2)
	AND a.active=1";

	print "Running Calls Query: \n" . $str_sql . "\n";

	$query = mysql_query($str_sql, $conn) or die(mysql_error());
	while($row = mysql_fetch_assoc($query)) {
		$previousResults['first'] = $row['first_name'];
		$previousResults['phone'] = $row['phone'];
		$previousResults['date'] = $row['appt_date'];
		
		$previousList[] = $previousResults;
	}
	
	echo "Response List\n\n";
	var_dump($previousList);
	echo "Response List\n\n";

$TextMapping = $this->loadMappingFileXLS('2327Texting.xlsx', '2327');
$ApptExcl = $this->loadMappingFileXLS('2327ApptBlacklist.xlsx', '2327');
$ProvExcl = $this->loadMappingFileXLS('2327ProviderBlacklist.xlsx', '2327');
$noArrival = $this->loadMappingFileXLS('2327NoArrival.xlsx', '2327');																	 

if($testing) {
	
	$files = $this->getFiles('2327-*appointmentremindercall*');
}
else {
    $files = $this->getFiles('2327-*appointmentremindercall*');
}

foreach($files as $filename=>$file) {

      echo "Working on File: ".$filename."\n";

	$lines = explode("\n",$file);
	foreach($lines as $key=>$line) {
		if(strlen(trim($line))==0) { continue; }

		$import = true;
		$data = getCSVValues($line, "|");

		$appt = array();
		$appt['client_id'] = '2327';
		$appt['script']  = "std";
		$appt['appt_seq'] = 2;		
		
		$appt['account_num'] = $data[0];		
		$appt['phone'] = normalizePhone($data[2]);        
		$appt['sec_phone'] = normalizePhone($data[3]);
		$dtDate = $data[4];
		$appt['appt_date'] = date("Y-m-d",strtotime($dtDate));
		$dtTime = $data[5];
		$appt['appt_time'] = date("H:i", strtotime($dtTime));
		$appt['first_name'] = trim($data[6]);
		$appt['last_name'] = trim($data[7]);
		$appt['provider_name'] = trim($data[8]);
		$appt['notes'] = trim($data[9]);
		$appt['appt_type'] = trim($data[9]); 	
		$appt['office_code'] = $data[10]; 
		$appt['language'] = $data[13];
		$appt['misc7'] = $appt['office_code']; 
		// SF 00202953 TLJ 11/29/2017
		if(!empty($data[14])){
			$appt['misc_time'] = date("H:i", strtotime($data[14]));
			if($appt['misc_time'] == $appt['appt_time']){ //Only if misc time and arrival time are not the same
				$miscTimeFlag = false;
			}else{
				$miscTimeFlag = true;
			}
			
		}else{
			$miscTimeFlag = false;
		}
		
		//Exclude anyone who already confirmed/cancelled
		foreach($previousList as $ckey => $previousTest){
			if($previousTest['phone'] == $appt['phone'] && $previousTest['first'] == $appt['first_name'] && $previousTest['date']  == $appt['appt_date']){
				echo "{$appt['first_name']} at {$appt['phone']} falsed because already confirmed\n\n";
				$import = false;
			}
			
		}
		
		//Locations that do not use the arrival time regardless
		//SF 00202953 TLJ 12/18/2017
		$arriveLookup = array(); 
		$arriveResult = array(); 
		$arriveLookup[0] = $appt['office_code'];
		$arriveResult = $this->searchMappingArray($noArrival, $arriveLookup);

		if($arriveResult){
			$miscTimeFlag = false;
			//echo "{$appt['office_code']} is excluded from the arrival scripts.\n\n";
		}
		
		if($appt['phone'] == ""){
			$appt['phone'] = $appt['sec_phone'];
			if($appt['phone'] == ""){
				$appt['phone'] = '0000000000';
			}
		}		
		
		$appt['text_phone'] = $appt['phone'];
		$text_phone = $appt['text_phone'];
		//testing
		//******************* TEXTING LOGIC ***********************			
		$RetryLookup = array(); 
		$RetryResult = array(); 
		$RetryLookup[0] = $appt['office_code'];
		$RetryResult = $this->searchMappingArray($TextMapping, $RetryLookup);

		if($RetryResult){
		
				$appt['call_deliver'] = 1;	
				$appt['req_call_date'] = date("Y-m-d");
				$appt['office_name'] = $RetryResult[1];
				$appt['office_phrase'] = $RetryResult[2];
				$appt['caller_id'] = normalizePhone($RetryResult[4]);
				$appt['misc'] = $RetryResult[5];
				$appt['script'] = $RetryResult[9];
				if($miscTimeFlag){
					$appt['script'] .= '_arrive';
				}	  
				$appt['misc2'] = $RetryResult[10];
				$appt['misc3'] = $RetryResult[11];
				if($appt['language'] == "SPANISH"){
					$appt['language'] = "spa";
				}else{
					$appt['language'] = "eng";
				}
				$appt['misc8'] = "Text ".$RetryResult[7]." days out";
				$appt['misc9'] = "Call ".$RetryResult[8]." days out";
				$appt['last_name'] = $appt['last_name']." -" . $RetryResult[8] . "Day Call";
				
			//echo "{$appt['phone']} misc9: {$appt['misc9']} - office name: {$appt['office_name']}\n\n";
		}else{
			$import = false;
		}
		
		
		
		//******************* Date and other Misc Call Logic ********
		
		if($appt['misc9'] == "Call 2 days out"){
			
			if(($appt['appt_date'] == date("Y-m-d",strtotime("+2 weekdays"))) || (date("w") == 4 && $appt['appt_date'] == date("Y-m-d",strtotime("+2 days")))){
				$appt['last_name'] = str_ireplace("-3Day txt","-2Day Call",$appt['last_name']);
				$appt['req_call_date'] = date("Y-m-d");
				$appt['req_call_time'] = date("18:00");
				echo "{$appt['misc9']} for {$appt['phone']} 2 day date logic for {$appt['appt_date']}\n\n";
			}else{$appt['call_deliver'] = 0;}
			
		}elseif($appt['misc9'] == "Call 1 days out"){
			
			if(($appt['appt_date'] == date("Y-m-d",strtotime("+1 weekday"))) || (date("w") == 5 && $appt['appt_date'] == date("Y-m-d",strtotime("+1 day")))){
				$appt['last_name'] = str_ireplace("-2Day txt","-1Day Call",$appt['last_name']);
				$appt['req_call_date'] = date("Y-m-d");
				$appt['req_call_time'] = date("18:00");
				
			}else{$appt['call_deliver'] = 0;}
			
		}elseif($appt['misc9'] == "Call 7 days out"){
			
			if(($appt['appt_date'] == date("Y-m-d",strtotime("+5 weekdays"))) || (date("w") == 1 && $appt['appt_date'] == date("Y-m-d",strtotime("+5 days")))){
				$appt['last_name'] = str_ireplace("-8Day txt","-7Day Call",$appt['last_name']);
				$appt['req_call_date'] = date("Y-m-d");
				$appt['req_call_time'] = date("18:00");
				
			}else{$appt['call_deliver'] = 0;}	
			
		}else{
			//echo "{$appt['misc9']} for {$appt['phone']} falsed on date logic for {$appt['appt_date']}\n\n";
			 $import = false; 
		}

		// 00226506 TLJ 4/3/18 Regional Urology so special
		switch($appt['office_code']){
			case '100060001':
			case '100060001':
			case '100000181':
			case '100056001':
			
				$appt['misc3'] = "We look forward to seeing you " . date("l", strtotime($appt['appt_date']));
				echo "{$appt['account_num']} is at regional urology\n\n";
				if(stripos($appt['provider_name'], 'Springhart') !== false){
					$appt['provider_phrase'] = 'Dr. Patrick Springhart';
				}elseif(stripos($appt['provider_name'], 'Busby') !== false){
					$appt['provider_phrase'] = 'Dr. Joseph E Busby';
				}elseif(stripos($appt['provider_name'], 'Marguet') !== false){
					$appt['provider_phrase'] = 'Dr. Charles Marguet (margee)';
				}elseif(stripos($appt['provider_name'], 'Flanagan') !== false){
					$appt['provider_phrase'] = 'Dr. William Flanagan';
				}elseif(stripos($appt['provider_name'], 'Burgess') !== false){
					$appt['provider_phrase'] = 'Dr. Kimberly Burgess';
				}elseif(stripos($appt['provider_name'], 'Wynia') !== false){
					$appt['provider_phrase'] = 'Dr.  Blake Wynia (Win – e –uh)';	
				}elseif(stripos($appt['provider_name'], 'Maloney') !== false){
					$appt['provider_phrase'] = 'Dr. Kelly Maloney (Muloney)';
				}elseif(stripos($appt['provider_name'], 'French') !== false){
					$appt['provider_phrase'] = 'Michaela French';
				}elseif(stripos($appt['provider_name'], 'Rookstool') !== false){
					$appt['provider_phrase'] = 'Katie Rookstool';
				}elseif(stripos($appt['provider_name'], 'Sinopoli') !== false){
					$appt['provider_phrase'] = 'Olivia Sinopoli (Sin-o-pole e)';
				}elseif(stripos($appt['provider_name'], 'McDaniel') !== false){
					$appt['provider_phrase'] = 'Andie McDaniel';
				}elseif(stripos($appt['provider_name'], 'Low') !== false){
					$appt['provider_phrase'] = 'Danielle Low';
				}elseif(stripos($appt['provider_name'], 'Holweger') !== false){
					$appt['provider_phrase'] = 'Heather Holweger-Garcia (Hall – wig- er – Gar – c – uh)';
				}elseif(stripos($appt['provider_name'], 'Bowles') !== false){
					$appt['provider_phrase'] = 'Ben Bowles';
				}elseif(stripos($appt['provider_name'], 'Brooks') !== false){
					$appt['provider_phrase'] = 'Kristen Brooks';
				//00254128 Added 2 Providers 8/30/18
				}elseif(stripos($appt['provider_name'], 'Dray') !== false){
					$appt['provider_phrase'] = 'Dr. Elizabeth Dray';
				}elseif(stripos($appt['provider_name'], 'Fiscus') !== false){
					$appt['provider_phrase'] = 'Dr. Gabriel Fiscus';	
				}elseif(stripos($appt['provider_name'], 'Hine') !== false){
					$appt['provider_phrase'] = 'Jo Ann Hine';
				}elseif(stripos($appt['provider_name'], 'Lab') !== false){
					echo "{$appt['phone']} is at the lab\n\n";
					$appt['provider_phrase'] = 'the Lab';	
				}elseif(stripos($appt['provider_name'], 'Nurse') !== false){
					echo "{$appt['phone']} is with the nurse\n\n";
					$appt['provider_phrase'] = 'the Nurse';
				// SF 00256686 	
				}elseif(stripos($appt['provider_name'], 'Pelvic Floor') !== false){
					echo "{$appt['phone']} is with pelvic floor\n\n";
					$appt['provider_phrase'] = 'your provider';
				}elseif(stripos($appt['provider_name'], 'Urodynamics') !== false){
					echo "{$appt['phone']} is with the Urodynamics\n\n";
					$appt['provider_phrase'] = 'your provider';		
				}elseif(stripos($appt['provider_name'], 'Six') !== false){
					echo "{$appt['phone']} is with Dr. Six\n\n";
					$appt['provider_phrase'] = 'Dr. Six';	
				}elseif(stripos($appt['provider_name'], 'Blestel') !== false){
					echo "{$appt['phone']} is with Dr. Blestel\n\n";
					$appt['provider_phrase'] = 'Dr. Blestel';	
				}elseif(stripos($appt['provider_name'], 'Yurko') !== false){
					echo "{$appt['phone']} is with Dr. Yurko\n\n";
					$appt['provider_phrase'] = 'Dr. Yurko';		
				}else{
					
					$import = false;
				}

				if(stripos($data[9], 'NEW') !== false){
					$appt['misc2'] = 'If you did not receive new patient paperwork in the mail please arrive 20-30 minutes prior to your scheduled appointment time for new patient registration.';
				}
				
				break;
		}

		if($appt['call_deliver'] == 0 && $appt['text_deliver'] == 0){
			$import = false;
		}
			      
		//Excluded Appts Mapping
		
		$ApptLookup = array(); 
		$ApptResult = array(); 
		$ApptLookup[0] = $appt['appt_type'];
		$ApptResult = $this->searchMappingArray($ApptExcl, $ApptLookup);
		
		if($ApptResult){
			$import = false;
		}
		
		//Excluded Providers Mapping
		
		$ProvLookup = array(); 
		$ProvResult = array(); 
		$ProvLookup[0] = $appt['provider_name'];
		$ProvResult = $this->searchMappingArray($ProvExcl, $ProvLookup);
		
		if($ProvResult){
			$import = false;
		}

			
		if($appt['script'] == "DNC" || $appt['script'] == "DNC_arrive"){
			$import = false;
		}elseif($appt['first_name'] == ""){
			$import = false;
		}elseif($appt['office_name'] == ""){
			$import = false;
        }elseif($appt['text_phone'] == ""){
			echo $appt['first_name'] . ' ' . $appt['last_name'] . " no text phone.\n\n";
			$import = false;
		}      

                
        //ADDED CODE FOR SF#152102 WHERE ALL LAB APPTS BEFORE 9AM SHOULD NOT BE
		//ASKED TO COME IN EARLIER THAN THEIR APPT TIME. JAM 20170324
        if(stripos($appt['provider_name'], 'IMA LAB') !== false || stripos($appt['provider_name'], 'IMA Speciatly Lab') !== false){
		    if(date("H",strtotime($appt['appt_time'])) == "08" || date("H",strtotime($appt['appt_time'])) == "07"){                            
                $appt['misc2'] = "";
                unset($appt['misc2']);                            
            }
		}
		
		//SF 00219780 TLJ 2/8/18 Only Remind Certain Types of Appt for This Office
		$pulmPateAppts = array('CT FOLLOW UP', 'ESTABLISHED PATIENT', 'NEW PATIENT', 'QUICK VISIT', 'DLCO/PFT/LUNG VOLUMES', 'PFT');
		if($appt['office_code'] == '100144001'){
			if(!in_array($appt['notes'], $pulmPateAppts)){
				echo "Appt: {$appt['office_name']} for {$appt['notes']} falsed for {$appt['phone']}\n\n";
				$import = false;
			}
		}
		
		//SF 00245663 
		if($appt['office_code'] == '150001001'){
			if($appt['notes'] == 'EVALUATION'){
				echo "Appt: {$appt['office_name']} for {$appt['notes']} falsed for {$appt['phone']}\n\n";
				$import = false;
			}
		}
		
		//SF 00254847  
		if($appt['office_code'] == '102245001'){
			if(stripos($appt['notes'], 'LAB') !== false){
				echo "Appt: {$appt['office_name']} for {$appt['notes']} falsed for {$appt['phone']}\n\n";
				$import = false;
			}
		}
		
		//SF 00254151 
		if($appt['office_code'] == '270001001'){
			if($appt['provider_name'] == 'Jorge Tolmos'){
				switch($appt['notes']){
					case 'ESTABLISHED PATIENT':
					case 'FOLLOW UP':
					case 'HOSPITAL FOLLOW UP':
					case 'NEW PATIENT':
					case 'POST OP':
						//Do Nothing, these get reminders
						echo "{$appt['phone']} is with {$appt['provider_name']} at {$appt['office_name']} for a {$appt['notes']} appt\n\n";
						break;
					default:
						$import = false;
						
				}
			}else{
				$import = false;
			}
		}
		
		//SF 00249969 Only Call FU Appts for Dr. Mullis 
		if(stripos($appt['provider_name'], 'Mullis') !== false){
			if(stripos($appt['notes'], 'New Patient') !== false || stripos($appt['notes'], 'IMA WORK UP') !== false){
				$import = false;
			}
		}
		
		//SF 00224233 TLJ 3/7/18 Exclude certain appts only for Kidnetics
		$kidneticsIncluded = array('OT EVALUATION','OT EVALUATION18 MO UNDER','OT FEEDING EVAL','OT FEEDING PRE-ASSESSMENT','PT CAR SEAT EVAL','PT EVALUATION','PT PECTUS','PT PECTUS CLINIC','PT PLAGIO/TORT','ST EVALUATION','ST FEEDING EVALUATION','ST FEEDING PRE-ASSESSMENT','ST VOICE EVALUATION','ST VOICE/RESONANCE/VPI EVAL','OT FEEDING EVALUATION');
		$notesCorrected = trim(str_ireplace("Text ", "", $appt['notes']));
		$kidneticsOffices = array('100317001','100318001');
		if(in_array($appt['office_code'], $kidneticsOffices) && !in_array($notesCorrected, $kidneticsIncluded)){
			echo "{$appt['account_num']} for {$appt['last_name']} was excluded because at {$appt['office_name']} for {$appt['notes']}\n\n";
			$import = false;
		}else{
			if($import){
				echo "{$appt['account_num']} was included because at {$appt['office_name']} for {$appt['notes']}\n\n";
			}
			
		}
		
		if($appt['script'] == 'reguro_arrive'){
			$appt['script'] = 'reguro';
		}
			

		if($callASAP && $appt['call_deliver'] == 1) {
			$appt['req_call_date'] = date('Y-m-d');
			$appt['req_call_time'] = date('H:i', strtotime("+15 minutes"));
		}
	
		if($import) {
			$appts[] = $appt;


		}
	}
}



foreach($appts as $key => &$appt) { 
	//Easley
	if(($appt['office_code'] == "100226002" || $appt['office_code'] == "100226000" || $appt['office_code'] == "100226001")){
	//This is the line to edit to include your search options 
	
		//DUPLICATE FILTERING 
		foreach($appts as $second_key => &$record) { 
			if( 
			$record['first_name'] == $appt['first_name'] && 
			$record['last_name'] == $appt['last_name'] && 
			$record['appt_date'] == $appt['appt_date'] && 
			stripos($record['office_code'], '10022600') !== false &&
			$record['appt_time'] > $appt['appt_time'] 
			){ 
				unset($appts[$second_key]); break; 
			}elseif( 
			$record['first_name'] == $appt['first_name'] && 
			$record['last_name'] == $appt['last_name'] && 
			$record['appt_date'] == $appt['appt_date'] && 
			stripos($record['office_code'], '10022600') !== false &&
			$record['appt_time'] < $appt['appt_time'] 
			){ 
				unset($appts[$key]); break; 
			} 
		} 
	} 
	
	//SF 00167892 TLJ 6/5/2017 Filtering By Cancer Center City, all in the same city receive 1 reminder
	//Spartanburg Locations
	if($appt['office_code'] == "100228008" || $appt['office_code'] == "100228001" || $appt['office_code'] == "100228002" || $appt['office_code'] == "100228000" || $appt['office_code'] == "100228003"){
	//This is the line to edit to include your search options 
	
	
		//DUPLICATE FILTERING 
		foreach($appts as $second_key => &$record) { 
			if( 
			$record['first_name'] == $appt['first_name'] && 
			$record['last_name'] == $appt['last_name'] && 
			$record['appt_date'] == $appt['appt_date'] && 
			stripos($record['office_code'], '10022800') !== false &&
			$record['appt_time'] > $appt['appt_time'] 
			){ 
				//echo $appt['first_name'] . ' ' . $appt['last_name'] . ' ' . $record['office_code'] . ' has multiple appts at Spartanburg keeping the earliest.' . "\n\n";
				unset($appts[$second_key]); break; 
			}elseif( 
			$record['first_name'] == $appt['first_name'] && 
			$record['last_name'] == $appt['last_name'] && 
			$record['appt_date'] == $appt['appt_date'] && 
			stripos($record['office_code'], '10022800') !== false &&
			$record['appt_time'] < $appt['appt_time'] 
			){ 
				//echo $appt['first_name'] . ' ' . $appt['last_name'] . ' ' . $record['office_code'] . ' has multiple appts at Spartanburg keeping the earliest.' . "\n\n";
				unset($appts[$key]); break; 
			} 
		} 
	// Faris Locations	
	}elseif($appt['office_code'] == "100224008" || $appt['office_code'] == "100224004" || $appt['office_code'] == "100224002" || $appt['office_code'] == "100224000" || $appt['office_code'] == "100224003"){
	//This is the line to edit to include your search options 
	
	
		//DUPLICATE FILTERING 
		foreach($appts as $second_key => &$record) { 
			if( 
			$record['first_name'] == $appt['first_name'] && 
			$record['last_name'] == $appt['last_name'] && 
			$record['appt_date'] == $appt['appt_date'] && 
			stripos($record['office_code'], '10022400') !== false &&
			$record['appt_time'] > $appt['appt_time'] 
			){ 
				//echo $appt['first_name'] . ' ' . $appt['last_name'] . ' ' . $record['office_code'] . ' has multiple appts at Faris keeping the earliest.' . "\n\n";
				unset($appts[$second_key]); break; 
			}elseif( 
			$record['first_name'] == $appt['first_name'] && 
			$record['last_name'] == $appt['last_name'] && 
			$record['appt_date'] == $appt['appt_date'] && 
			stripos($record['office_code'], '10022400') !== false &&
			$record['appt_time'] < $appt['appt_time'] 
			){ 
				//echo $appt['first_name'] . ' ' . $appt['last_name'] . ' ' . $record['office_code'] . ' has multiple appts at Faris keeping the earliest.' . "\n\n";
				unset($appts[$key]); break; 
			} 
		} 
	// Laurens Locations	
	}elseif($appt['office_code'] == "100481002" || $appt['office_code'] == "100481001"){
	//This is the line to edit to include your search options 
	
	
		//DUPLICATE FILTERING 
		foreach($appts as $second_key => &$record) { 
			if( 
			$record['first_name'] == $appt['first_name'] && 
			$record['last_name'] == $appt['last_name'] && 
			$record['appt_date'] == $appt['appt_date'] && 
			stripos($record['office_code'], '10048100') !== false &&
			$record['appt_time'] > $appt['appt_time'] 
			){ 
				//echo $appt['first_name'] . ' ' . $appt['last_name'] . ' ' . $record['office_code'] . ' has multiple appts at Laurens keeping the earliest.' . "\n\n";
				unset($appts[$second_key]); break; 
			}elseif( 
			$record['first_name'] == $appt['first_name'] && 
			$record['last_name'] == $appt['last_name'] && 
			$record['appt_date'] == $appt['appt_date'] && 
			stripos($record['office_code'], '10048100') !== false &&
			$record['appt_time'] < $appt['appt_time'] 
			){ 
				//echo $appt['first_name'] . ' ' . $appt['last_name'] . ' ' . $record['office_code'] . ' has multiple appts at Laurens keeping the earliest.' . "\n\n";
				unset($appts[$key]); break; 
			} 
		} 
	// Greer Locations	
	}elseif($appt['office_code'] == "100225001" || $appt['office_code'] == "100225002" || $appt['office_code'] == "100225000" || $appt['office_code'] == "100225003"){
	//This is the line to edit to include your search options 
	
	
		//DUPLICATE FILTERING 
		foreach($appts as $second_key => &$record) { 
			if( 
			$record['first_name'] == $appt['first_name'] && 
			$record['last_name'] == $appt['last_name'] && 
			$record['appt_date'] == $appt['appt_date'] &&
			stripos($record['office_code'], '10022500') !== false &&			
			$record['appt_time'] > $appt['appt_time'] 
			){ 
				//echo $appt['first_name'] . ' ' . $appt['last_name'] . ' ' . $record['office_code'] . ' has multiple appts at Greer keeping the earliest.' . "\n\n";
				unset($appts[$second_key]); break; 
			}elseif( 
			$record['first_name'] == $appt['first_name'] && 
			$record['last_name'] == $appt['last_name'] && 
			$record['appt_date'] == $appt['appt_date'] &&
			stripos($record['office_code'], '10022500') !== false &&
			$record['appt_time'] < $appt['appt_time'] 
			){ 
				//echo $appt['first_name'] . ' ' . $appt['last_name'] . ' ' . $record['office_code'] . ' has multiple appts at Greer keeping the earliest.' . "\n\n";
				unset($appts[$key]); break; 
			} 
		} 
	// Seneca Locations	
	}elseif($appt['office_code'] == "100223004" || $appt['office_code'] == "100223002" || $appt['office_code'] == "100223000" || $appt['office_code'] == "100223003"){
	//This is the line to edit to include your search options 
	
	
		//DUPLICATE FILTERING 
		foreach($appts as $second_key => &$record) { 
			if( 
			$record['first_name'] == $appt['first_name'] && 
			$record['last_name'] == $appt['last_name'] && 
			$record['appt_date'] == $appt['appt_date'] && 
			stripos($record['office_code'], '10022300') !== false &&
			$record['appt_time'] > $appt['appt_time'] 
			){ 
				//echo $appt['first_name'] . ' ' . $appt['last_name'] . ' ' . $record['office_code'] . ' has multiple appts at Seneca keeping the earliest.' . "\n\n";
				unset($appts[$second_key]); break; 
			}elseif( 
			$record['first_name'] == $appt['first_name'] && 
			$record['last_name'] == $appt['last_name'] && 
			$record['appt_date'] == $appt['appt_date'] && 
			stripos($record['office_code'], '10022300') !== false &&
			$record['appt_time'] < $appt['appt_time'] 
			){ 	
				//echo $appt['first_name'] . ' ' . $appt['last_name'] . ' ' . $record['office_code'] . ' has multiple appts at Seneca keeping the earliest.' . "\n\n";
				unset($appts[$key]); break; 
			} 
		} 
	}		
//SF 00254704 Eastside Locations
	elseif($appt['office_code'] == "100222002" || $appt['office_code'] == "100222000" || $appt['office_code'] == "100222004" || $appt['office_code'] == "100222005"){
	//This is the line to edit to include your search options 
	
	
		//DUPLICATE FILTERING 
		foreach($appts as $second_key => &$record) { 
			if( 
			$record['first_name'] == $appt['first_name'] && 
			$record['last_name'] == $appt['last_name'] && 
			$record['appt_date'] == $appt['appt_date'] && 
			stripos($record['office_code'], '10022200') !== false &&
			$record['appt_time'] > $appt['appt_time'] 
			){ 
				//echo $appt['first_name'] . ' ' . $appt['last_name'] . ' ' . $record['office_code'] . ' has multiple appts at Seneca keeping the earliest.' . "\n\n";
				unset($appts[$second_key]); break; 
			}elseif( 
			$record['first_name'] == $appt['first_name'] && 
			$record['last_name'] == $appt['last_name'] && 
			$record['appt_date'] == $appt['appt_date'] && 
			stripos($record['office_code'], '10022200') !== false &&
			$record['appt_time'] < $appt['appt_time'] 
			){ 	
				//echo $appt['first_name'] . ' ' . $appt['last_name'] . ' ' . $record['office_code'] . ' has multiple appts at Seneca keeping the earliest.' . "\n\n";
				unset($appts[$key]); break; 
			} 
		} 
	}
	// SF 00254166 Grove
	elseif($appt['office_code'] == "100221000" || $appt['office_code'] == "100221002" || $appt['office_code'] == "100221001"){
	//This is the line to edit to include your search options 
	
	
		//DUPLICATE FILTERING 
		foreach($appts as $second_key => &$record) { 
			if( 
			$record['first_name'] == $appt['first_name'] && 
			$record['last_name'] == $appt['last_name'] && 
			$record['appt_date'] == $appt['appt_date'] && 
			stripos($record['office_code'], '10022100') !== false &&
			$record['appt_time'] > $appt['appt_time'] 
			){ 
				//echo $appt['first_name'] . ' ' . $appt['last_name'] . ' ' . $record['office_code'] . ' has multiple appts at Seneca keeping the earliest.' . "\n\n";
				unset($appts[$second_key]); break; 
			}elseif( 
			$record['first_name'] == $appt['first_name'] && 
			$record['last_name'] == $appt['last_name'] && 
			$record['appt_date'] == $appt['appt_date'] && 
			stripos($record['office_code'], '10022100') !== false &&
			$record['appt_time'] < $appt['appt_time'] 
			){ 	
				//echo $appt['first_name'] . ' ' . $appt['last_name'] . ' ' . $record['office_code'] . ' has multiple appts at Seneca keeping the earliest.' . "\n\n";
				unset($appts[$key]); break; 
			} 
		} 
	}
} 



if($testing) {
	var_dump($appts);
	$appts = array();
}