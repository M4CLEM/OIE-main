<?php 
 $connect = new mysqli('localhost', 'root', '', 'plmunoiedb');
   	  
   	  if(isset($_POST['save']))
	{

        $Fullname = $_POST['Fullname'];
        $email = $_POST['email'];
        $section = $_POST['section'];
        $dept = $_POST['dept'];  
        
    $query ="INSERT INTO listadviser(Fullname,email,section,dept) VALUES('$Fullname', '$email','$section','$dept')";
        if (mysqli_query($connect, $query)) {
		echo "New record created successfully !";
		header("Location:advisers.php");
		} else {
				echo "Error: " . $query . "
		" . mysqli_error($connect);
			 }
			 mysqli_close($connect);
		}
		?>
