<?php
$startDate = new DateTime('2025-06-04');
$logsNeeded = 35;
$generatedLogs = 0;
$studentNum = '21135942';
$companyID = '1';
$course = 'BSCS';
$dept = 'CITCS';
$section = '4C';
$semester = '2nd Semester';
$schoolYear = '2024-2025';

$sql = "INSERT INTO `logdata` (`id`, `date`, `log_company`, `log_course`, `log_dept`, `log_section`, `student_num`, `status`, `time_in`, `time_out`, `is_approved`, `semester`, `schoolYear`, `break_minutes`) VALUES\n";

while ($generatedLogs < $logsNeeded) {
    $day = $startDate->format('l');
    if ($day !== 'Saturday' && $day !== 'Sunday') {
        $dateStr = $startDate->format('Y-m-d');
        $sql .= "(NULL, '$dateStr', '$companyID', '$course', '$dept', '$section', '$studentNum', 'In', ";
        $sql .= "'$dateStr 08:00:00', '$dateStr 17:00:00', 'Approved', '$semester', '$schoolYear', '60'),\n";
        $generatedLogs++;
    }
    $startDate->modify('+1 day');
}

// Add final partial log (5 rendered hours: 6 hours total with 1-hr break)
$finalDate = $startDate->format('Y-m-d');
$sql .= "(NULL, '$finalDate', '$companyID', '$course', '$dept', '$section', '$studentNum', 'In', ";
$sql .= "'$finalDate 08:00:00', '$finalDate 14:00:00', 'Approved', '$semester', '$schoolYear', '60');";

echo $sql;
