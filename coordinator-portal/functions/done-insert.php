<?php 
 $connect = new mysqli('localhost', 'root', '', 'plmunoiedb');
 
   	    if(isset($_POST['save']))
	{
   	  	$studentID = $_POST['studentID'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname']; 
        $department = $_POST['department'];
       	$course = $_POST['course'];
        $status = $_POST['status'];

    
    $query ="INSERT INTO doneinternship(studentID,firstname,lastname,department,course,status) VALUES( '$studentID','$firstname', '$lastname', '$department', '$course', '$status')";
        if (mysqli_query($connect, $query)) {
		echo "New record created successfully !";
		header("Location:doneinterns.php");
		} else {
				echo "Error: " . $query . "
		" . mysqli_error($connect);
			 }
			 mysqli_close($connect);
		
		}
		?>
