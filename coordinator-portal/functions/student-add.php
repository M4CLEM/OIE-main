<?php 
 $connect = new mysqli('localhost', 'root', '', 'plmunoiedb');
   	  
   	  if(isset($_POST['save']))
	{

   	    $studentID = $_POST['studentID']; 
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname']; 
        $course = $_POST['course'];
        $department = $_POST['department'];
       	$email = $_POST['email'];
        $status = $_POST['status'];

    
    $query ="INSERT INTO studentinfo(studentID,firstname,lastname,course,department,email,status) VALUES('$studentID' , '$firstname', '$lastname', '$course', '$department', '$email', '$status')";
        if (mysqli_query($connect, $query)) {
		echo "New record created successfully !";
		header("Location:../masterlist.php");
		} else {
				echo "Error: " . $query . "
		" . mysqli_error($connect);
			 }
			 mysqli_close($connect);
		}
		?>
