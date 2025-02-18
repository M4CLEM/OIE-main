<?php
session_start();
include_once("../includes/connection.php");

require('../vendor/fpdf/fpdf.php');

// Get student ID from GET parameter
$studentID = $_GET['studentID'];

// Fetch student information from the database
$query = "SELECT * FROM studentinfo WHERE studentID ='$studentID'";
$result = mysqli_query($connect, $query);

// Check if the query was successful
if ($result) {
    // Fetch the row from the result set
    $row = mysqli_fetch_assoc($result);

    // Extract data from the row
    $firstname = $row['firstname'];
    $lastname = $row['lastname'];
    $address = $row['address'];
    $contactNo = $row['contactNo'];
    $email = $row['email'];
    $section = $row['section'];
    $objective = $row['objective'];
    $image = $row['image'];
    $skills = explode(',', $row['skills']);
    $seminars = explode(',', $row['seminars']);

    // Create PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 12);

    $pdf->Ln(10);

    // Set left and right margins to 15
    $pdf->SetMargins(15, 15);

    //Image
    $pdf->Image($image, 145, $pdf->GetY(), 50, 0);
    $pdf->Ln(5);

    // Personal Information
    $pdf->SetFont('Arial', 'B', 20);
    $pdf->SetX(15);
    $pdf->Cell(0, 10, $firstname . ' ' . $lastname, 0, 1);
    $pdf->SetFont('Arial', '', 12);
    $pdf->SetX(15);
    $pdf->Cell(0, 10, 'Address: ' . $address, 0, 1);
    $pdf->SetX(15);
    $pdf->Cell(0, 10, 'Contact Number: +63' . $contactNo, 0, 1);
    $pdf->SetX(15);
    $pdf->Cell(0, 10, 'Email: ' . $email, 0, 1);
    $pdf->Ln(5);

    $pdf->Ln(10); // Add additional space before the line

    // Add a horizontal line
    $pdf->Line(15, $pdf->GetY(), $pdf->GetPageWidth() - 15, $pdf->GetY(), array('width' => 0.5, 'color' => array(0, 0, 0)));

    // Objective
    $pdf->Ln(5); // Adjust the space after the line to match the space before the "Objective" section
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'OBJECTIVE', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(0, 10, $objective, 0, 'J');

    // Skills
    $pdf->Ln(5); // Add space between sections
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'SKILLS', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 12);
    foreach ($skills as $skill) {
        $skill = ltrim($skill); // Remove spaces at the beginning
        $pdf->Cell(50, 10, $skill, 0, 1);
    }

    // Seminars Attended
    $pdf->Ln(5); // Add space between sections
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'SEMINARS ATTENDED', 0, 1, 'L');
    $pdf->SetFont('Arial', '', 12);
    foreach ($seminars as $seminar) {
        $seminar = ltrim($seminar); // Remove spaces at the beginning
        $pdf->Cell(0, 10, $seminar, 0, 1);
    }
    $pdf->Ln(5);

    // Output PDF with dynamic filename
    $pdfFileName = "Resume_" . $lastname . "_" . $section . ".pdf";
    $pdf->Output($pdfFileName, 'D');
} else {
    // Handle query error
    echo "Error: " . mysqli_error($connect);
}
