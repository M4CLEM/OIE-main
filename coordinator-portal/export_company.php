<?php
require_once('../vendor/autoload.php');
require('../vendor/fpdf/fpdf.php');
include_once("../includes/connection.php");

// Set the title for the web page
$title = 'Industry Partners of PLMUN';

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

$pdf = new PDF();

// Add a page
$pdf->AddPage();

// Set font for header
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, $title, 0, 1, 'C'); // Centered header
$pdf->Ln(10); // Add some space after header

// Set font for table content
$pdf->SetFont('Arial', '', 10);

// Check if department parameter is set
$dept = isset($_GET['dept']) ? $_GET['dept'] : '';

// Fetch data from the database, filtering by department if provided
$query = "SELECT companyName, companyaddress, dept FROM companylist" . ($dept ? " WHERE dept = '$dept'" : "");
$result = mysqli_query($connect, $query);

// Set font for table header
$pdf->SetFont('Arial', 'B', 10);

// Add a table header
$pdf->Cell(60, 7, 'Company Name', 1);
$pdf->Cell(90, 7, 'Company Address', 1);
$pdf->Cell(40, 7, 'Department', 1);
$pdf->Ln();

// Reset font for table content
$pdf->SetFont('Arial', '', 10);

while ($row = mysqli_fetch_assoc($result)) {
    // Get current position
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    
    // Adjust column widths or implement word wrapping
    $pdf->MultiCell(60, 7, $row['companyName'], 1);
    
    // Get height of MultiCell content
    $multiCellHeight = $pdf->GetY() - $y;
    
    // Set position for next cell based on height of MultiCell
    $pdf->SetXY($x + 60, $y);
    
    // Adjust column widths or implement word wrapping for Company Address
    $pdf->MultiCell(90, $multiCellHeight, $row['companyaddress'], 1);
    
    // Set position for next cell based on height of MultiCell
    $pdf->SetXY($x + 150, $y);
    
    // Output Department
    $pdf->Cell(40, $multiCellHeight, $row['dept'], 1);
    
    // Go to next line
    $pdf->Ln();
}

$pdfFileName = "Industry_Partners.pdf";
$pdf->Output($pdfFileName, 'D');
?>