<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('database_connection.php'); 
#include('index.php');
////choose which files to include depending on if you are exporting an xls or not.
//xls cannot include the index file because it will send headers too early (and the wrong ones)
if((isset($_GET['db_content'])) && ($_GET['db_content'] == 'xls' || $_GET['db_content'] == 'xls_isolates')){
	include('functions/build_xls_output_table.php');
}
elseif(isset($_GET['db_content']) && ($_GET['db_content'] == 'bulk_dna')){
	include('index.php');
	include('functions/build_bulk_dna_table.php');
}
elseif(isset($_GET['db_content']) && ($_GET['db_content'] == 'seq_sub')){
	include('index.php');
	include('functions/build_bulk_seqSub_table.php');

}
elseif(isset($_GET['db_content']) && ($_GET['db_content'] == 'read_sub')){
	include('index.php');
	include('functions/build_bulk_read_sub_id_table.php');
}
elseif(isset($_GET['db_content']) && ($_GET['db_content'] == 'update_read_sub')){
	include('index.php');
	include('functions/build_bulk_read_sub_id_update_table.php');
}
else{
	include('index.php');
	include('functions/build_table.php');

}
?>

<!doctype html>
<html>
<head>
<title>Query Results</title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="freeze/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="freeze/dataTables.bootstrap.css">
	<style type="text/css" class="init">
	body { font-size: 140%; }
	div.dataTables_wrapper {
		width: 99%;
		height: 100%
		margin: 0 auto;
	}
	div.dataTables_scrollBody {
		/*height: 90%*/
	}
	</style>
	<script type="text/javascript" language="javascript" src="freeze/jquery.js"></script>
	<script type="text/javascript" language="javascript" src="freeze/jquery.dataTables.js"></script>
	<script type="text/javascript" language="javascript" src="freeze/dataTables.fixedColumns.js"></script>
	<script type="text/javascript" language="javascript" src="freeze/dataTables.bootstrap.js"></script>
	<script type="text/javascript" language="javascript" class="init">
		$(document).ready(function() {
			var table = $('#example').DataTable( {
				scrollY:        "90%", 
				scrollX:        true,
				scrollCollapse: true,
				paging:         false
			} );
			new $.fn.dataTable.FixedColumns( table );
		} );
	</script>
	
	 <script type="text/javascript">
        $(document).ready(function() {
            //$('#btnHide').click(function() {
            $('.reg').dblclick(function() {
				var index1 = $(this).index() + 1;
                $("td:nth-child(" +index1+ ")").toggleClass('hidden');
                $("th:nth-child(" +index1+ ")").toggleClass('hidden');
            });
        });
    </script>
</head>

 
<body class="dt-example">
<?php

if(isset($_GET['submit'])){
	include('functions/check_box_tables_output.php');
	include('functions/white_list.php');
	
	$submit = $_GET['submit'];
	
	//sample
	if($submit == 'sample'){
		$check_date = 'false';
		$check_field = 'false';
		$query_date = '';
		$query_field = '';
		$stmt = '';
		if(($_GET['smydate'] != NULL) && ($_GET['emydate'] != NULL)){
		
			//sanatize user input to make safe for browser
			$p_smydate = htmlspecialchars($_GET['smydate']);
			$p_emydate = htmlspecialchars($_GET['emydate']);
		
			//make sure you cover the entire day
			$p_smydate = $p_smydate.' 00:00:00';
			$p_emydate = $p_emydate.' 23:59:00';
			#$query_date = ' start_samp_date_time BETWEEN (?) AND (?)';
			//$query_date = ' sample.start_samp_date_time BETWEEN (?) AND (?)';
			$query_date = ' sample_air_sampler.start_date_time BETWEEN (?) AND (?)';
			$check_date = 'true';
		}
			
		if(($_GET['field'] != '0') && isset($_GET['query'])){
			$p_field = htmlspecialchars($_GET['field']);
			$p_query_basis = htmlspecialchars($_GET['query']);
			//check whitelist for p_field
			$p_field_check = whiteList($p_field, 'column');
			if($p_field_check == 'true'){
				if($p_field == 'air_sampler_name'){
					$query_field = " sample_air_sampler.$p_field = (?)";
				}
				else{
					$query_field = " sample.$p_field = (?)";
				}
				
				#$query_field = " $p_field = (?)";
				$check_field = 'true';
			}
		}
				
		if(isset($_GET['column_names'])){$field_names = check_box_results($_GET['column_names']);}
		elseif(isset($_GET['db_content']) && $_GET['db_content'] == 'bulk_dna'){
			$field_names = 'sample_name,d_conc,sample_sort';
		}
		else{$field_names = "*";}
		#$query = "SELECT $field_names FROM sample WHERE $p_field = (?) AND start_samp_date_time BETWEEN (?) AND (?)";
		//$query_main = "SELECT $field_names FROM sample WHERE";
		#$query_main = "SELECT $field_names FROM sample LEFT JOIN isolates ON isolates.sample_name = sample.sample_name WHERE";
		//note: not sure why this is working to show correct sample names instead of the one on top
		#$query_main = "SELECT $field_names FROM isolates RIGHT JOIN sample ON isolates.sample_name = sample.sample_name WHERE";
		
		
		if(isset($_GET['db_content']) && $_GET['db_content'] == 'sensor'){
			#$query_main = "SELECT * FROM sample JOIN daily_data2 ON sample.daily_data = daily_data2.daily_date WHERE ";
			//trying to make it so you don't have to manually link the daily date to the sample entry...this should do it
			$query_main = "SELECT * FROM sample JOIN daily_data2 ON DATE(sample.start_samp_date_time) = daily_data2.daily_date WHERE ";
		}
		elseif(isset($_GET['db_content']) && $_GET['db_content'] == 'read_sub'){
			#$query_main = "SELECT * FROM sample JOIN read_submission ON read_submission.sample_name = sample.sample_name WHERE ";
			$query_main = "SELECT sample.sample_name,sample.sample_num,sample.sample_sort,sample.seq_id,read_submission.subm_id,read_submission.subm_db,read_submission.subm_date,read_submission.submitter,read_submission.type_exp FROM sample LEFT JOIN read_submission ON read_submission.sample_name = sample.sample_name WHERE ";
			#$query_main = "SELECT * FROM sample WHERE ";
		}
		elseif(isset($_GET['db_content']) && ($_GET['db_content'] == 'view_read_sub' || $_GET['db_content'] == 'update_read_sub')){
			$query_main = "SELECT sample.sample_name,sample.sample_sort,sample.seq_id,read_submission.subm_id,read_submission.subm_db,read_submission.subm_date,read_submission.submitter,read_submission.type_exp FROM sample RIGHT JOIN read_submission ON read_submission.sample_name = sample.sample_name WHERE ";
		}
		elseif(isset($_GET['db_content']) && ($_GET['db_content'] == 'isolates' || $_GET['db_content'] == 'xls_isolates')){
			$query_main = "SELECT 
			sample.sample_name,
			sample.sample_sort,
			sample.collector_name,
			sample.start_samp_date_time,
			sample.sample_type,
			sample.location_name,
			isolates.loc_type,
			sample.media_type,
			isolates.iso_coll_temp,
			isolates.iso_date,
			isolates.iso_store_method,
			isolates.seq_sang,
			isolates.closest_hit,
			isolates.send_pac_bio
			FROM sample JOIN isolates ON isolates.sample_name = sample.sample_name WHERE (sample.sample_type = 'F' OR sample.sample_type = 'BC') AND ";
		}
		else{
			//$query_main = "SELECT $field_names FROM sample WHERE";
			//add new air sampler table
			$query_main = "SELECT $field_names FROM sample JOIN sample_air_sampler ON sample_air_sampler.sample_name  = sample.sample_name WHERE";
		}
		$query = "";
		$query_add = "";
		
		if($check_field == 'true' && $check_date == 'false'){//only query field populated
			$query = $query_main.$query_field;
			$query_add = $query_field;
			$stmt = $dbc->prepare($query);
			$stmt -> bind_param('s', $p_query_basis);
		}
		elseif ($check_field == 'false' && $check_date == 'true') {//only date is populated
			$query = $query_main.$query_date;
			$query_add = $query_date;
			$stmt = $dbc->prepare($query);
			$stmt -> bind_param('ss',$p_smydate , $p_emydate);
		}
		elseif ($check_field == 'true' && $check_date == 'true') {//date and query fields are populated
			$query = $query_main.$query_field.' AND '.$query_date;
			$query_add = $query_field.' AND '.$query_date;
			$stmt = $dbc->prepare($query);
			$stmt -> bind_param('sss', $p_query_basis, $p_smydate , $p_emydate);
		}
		else{
			echo '<script>Alert.render("ERROR: No entries found. Please check fields");</script>';
			echo '<input action="action" class="btn btn-success" type="button" value="Go Back" onclick="history.go(-1);" />';
		}
		
		
	

		if(isset($_GET['db_content']) && ($_GET['db_content'] == 'xls' || $_GET['db_content'] == 'xls_isolates')){
			build_xls_output_table($stmt);
		}
		elseif(isset($_GET['db_content']) && $_GET['db_content'] == 'bulk_dna'){
			build_bulk_dna_table($stmt);
		}
		elseif(isset($_GET['db_content']) && $_GET['db_content'] == 'seq_sub'){
			echo '<script>Alert.render("ERROR:Current Page Under Construction.");</script>';
			echo '<input action="action" style = "float: left"class="button" type="button" value="Go Back" onclick="history.go(-1);" />';
			//build_bulk_seqSub_table($stmt);
		}
		elseif(isset($_GET['db_content']) && $_GET['db_content'] == 'read_sub'){
			build_bulk_read_sub_id_table($stmt);
		}
		elseif(isset($_GET['db_content']) && $_GET['db_content'] == 'update_read_sub'){
			build_bulk_read_sub_id_update_table($stmt);
		}
		else{
			if($stmt){
				build_table($stmt,'display',$dbc);
			}
		}
	}


	if($submit == 'other'){
		//project
		if($_GET['db_content']== 'project_all'){
			$stmt = $dbc->prepare("SELECT * FROM project_name");
			build_table($stmt,'display');
		}
		
		//weather
		if($_GET['db_content']=='weather_xls'){
					#echo '<body class="dt-example">'; //you can use this or no?
					$stmt = $dbc->prepare("SELECT * FROM daily_weather");
					build_xls_output_table($stmt,'xls');
					echo "</body>";
					echo "</html>";
		}
		if($_GET['db_content']=='weather_all'){
			$stmt = $dbc->prepare("SELECT * FROM daily_weather");
			build_table($stmt,'display');
		}
		
		//daily data
		if($_GET['db_content']=='daily_data_xls'){
			$sdate = htmlspecialchars($_GET['sdate']);
			$edate = htmlspecialchars($_GET['edate']);
			$stmt = $dbc->prepare("SELECT daily_data2_particle_counter.daily_date,daily_data2_particle_counter.part_sens_name,daily_data2_particle_counter.start_time,daily_data2_particle_counter.end_time,daily_data2.temp,daily_data2.hum,daily_data2.co2,daily_data2.rain,daily_data2.notes,daily_data2.entered_by,daily_data2.updated_by,daily_data2.update_timestamp FROM daily_data2_particle_counter LEFT JOIN daily_data2 ON (daily_data2_particle_counter.daily_date = daily_data2.daily_date) WHERE daily_data2_particle_counter.daily_date BETWEEN (?) AND (?)");
			$stmt -> bind_param('ss', $sdate,$edate);
			build_xls_output_table($stmt,'xls');
			echo "</body>";
			echo "</html>";
		}
		/*//depricated
		 * if($_GET['db_content']=='daily_data_all'){
			$sdate = htmlspecialchars($_GET['sdate']);
			$edate = htmlspecialchars($_GET['edate']);
			//$stmt = $dbc->prepare("SELECT daily_data2_particle_counter.daily_date,daily_data2_particle_counter.part_sens_name,daily_data2_particle_counter.start_time,daily_data2_particle_counter.end_time,daily_data2.temp,daily_data2.hum,daily_data2.co2,daily_data2.rain,daily_data2.notes,daily_data2.entered_by,daily_data2.updated_by,daily_data2.update_timestamp FROM daily_data2_particle_counter LEFT JOIN daily_data2 ON (daily_data2_particle_counter.daily_date = daily_data2.daily_date) WHERE daily_data2_particle_counter.daily_date BETWEEN (?) AND (?)");
			$stmt = $dbc->prepare("SELECT * FROM daily_data2 JOIN daily_data2_particle_counter ON daily_data2.daily_date = daily_data2_particle_counter.daily_date");
			
			$stmt -> bind_param('ss', $sdate,$edate);
			build_table($stmt);
		}
		*/
		//air samplers
		if($_GET['db_content']=='airSampler_all'){
			$stmt = $dbc->prepare("SELECT * FROM air_sampler");
	    	build_table($stmt,'display');
		}
		
		//sensors
		if($_GET['db_content']=='partCt_all'){
			$stmt = $dbc->prepare("SELECT * FROM particle_counter");
			build_table($stmt,'display');
		}
	}

}
?>

</body>
</html>