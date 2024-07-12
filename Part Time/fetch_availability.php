<?php
include 'db_connection.php';
session_start();

if (!isset($_SESSION['UserID']) || !isset($_POST['week_start_date'])) {
    echo json_encode(['success' => false]);
    exit();
}

$user_id = $_SESSION['UserID'];
$week_start_date = DateTime::createFromFormat('d/m/Y', $_POST['week_start_date'])->format('Y-m-d');

$conn = OpenCon();

$sql = "SELECT DayOfWeek, IsAvailable FROM availability WHERE UserID = ? AND WeekStartDate = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $week_start_date);
$stmt->execute();
$result = $stmt->get_result();

$availability = [];
while ($row = $result->fetch_assoc()) {
    $availability[$row['DayOfWeek']] = $row['IsAvailable'];
}

$stmt->close();
CloseCon($conn);

echo json_encode(['success' => true, 'availability' => $availability]);
?>
