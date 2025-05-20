<?php
session_start();
require_once('../vendor/autoload.php');
require('../vendor/fpdf/fpdf.php');
include_once("../includes/connection.php");

// Set the title for the web page
$title = 'Student Information';

// Create new PDF document
class PDF extends FPDF
{
    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Printed on: ' . date('Y-m-d'), 0, 0, 'C');
    }
}

$pdf = new PDF('L');

// Add a page
$pdf->AddPage();

// Set font for header
$pdf->SetFont('Arial', 'B', 18);
$pdf->Cell(0, 10, $title, 0, 1, 'C');
$pdf->Ln(); 

// Set font for table content
$pdf->SetFont('Arial', '', 12);

// Check if option is included in the URL
if (isset($_GET['option'])) {
    // Sanitize and store the option value
    $option = mysqli_real_escape_string($connect, $_GET['option']);

    $sections = implode("','", $_SESSION['dept_sec']); // Convert section array to comma-separated string
    $dept = $_SESSION['dept_adv']; // Single advisor value
    $course = $_SESSION['dept_crs']; // Single course value

    switch ($option) {
        case 'Student Information':
            $query = "SELECT si.lastName, si.firstName, si.contactNo, si.section, si.course, si.status, ci.companyName 
                  FROM studentinfo si 
                  LEFT JOIN company_info ci ON si.companyCode = ci.companyCode
                  WHERE si.section IN ('$sections')
                  AND si.department = '$dept'
                  AND si.course = '$course'";
            break;
        case 'Deployed':
        case 'Undeployed':
        case 'Completed':
            $query = "SELECT si.lastName, si.firstName, si.contactNo, si.section, si.course, ci.companyName, ci.companyaddress, ci.trainerContact, si.status 
                  FROM studentinfo si
                  LEFT JOIN company_info ci ON si.companyCode = ci.companyCode
                  WHERE si.status = '$option'
                  AND si.section IN ('$sections')
                  AND si.department = '$dept'
                  AND si.course = '$course'";
            break;
        default:
            // Default query to select all data
            $query = "SELECT lastName, firstName, contactNo, section, course, status FROM studentinfo
                  WHERE section IN ('$sections')
                  AND si.department = '$dept'
                  AND si.course = '$course'";
            break;
    }



    // Include option name in the PDF file name
    $pdfFileName = "Student_Information_" . str_replace(' ', '_', $option) . ".pdf";
} else {
    // If option is not included, select all data
    $query = "SELECT lastName, firstName, contactNo, section, course, status FROM studentinfo";

    // Default PDF file name
    $pdfFileName = "Student_Information.pdf";
}

$result = mysqli_query($connect, $query);

// Set font for table header
$pdf->SetFont('Arial', 'B', 10);

// Set font for table header
$pdf->SetFont('Arial', 'B', 10);

if ($option == 'Student Information') {
    // Add a table header for Student Information
    $pdf->Cell(60, 7, 'Name', 1);
    $pdf->Cell(30, 7, 'Contact', 1);
    $pdf->Cell(40, 7, 'Section/Course', 1);
    $pdf->Cell(115, 7, 'Company', 1);
    $pdf->Cell(30, 7, 'Status', 1);
} else {
    // Add a table header for other options
    $pdf->Cell(45, 7, 'Name', 1);
    $pdf->Cell(30, 7, 'Section/Course', 1);
    $pdf->Cell(80, 7, 'Company', 1);
    $pdf->Cell(80, 7, 'Address', 1);
    $pdf->Cell(40, 7, 'Trainer Contact', 1);
}
$pdf->Ln();

// Reset font for table content
$pdf->SetFont('Arial', '', 10);

while ($row = mysqli_fetch_assoc($result)) {
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    if ($option == 'Student Information') {
        $pdf->MultiCell(60, 7, $row['lastName'] . ' ' . $row['firstName'], '1');
        // Get height of MultiCell content
        $multiCellHeight = $pdf->GetY() - $y;
    
        // Set position for next cell based on height of MultiCell
        $pdf->SetXY($x + 60, $y);
    
        $pdf->MultiCell(30, $multiCellHeight, $row['contactNo'], 1);
        // Update $multiCellHeight for the next cell
        $multiCellHeight = $pdf->GetY() - $y;
    
        // Set position for next cell based on height of MultiCell
        $pdf->SetXY($x + 90, $y);
    
        $pdf->MultiCell(40, $multiCellHeight, $row['section'] . ' ' . $row['course'], 1);
        // Update $multiCellHeight for the next cell
        $multiCellHeight = $pdf->GetY() - $y;
    
        // Set position for next cell based on height of MultiCell
        $pdf->SetXY($x + 130, $y);
        $pdf->MultiCell(115, $multiCellHeight, $row['companyName'], 1);
        // Update $multiCellHeight for the next cell
        $multiCellHeight = $pdf->GetY() - $y;
    
        // Set position for next cell based on height of MultiCell
        $pdf->SetXY($x + 245, $y);
        $pdf->MultiCell(30, $multiCellHeight, $row['status'], 1, 'C');
        // Update $multiCellHeight for the next cell
        $multiCellHeight = $pdf->GetY() - $y;
    
        // Set position for next cell based on height of MultiCell
        $pdf->SetXY($x + 275, $y);
    }
     else {
        $pdf->MultiCell(45, 7, $row['lastName'] . ' ' . $row['firstName'], 1);
        // Get height of MultiCell content
        $multiCellHeight = $pdf->GetY() - $y;
    
        // Set position for next cell based on height of MultiCell
        $pdf->SetXY($x + 45, $y);
    
        $pdf->MultiCell(30, $multiCellHeight, $row['section'] . ' ' . $row['course'], 1);
        // Get height of MultiCell content
        $multiCellHeight = $pdf->GetY() - $y;
    
        // Set position for next cell based on height of MultiCell
        $pdf->SetXY($x + 75, $y);
    
        $pdf->MultiCell(80, $multiCellHeight, $row['companyName'], 1);
        // Get height of MultiCell content
        $multiCellHeight = $pdf->GetY() - $y;
    
        // Set position for next cell based on height of MultiCell
        $pdf->SetXY($x + 155, $y);

        $pdf->MultiCell(80, $multiCellHeight, $row['companyaddress'], 1);
        // Get height of MultiCell content
        $multiCellHeight = $pdf->GetY() - $y;
    
        // Set position for next cell based on height of MultiCell
        $pdf->SetXY($x + 235, $y);

        $pdf->MultiCell(40, $multiCellHeight, $row['trainerContact'], 1);
        // Get height of MultiCell content
        $multiCellHeight = $pdf->GetY() - $y;
    
        // Set position for next cell based on height of MultiCell
        $pdf->SetXY($x + 275, $y);
    }
    $pdf->Ln();
}


$pdf->Output($pdfFileName, 'D');
