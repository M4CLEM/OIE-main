<?php
include_once("../../includes/connection.php");

if (isset($_POST['college'])) {
    $college = $_POST['college'];
    $query = "SELECT * FROM sections_list WHERE department = ?";
    $stmt = $connect->prepare($query);
    $stmt->bind_param("s", $college);
    $stmt->execute();
    $result = $stmt->get_result();
    $sections = array();
    while ($row = $result->fetch_assoc()) {
        $sections[] = $row;
    }

    // Start building the HTML for the table rows
    $html = '';
    foreach ($sections as $section) {
        $html .= '<tr>';
        // Create a link to the section details page, passing the section ID as a parameter
        $html .= '<td><a href="#" class="section-link small" data-section="' . $section['section'] . '">' . $section['department'] . ' ' . $section['course'] . ' ' . $section['section'] . '</a></td>';
        $html .= '<td class="small">Action</td>'; // Placeholder for the action column
        $html .= '</tr>';
    }

    // Echo the HTML
    echo $html;
}
?>