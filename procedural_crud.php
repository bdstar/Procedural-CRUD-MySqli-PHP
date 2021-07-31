<?php
	/*array(
		'name' => "id", // table column name 
		'type' => "INT", //Column data type according to the SQL convention
		'lenght' => 11, //Column length as integer
		'default' => NULL, // possible value can be [NULL, CURRENT_TIMESTAMP, AUTO_INCREMENT, "string"]	
		'index' => "PRIMARY",	// PRIMARY, UNIQUE, INDEX, FULLTEXT, SPATIAL						
	),
	# --------- key->default -------
	//NULL => NULL DEFAULT NULL
	//CURRENT_TIMESTAMP => NOT NULL DEFAULT CURRENT_TIMESTAMP
	//AUTO_INCREMENT => NOT NULL AUTO_INCREMENT
	//'string' => NOT NULL DEFAULT 'string'

	# --------- key->index -----------
	// PRIMARY KEY (`id`)
	// UNIQUE (`id`, `firstname`)
	// INDEX (`lastname`)
	// FULLTEXT (`age`)
	// SPATIAL (`created_at`)	
	*/

	function create_table($connection, $tablename, array $table){
		$sql = "CREATE TABLE `".$tablename."` ( ";
		$sql_index = "";

		foreach ($table as $column) {
			foreach ($column as $key => $value) {
				switch ($key) {
					case "name":
						$sql .= "`".$value."`";
						$column_name = $value;
						break;
					case "type":
						$sql .= " ".$value."";
						break;
					case "lenght":
						if($value !== NULL){
							$sql .= "(".$value.")";
						}							
						break;
					case "default":
						if ($value==NULL) {
							$sql .= " NULL DEFAULT NULL,";
						} 
						elseif ($value=="CURRENT_TIMESTAMP") {
							$sql .= " NOT NULL DEFAULT CURRENT_TIMESTAMP,";
						}
						elseif ($value=="AUTO_INCREMENT") {
							$sql .= " NOT NULL AUTO_INCREMENT,";
						}						
						else {
							$sql .= " NOT NULL DEFAULT '".$value."',";
						}
						break;
					case "index":
						if ($value=="PRIMARY") {
							$sql_index .= " PRIMARY KEY (`".$column_name."`)";
						} 
						elseif ($value=="UNIQUE") {
							$sql_index .= " ,UNIQUE (`".$column_name."`)";
						}
						elseif ($value=="INDEX") {
							$sql_index .= " ,INDEX (`".$column_name."`)";
						}						
						elseif ($value=="FULLTEXT") {
							$sql_index .= " ,FULLTEXT (`".$column_name."`)";
						}
						elseif ($value=="SPATIAL") {
							$sql_index .= " ,SPATIAL (`".$column_name."`)";
						}	
						break;
				}
			}
		} 
		$sql .= $sql_index;
		$sql .= ") ENGINE = InnoDB;";
		echo "sql: ".$sql;
		die;
		$sql="CREATE TABLE $tablename(FirstName CHAR(30),LastName CHAR(30),Age INT)";

		// Check and Execute query
		if (mysqli_query($connection,$sql)) {
			echo "<br>Table <b style='color: blue'>$tablename</b> created successfully";
			return true;
		} else {
			echo "<br>Error creating table: <b style='color: blue'>" . mysqli_error($connection) . "</b>";
			return false;
		}



		/*
		CREATE TABLE `test1`. ( 
			`id` INT(11) NOT NULL AUTO_INCREMENT , 
			`firstname` VARCHAR(100) NULL DEFAULT NULL , 
			`lastname` VARCHAR(100) NULL DEFAULT NULL , 
			`age` INT(11) NULL DEFAULT NULL , 
			`created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , 
			`updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , 
			PRIMARY KEY (`id`)
			) ENGINE = InnoDB;

		CREATE TABLE `test1`. ( `id` INT(11) NULL AUTO_INCREMENT , `firstname` VARCHAR(100) NULL DEFAULT '' , `lastname` VARCHAR(100) NULL DEFAULT NULL , `age` INT(11) NULL DEFAULT NULL , `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`), INDEX (`lastname`), UNIQUE (`id`, `firstname`), FULLTEXT (`age`), SPATIAL (`created_at`)) ENGINE = InnoDB;
		CREATE TABLE `test1`. ( `id` INT(11) NULL AUTO_INCREMENT , `firstname` VARCHAR(100) NULL DEFAULT '' , `lastname` VARCHAR(100) NULL DEFAULT NULL , `age` INT(11) NULL DEFAULT NULL , `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`, `firstname`), INDEX (`lastname`), UNIQUE (`id`), FULLTEXT (`age`), SPATIAL (`created_at`)) ENGINE = InnoDB;
		*/
	}

	//Fatch data by accepting table name and columns(1 dimentional array) name
	function fatch($connection, $table, array $columns){
		if (empty($columns)) {
			$result = mysqli_query($connection, "SELECT * FROM $table");
		}else{
			$columns = implode(",",$columns);
			$result = mysqli_query($connection, "SELECT $columns FROM $table");
		}
	
		if(mysqli_connect_errno())
		  {
		  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		  }

		//return tow dimentional array as required columns result
		return mysqli_fetch_all($result,MYSQLI_ASSOC);
	}


	# Insert Data within table by accepting TableName and Table column => Data as associative array
	function insert($connection, $tblname, array $val_cols){
		//echo $tblname."<br>";

		//$Values = implode(", ",$val_cols);
		//echo "Value => ".$Values."<br>";

		$keysString = implode(", ", array_keys($val_cols));
		//echo "Key => ".$keysString."<br>";

		# print key and value for the array
		$i=0;
		foreach($val_cols as $key=>$value) {
			//echo "Key=" . $key . ", Value=" . $value;
			$StValue[$i] = "'".$value."'";
			//echo $StValue[$i];
		    //echo "<br>";
		    $i++;
		}

		$StValues = implode(", ",$StValue);
		//echo "Value => ".$StValues."<br>";
		
		if (mysqli_connect_errno()) {
		  echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}

		if(mysqli_query($connection,"INSERT INTO $tblname ($keysString) VALUES ($StValues)")){
			echo "<br><b style='color: green'>Data successfully Inserted!</b>";
		}else{
			echo "<br><b style='color: red'>Unable to Insert data!</b>";
		}
	}


	//Delete data form table; Accepting Table Name and Keys=>Values as associative array
	function delete($connection, $tblname, array $val_cols){
		
		$i=0;
		foreach($val_cols as $key=>$value) {
			//echo "Key=" . $key . ", Value=" . $value;
			$exp[$i] = $key." = '".$value."'";
			//echo $exp[$i];
		    //echo "<br>";
		    $i++;
		}

		$Stexp = implode(" AND ",$exp);
		//echo "Value => ".$Stexp."<br>";
		mysqli_query($connection,"DELETE FROM $tblname WHERE $Stexp");
	}


	#Update data within table; Accepting Table Name and Keys=>Values as associative array
	function update($connection, $tblname, array $set_val_cols, array $cod_val_cols){
		
		$i=0;
		foreach($set_val_cols as $key=>$value) {
			//echo "Key=" . $key . ", Value=" . $value;
			$set[$i] = $key." = '".$value."'";
			//echo $set[$i];
		    //echo "<br>";
		    $i++;
		}

		$Stset = implode(", ",$set);
		//echo "Set String => ".$Stset."<br>";


		$i=0;
		foreach($cod_val_cols as $key=>$value) {
			//echo "Key=" . $key . ", Value=" . $value;
			$cod[$i] = $key." = '".$value."'";
			//echo $cod[$i];
		    //echo "<br>";
		    $i++;
		}

		$Stcod = implode(" AND ",$cod);
		//echo "condition String => ".$Stcod."<br>";

		mysqli_query($connection,"UPDATE $tblname SET $Stset WHERE $Stcod");
	}






$hostname = "localhost";
$username = "root";
$passowrd = "";
$database = "test1";

# Database connection String
$con=mysqli_connect($hostname,$username,$passowrd);


// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
else{
	echo "Successfully connected to MySQL...";
}


# Create Database query
$sql="CREATE DATABASE IF NOT EXISTS $database";

// Check and Create database if not exists
if (mysqli_query($con,$sql)) {
  echo "<br>Database <b style='color: blue'>$database</b> created successfully";
  mysqli_select_db($con,$database);
} else {
  echo "<br>Error creating database: " . mysqli_error($con);
}



$create_table = array(
	array(
		'name' => "id", 
		'type' => "INT", 
		'lenght' => 11,
		'default' => "AUTO_INCREMENT",
		'index' => "PRIMARY",						
	),	
	array(
		'name' => "firstname", 
		'type' => "VARCHAR", 
		'lenght' => 100,
		'default' => NULL,	
		'index' => NULL					
	),	
	array(
		'name' => "lastname", 
		'type' => "VARCHAR", 
		'lenght' => 100,
		'default' => NULL,	
		'index' => NULL							
	),
	array(
		'name' => "age", 
		'type' => "INT", 
		'lenght' => 11,
		'default' => NULL,	
		'index' => NULL							
	),
	array(
		'name' => "created_at", 
		'type' => "DATETIME", 
		'lenght' => NULL,
		'default' => "CURRENT_TIMESTAMP",
		'index' => NULL						
	),
	array(
		'name' => "updated_at", 
		'type' => "DATETIME", 
		'lenght' => NULL,
		'default' => "CURRENT_TIMESTAMP",
		'index' => NULL								
	),						
);

/*------------------ Create Table Operation ----------------------*/
$tablename = "persons";
# Create Table according to the array
create_table($con,$tablename, $create_table);


/*------------------ INSEART Operation ----------------------*/
# Table Column Name and Value to be insert into the table
$insert_value = array("FirstName"=>"MD","LastName"=>'Jamal',"Age"=>30);
# Insert Data from Table
insert($con,$tablename, $insert_value);


/*------------------ DELETE Operation ----------------------*/
# Table Column Name and Value to be delete form the Table
$delete_condition = array("LastName"=>"Cake","Age"=>32);
# Delete Data from Table
//delete($con, $tablename, $delete_condition);


/*------------------ UPDATE Operation ----------------------*/
# Set and Condition to be update row in the table
$set_value = array("LastName"=>'Cake',"Age"=>'27');
$update_condition = array("FirstName"=>'MD',"Age"=>'34');
# Update function for the table row
// update($con, $tablename, $set_value, $update_condition);


/*------------------ SELECT Operation ----------------------*/
# Select consition to fetch data from table
$select_condition = array("FirstName","LastName","Age");
$show = fatch($con ,$tablename, $select_condition);
echo "<pre>";
print_r($show);
echo "</pre>";

mysqli_close($con);
?>