<?php 

$token = "ENTER YOUR TOKEN HERE";
$project_id = "ENTER YOUR PROJECT_ID HERE";
$query = "ENTER YOUR ARRAY OF QUERIES HERE";

// EXEC Query
$result = json_encode(curl_multi_query($token,$project_id,$query));

// Return Results
print_r($result);

function curl_multi_query($token,$project_id,$query)
{
	for ($i = 0; $i < count($query); $i++) {
			$url = 'https://app.cooladata.com/api/v2/projects/'.$project_id.'/cql/';
			${"curl".$i} = curl_init();
			curl_setopt(${"curl".$i},CURLOPT_URL, $url); 
			curl_setopt(${"curl".$i},CURLOPT_POST, 1); 
			curl_setopt(${"curl".$i},CURLOPT_POSTFIELDS, 'tq='.urlencode($query[$i]));
			curl_setopt(${"curl".$i},CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt(${"curl".$i}, CURLINFO_HEADER_OUT, 1);
			curl_setopt(${"curl".$i}, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt(${"curl".$i},CURLOPT_HTTPHEADER, array('Authorization: Token '.$token, 'Accept: application/json'));
	}
	
	$mh = curl_multi_init();
	$results = Array();
	if($mh) {
        for ($i = 0; $i < count($query); $i++) {
			curl_multi_add_handle($mh,${"curl".$i});
		}
		
		$active = null;
		//execute the handles
		do {
			$mrc = curl_multi_exec($mh, $active);
		} while ($mrc == CURLM_CALL_MULTI_PERFORM);

		while ($active && $mrc == CURLM_OK) {
			if (curl_multi_select($mh) == -1) {
				usleep(10000);
			}
			do {
				$mrc = curl_multi_exec($mh, $active);
			} while ($mrc == CURLM_CALL_MULTI_PERFORM);
		}
		
		for ($j = 0; $j < count($query); $j++) {
			array_push($results, str_replace("integer","number",curl_multi_getcontent(${"curl".$j})));
			curl_multi_remove_handle($mh, ${"curl".$j});
		}
		curl_multi_close($mh);
		
		return $results;
	} else {
			return  "Connection Error, Please Try Again.";
	}
}

?>

