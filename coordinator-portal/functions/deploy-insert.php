<?php 
 $connect = new mysqli('localhost', 'root', '', 'plmunoiedb');
   	  
   	  if(isset($_POST['save']))
	{

   	   	$studentID = $_POST['studentID'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname']; 
        $department = $_POST['department'];
       	$course = $_POST['course'];
        $companyName = $_POST['companyName'];
        $jobrole = $_POST['jobrole'];

    
    $query ="INSERT INTO ongoinginternship(studentID,firstname,lastname,department,course,companyName,jobrole) VALUES( '$studentID','$firstname', '$lastname', '$department', '$course', '$companyName', '$jobrole')";
        if (mysqli_query($connect, $query)) {
		echo "New record created successfully !";
		header("Location:ongoinginterns.php");
		} else {
				echo "Error: " . $query . "
		" . mysqli_error($connect);
			 }
			 mysqli_close($connect);
		}
		?>
