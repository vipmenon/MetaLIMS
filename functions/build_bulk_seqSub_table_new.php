<?php	

//display table
function build_bulk_seqSub_table_new($array_sample_names,$sample_type,$container_type,$root){
	$path = $_SERVER['DOCUMENT_ROOT'].$root;
	include($path.'functions/convert_time.php');
	include($path.'functions/text_insert_update.php');

	echo '<pre style="width:90%;margin-left:5%;">';
	echo '*Notice: Bulk Update Cannot Be Bulk Edited At This Time. Please Double Check Entries';
	echo '</pre>';
	echo '<form class="registration" onsubmit="return validate(this)" action="seqSub_bulk_update_new.php" method="POST">';
	echo '<div>';

	echo '<table id = "datatable_bulk" style="width:90%;margin-left:5%; background-color:white">';

	echo '<thead>';
	echo '<tr>';
	echo '<th>Sample Name</th>';
	if($container_type != 'Tube'){
		echo '<th>Well Location</th>';
	}
	echo '<th  >Sample Conc. (ng/uL)</th>';
	echo '<th  >Volume Of Aliquot(uL)</th>';
	echo '<th  >Sample Buffer<br></th>';

	echo '<th  >Does Orig DNA/RNA Sample Still Exist?<br><label class="checkbox-label"><input type="checkbox" id="selecctallyes"/>Yes</label><label class="checkbox-label"><input type="checkbox" id="selecctallno"/>No</label></th>';
	echo '<th  >Sequencing ID</th>';
	
	echo '</tr>';
	echo '</thead>';					
	echo '<tbody>';

	$arrlength = count($array_sample_names);

	$new_array = array();
	$seq_id_array = array();
	for($x = 0; $x < $arrlength; $x++) {
		$pieces = explode(':', $array_sample_names[$x]);
		$sample_name = $pieces[0];
		$sample_sort = $pieces[1];
		$seq_id = $pieces[2];
		$seq_id_array[$sample_name] = $seq_id;
		$new_array[$sample_name] = $sample_sort;
	}

	foreach($new_array as $key => $value){
		
		$data[] = array('sample_name' => $key, 'sample_sort' => $value, 'seq_id' => $seq_id_array[$key]);
		$key= preg_replace("/\//",'-',$key);
		$mod_data[] = array('sample_name' => $key, 'sample_sort' => $value);
	}
?>
	<script type="text/javascript">var js_array = <?php echo json_encode($mod_data); ?>;</script>
<?php
	// Obtain a list of columns
	foreach ($data as $key => $row) {
		$s_sort[$key]  = $row['sample_sort'];
		$s_name[$key] = $row['sample_name'];
	}
			
	// Sort the sample names for output
	// Add $data as the last parameter, to sort by the common key
	array_multisort($s_sort, SORT_ASC, $s_name, SORT_ASC, $data);

	foreach($data as $key => $row){
		echo '<tr>';
		$p_value = htmlspecialchars($row['sample_name']);
		$sid = htmlspecialchars($row['seq_id']);
		$sample_name = $p_value;
		$mod_sample_name = preg_replace("/\//",'-',$sample_name);//jQuery cannot use slashes
		$mod_sample_name = preg_replace("/\s+/",'-',$mod_sample_name);//jQuery can also not use spaces
?>
		<td>
 		<!--<input type="checkbox" class = "checkbox1" id="<?php echo $mod_sample_name;?>_checkbox" name="sample[<?php echo $sample_name; ?>][checkbox]" value="checked" <?php if (isset($_SESSION['submitted']) && $_SESSION['submitted'] == 'false') {if(isset($_SESSION['sample_array'][$sample_name]['checkbox']) && htmlspecialchars($_SESSION['sample_array'][$sample_name]['checkbox']) == 'checked'){echo "checked";}}?>/> <?php echo $sample_name ?><br />
		-->
		<?php echo $mod_sample_name;?>
		</td>
		<?php
		$dna_conc = '';
		$vol = '';


		if($container_type != 'Tube'){
		
			?>
			<td><input type="text" class = "bulkfields" id="<?php echo $mod_sample_name;?>_wellLoc" name="sample[<?php echo $sample_name; ?>][wellLoc]" value="<?php if (isset($_SESSION['submitted']) && $_SESSION['submitted'] == "false") {echo htmlspecialchars($_SESSION['sample_array'][$sample_name]['wellLoc']);}?>"></td>
			<?php 
		} 
		?>
		
		
		<td><input type="text" class = "bulkfields" id="<?php echo $mod_sample_name;?>_sampConc" name="sample[<?php echo $sample_name; ?>][sampConc]" value="<?php if (isset($_SESSION['submitted']) && $_SESSION['submitted'] == 'false') {echo htmlspecialchars($_SESSION['sample_array'][$sample_name]['sampConc']);}?>"></td>
		<td><input type="text" class = "bulkfields" id="<?php echo $mod_sample_name;?>_vol" name="sample[<?php echo $sample_name; ?>][vol]" value="<?php if (isset($_SESSION['submitted']) && $_SESSION['submitted'] == 'false') {echo htmlspecialchars($_SESSION['sample_array'][$sample_name]['vol']);}else{echo $vol;}?>"></td>
		<td><input type="text" class = "bulkfields" id="<?php echo $mod_sample_name;?>_sampBuffer" name="sample[<?php echo $sample_name; ?>][sampBuffer]" value="<?php if (isset($_SESSION['submitted']) && $_SESSION['submitted'] == 'false') {echo htmlspecialchars($_SESSION['sample_array'][$sample_name]['sampBuffer']);}?>"></td>
	
		<?php
		if($sample_type == 'Purified small RNA' || $sample_type == 'Purified mRNA' || $sample_type == 'Total RNA'){ ?>
				<td><input type="text" class = "bulkfields" id="<?php echo $mod_sample_name;?>_dnaCont" name="sample[<?php echo $sample_name; ?>][dnaCont]" value="<?php if (isset($_SESSION['submitted']) && $_SESSION['submitted'] == 'false') {echo htmlspecialchars($_SESSION['sample_array'][$sample_name]['dnaCont']);}?>"></td>
				<td><input type="text" class = "bulkfields" id="<?php echo $mod_sample_name;?>_RIN" name="sample[<?php echo $sample_name; ?>][RIN]" value="<?php if (isset($_SESSION['submitted']) && $_SESSION['submitted'] == 'false') {echo htmlspecialchars($_SESSION['sample_array'][$sample_name]['RIN']);}?>"></td>
		<?php }?>			
		<td>
		<div id="<?php echo $mod_sample_name;?>_color_checkbox_div" style="margin:2px 0px 10px 10px;height:34px;border-radius: 5px;text-indent: 0.01;">
		<label class="checkbox-label"><input type="radio" id="<?php echo $mod_sample_name;?>_exists_yes"  class = "checkbox2" name="sample[<?php echo $sample_name; ?>][exists]" value="one" <?php if (isset($_SESSION['submitted']) && $_SESSION['submitted'] == 'false') {if(isset($_SESSION['sample_array'][$sample_name]['exists']) && htmlspecialchars($_SESSION['sample_array'][$sample_name]['exists']) == 'one'){echo "checked='checked'";}}?>">Yes</label>			
		<label class="checkbox-label"><input type="radio" id="<?php echo $mod_sample_name;?>_exists_no"  class = "checkbox3" name="sample[<?php echo $sample_name; ?>][exists]" value="three" <?php if (isset($_SESSION['submitted']) && $_SESSION['submitted'] == 'false') {if(isset($_SESSION['sample_array'][$sample_name]['exists']) && htmlspecialchars($_SESSION['sample_array'][$sample_name]['exists']) == 'three'){echo "checked='checked'";}}?>">No</label>
		</div>
		</td>
		<td><input  type="text" class = "bulkfields" id="<?php echo $mod_sample_name;?>_seq_id" name="sample[<?php echo $sample_name; ?>][seq_id]" value="<?php echo $sid ?>"></td>
		
		</tr>
		<?php } ?>
   		</tbody>
		</table>
		
		<input type='submit' id="sub" class = "button" name ="submit" value='Update Samples' />
		</form>

		</div>		

		<?php }?>
																																															 
		<!---------form validation----->
		<!--see if anything is not filled in. If it is not, color field #f9ae7d and don't let the user submit-->
					
		<script type="text/javascript">
		   function validate(from) {
                           
                           //if you tried to submit, check the entire page for color?
                           //return valid is false if you find it
                           
                           var valid = 'true';
                               if(check_form() == 'false'){
                                  valid = 'false';    
                               }
                               if(valid == 'false'){
                                  alert('ERROR: Some inputs are invalid. Please check fields');
                                  return false;
                               }
                               else{
                                  return confirm('Are you sure you want to submit?');
                               }
                           }
                           
                           function check_form(){
                                 var index;
                                 var a = js_array;
                                 var valid = 'true';
                                 
                                 var inputs = document.getElementsByTagName("input");
                                 var txt = "";
                                 var i;
                                 for (i = 0; i < inputs.length; i++) {
                                     txt = inputs[i].value;
                                     
                                     //check if your input is empty
                                     var n = txt.length;
                                     if(n == 0){
                                     	var input_type = inputs[i].getAttribute("type");
                                     	if(input_type != 'search' ){
                                     		inputs[i].style.background = "#f9ae7d";
                                       	 	valid = 'false';
                                     	}
                                     	
                                       
                                        //validate numbers and decimal places?
                                     }
                                     else{
                                     	//if you were not empty, go ahead and check that the format is correct
                                     	var name = inputs[i].getAttribute("name");
                                   		//alert(name);
                                   		
										//well location
	                                     var pattern1 = /.*\[wellLoc\]$/;
	                                     if(pattern1.test(name)){
	                                     	//check if you are of the correct format
	                                     	 var pattern_well = /^[A-P]:[1-9]{1,2}$/;
	                                     	//alert(txt.value);
	                                     	 if(pattern_well.test(txt) == false){
	                                     	 	valid = "false";
	                                     	 	inputs[i].style.background = "#f9ae7d";	
	                                     	 }else{ inputs[i].style.background = "white";}	
	                                     };
	                                     
	                                     //fields that may have 000.00 decimal format
	                                     var pattern2 = /.*(\[dnaCont\]|\[sampConc\]|\[nano\]|\[280\]|\[230\]|\[RIN\])$/;
	                                     if(pattern2.test(name)){
	                                     	//check if you are of the correct format
	                                     	 var pattern_num = /^\s*(?=.*[0-9])\d{0,3}(?:\.\d{1,2})?\s*$/;	
	                                     	 if(pattern_num.test(txt) == false){
	                                     	 	valid = "false";
	                                     	 	inputs[i].style.background = "#f9ae7d";	
	                                     	 }else{ inputs[i].style.background = "white";}	
	                                     };
	                                     
	                                     //fields that may only have 000.0 format
	                                     var pattern3 = /.*(\[vol\])$/;
	                                     if(pattern3.test(txt)){
	                                     	//check if you are of the correct format
	                                     	 var pattern_vol = /^\s*(?=.*[0-9])\d{0,3}(?:\.\d{1})?\s*$/;
	                                     	 if(pattern_vol.test(txt) == false){
	                                     	 	valid = "false";
	                                     	 	inputs[i].style.background = "#f9ae7d";	
	                                     	 }else{ inputs[i].style.background = "white";}		
	                                     };
	                                     
                                     }
                                 }
                                 
                    			//check radio buttons are checked									
									var index;		
									var a = js_array;		
									for (index = 0; index < a.length; ++index) {		
				   	 					var sample_name = a[index]["sample_name"];	
											
				   	 					var exists_name_yes = sample_name+'_exists_yes';	
										var exists_name_no = sample_name+'_exists_no';	
										var color_checkbox_div = sample_name+'_color_checkbox_div';	
										color_checkbox_div = document.getElementById(color_checkbox_div);	
											
										if((!document.getElementById(exists_name_yes).checked) && (!document.getElementById(exists_name_no).checked)){	
											color_checkbox_div.style.backgroundColor = "#f9ae7d";
											valid = 'false';
										}
										else{
											color_checkbox_div.style.backgroundColor = "white";
										}	
									}	

                           		//add check for radio buttons
                           		//sessions
                           		//print out sheet and commit to db
                                 return valid;
                           }

		</script>

