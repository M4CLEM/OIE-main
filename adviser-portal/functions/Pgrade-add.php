<?php
$connect = new mysqli('localhost', 'root', '', 'plmunoiedb');

 if(isset($_POST['save']))
    {
        $SOU = $_POST['SOU'];
        $QOW = $_POST['QOW']; 
        $EC = $_POST['EC'];
        $PAP = $_POST['PAP'];
        $WE = $_POST['WE'];
        $D = $_POST['D'];
        $total = $SOU + $QOW + $EC + $PAP + $WE + $D;
        $average = $total / 6.0;
        $percentage = ($total / 500.0) * 100;

        if ($average >= 90)
            $grade = "A";
        else if ($average >= 80 && $average < 90)
            $grade = "B";
        else if ($average >= 70 && $average < 80)
            $grade = "C";
        else if ($average >= 60 && $average < 70)
            $grade = "D";
        else
            $grade = "E";
        echo "The Total marks   = " . $total . "/500\n";
        echo "The Average marks = " . $average . "\n";
        echo "The Percentage    = " . $percentage . "%\n";
        echo "The Grade         = '" . $grade . "'\n";
    }
?>