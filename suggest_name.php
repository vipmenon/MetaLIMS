<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include ('database_connection.php');

/* Connect to database and set charset to UTF-8 */
if($dbc->connect_error) {
  echo 'Database connection failed...' . 'Error: ' . $dbc->connect_errno . ' ' . $dbc->connect_error;
  exit;
} else {
  $dbc->set_charset('utf8');
}


/* retrieve the search term that autocomplete sends */
/*$term = trim(strip_tags($_REQUEST['term'])); 
$term = htmlspecialchars($term);
#$term = '2014';
$a_json = array();
$a_json_row = array();
$data = array();
if ($data2 = $dbc->query("SELECT * FROM sample WHERE sample_name LIKE '%$term%' ORDER BY sample_name LIMIT 10")) {
	while($row = mysqli_fetch_array($data2)) {
		
		$data[] = array(
			'label' => $row['sample_name'],
			'value' => $row['sample_name']
		);								
	}
}
// jQuery wants JSON data
echo json_encode($data);
flush();
 
$dbc->close();
*/

///
#$param = "%2014%";
#$param = "%Vaccum%";
#$param = "%Coil%";
$term = trim(strip_tags($_REQUEST['term'])); 
$term = htmlspecialchars($term);
#$param = "{$term}%"; 
$param = "%{$term}%";

//add sample names
$stmt = $dbc->prepare("SELECT * FROM sample WHERE sample_name LIKE ? ORDER BY sample_name LIMIT 10");
$stmt->bind_param("s", $param);

$data = array();
if ($stmt->execute()){
	    			
	if($stmt->fetch()){
		$meta = $stmt->result_metadata(); 
   		while ($field = $meta->fetch_field()){ 
        	$params[] = &$row[$field->name]; 
    	} 

    	call_user_func_array(array($stmt, 'bind_result'), $params); 
		$stmt->execute();
    	while ($stmt->fetch()) {	
			$data[] = array(
				'label' => $row['sample_name'],
				'value' => $row['sample_name']
			);				
		}
	}
}

//add Sampler names 
$stmt2 = $dbc->prepare("SELECT * FROM sampler WHERE sampler_name LIKE ? ORDER BY sampler_name LIMIT 10");
$stmt2->bind_param("s", $param);
if ($stmt2->execute()){
	    			
	if($stmt2->fetch()){
		$meta2 = $stmt2->result_metadata(); 
   		while ($field2 = $meta2->fetch_field()){ 
        	$params2[] = &$row[$field2->name]; 
    	} 

    	call_user_func_array(array($stmt2, 'bind_result'), $params2); 
		$stmt2->execute();
    	while ($stmt2->fetch()) {
			$data[] = array(
				'label' => $row['sampler_name'],
				'value' => $row['sampler_name']
			);				
		}
	}
}

//add Project names 
$stmt3 = $dbc->prepare("SELECT * FROM project_name WHERE project_name LIKE ? ORDER BY project_name LIMIT 10");
$stmt3->bind_param("s", $param);

if ($stmt3->execute()){
	    			
	if($stmt3->fetch()){
		$meta3 = $stmt3->result_metadata(); 
   		while ($field3 = $meta3->fetch_field()){ 
        	$params3[] = &$row[$field3->name]; 
    	} 

    	call_user_func_array(array($stmt3, 'bind_result'), $params3); 
		$stmt3->execute();
    	while ($stmt3->fetch()) {
			$data[] = array(
				'label' => $row['project_name'],
				'value' => $row['project_name']
			);				
		}
	}
}

//add Daily Dates
$stmt4 = $dbc->prepare("SELECT * FROM daily_data2 WHERE daily_date LIKE ? ORDER BY daily_date LIMIT 10");
$stmt4->bind_param("s", $param);

if ($stmt4->execute()){
	    			
	if($stmt4->fetch()){
		$meta4 = $stmt4->result_metadata(); 
   		while ($field4 = $meta4->fetch_field()){ 
        	$params4[] = &$row[$field4->name]; 
    	} 

    	call_user_func_array(array($stmt4, 'bind_result'), $params4); 
		$stmt4->execute();
    	while ($stmt4->fetch()) {
			$data[] = array(
				'label' => $row['daily_date'],
				'value' => $row['daily_date']
			);				
		}
	}
}

echo json_encode($data);
flush();
 
$dbc->close();
?>