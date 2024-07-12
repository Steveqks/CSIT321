<?php
session_start();
include 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['Email'])) {
    header("Location: ../Unregistered Users/LoginPage.php");
    exit();
}

$user_id = $_SESSION['UserID'];
$FirstName = $_SESSION['FirstName'];

// Connect to the database
$conn = OpenCon();

$availability = [];

// Set the default date to the current week's Monday
$today = new DateTime();
$dayOfWeek = $today->format('N'); // 'N' format gives 1 for Monday, 2 for Tuesday, ..., 7 for Sunday
$diff = 1 - $dayOfWeek; // calculate the difference to Monday
$monday = $today->modify("$diff days");
$week_start_date = $monday->format('Y-m-d');
$formatted_week_start_date = $monday->format('d/m/Y');

// Fetch existing availability for the current week
$sql_fetch_availability = "SELECT DayOfWeek, IsAvailable FROM availability WHERE UserID = ? AND WeekStartDate = ?";
$stmt_fetch_availability = $conn->prepare($sql_fetch_availability);
$stmt_fetch_availability->bind_param("is", $user_id, $week_start_date);
$stmt_fetch_availability->execute();
$result_fetch_availability = $stmt_fetch_availability->get_result();
while ($row = $result_fetch_availability->fetch_assoc()) {
    $availability[$row['DayOfWeek']] = $row['IsAvailable'];
}
$stmt_fetch_availability->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['week_start_date'])) {
    // Convert DD/MM/YYYY to YYYY-MM-DD for storing in the database
    $week_start_date = DateTime::createFromFormat('d/m/Y', $_POST['week_start_date'])->format('Y-m-d');

    // Save availability
    foreach ($_POST['availability'] as $day => $is_available) {
        $availability[$day] = $is_available === '1' ? 1 : 0;
        $sql = "INSERT INTO availability (UserID, WeekStartDate, DayOfWeek, IsAvailable)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE IsAvailable = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issii", $user_id, $week_start_date, $day, $availability[$day], $availability[$day]);
        $stmt->execute();
        $stmt->close();
    }

    CloseCon($conn);
    header("Location: PT_SetAvailability.php?message=Availability saved successfully.");
    exit();
}

CloseCon($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Availability</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .top-section {
            border: 1px solid black;
            height: 20vh;
            overflow: hidden;
            text-align: left;
            padding: 10px;
        }

        .top-section img {
            height: 100%;
            width: auto;
        }

        .middle-section {
            display: flex;
            border: 1px solid black;
            height: 95vh;
        }

        .navbar {
            border: 1px solid black;
            width: 200px;
            padding: 0;
            background-color: #f8f8f8;
            box-sizing: border-box;
        }

        .navbar ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            width: 200px;
        }

        .navbar li {
            margin: 0;
        }

        .navbar a {
            text-decoration: none;
            color: #333;
            display: block;
            width: calc(100% - 1px);
            padding: 10px;
            border: 0.5px solid black;
            transition: background-color 0.3s, color 0.3s;
            box-sizing: border-box;
            border-width: 1px 0px 0px 0px;
        }

        .navbar a:hover {
            background-color: #ddd;
            color: #000;
            border: 0.5px solid black;
        }

        .availability-section {
            padding: 20px;
            flex-grow: 1;
        }

        .availability-header {
            display: inline-flex;
            align-items: center;
            border-bottom: 1px solid black;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }

        .availability-header i {
            margin-right: 10px;
        }

        .availability-header h2 {
            margin: 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input, .form-group select {
            width: 150px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-group input[readonly] {
            background-color: #f0f0f0;
            cursor: not-allowed;
        }

        .form-group button {
            width: 170px;
            padding: 10px 20px;
            background-color: green;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .form-group button:hover {
            background-color: darkgreen;
        }

        .message {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <!-- TOP SECTION -->
    <div class="top-section">
        <img src="Images/tms.png" alt="TrackMySchedule Logo">
    </div>

    <!-- MIDDLE SECTION -->
    <div class="middle-section">
        <!-- LEFT SECTION (NAVIGATION BAR) -->
        <div class="navbar">
            <ul>
                <li><a href="PT_HomePage.php"><?php echo htmlspecialchars("$FirstName, Staff(PT)"); ?></a></li>
                <li><a href="PT_AccountDetails.php">Manage Account</a></li>
                <li><a href="PT_AttendanceManagement.php">Attendance Management</a></li>
                <li><a href="PT_LeaveManagement.php">Leave Management</a></li>
                <li><a href="PT_SwapShift.php">Swap Shifts</a></li>
                <li><a href="PT_SetAvailability.php">Set Availability</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>

        <!-- RIGHT SECTION (SET AVAILABILITY) -->
        <div class="availability-section">
            <div class="availability-header">
                <i class="fas fa-calendar-alt"></i>
                <h2>Set Availability</h2>
            </div>

            <?php if (isset($_GET['message'])): ?>
                <div class="message success"><?php echo htmlspecialchars($_GET['message']); ?></div>
            <?php elseif (isset($_GET['error'])): ?>
                <div class="message error"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>

            <form action="PT_SetAvailability.php" method="post" id="availability-form">
                <div class="form-group">
                    <label for="week_start_date">Select Week</label>
                    <input type="text" id="week_start_date" name="week_start_date" value="<?php echo htmlspecialchars($formatted_week_start_date); ?>" readonly required>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Availability</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $days_of_week = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                        foreach ($days_of_week as $day):
                            $is_available = isset($availability[$day]) ? $availability[$day] : '';
                        ?>
                            <tr>
                                <td><?php echo $day; ?></td>
                                <td>
                                    <select name="availability[<?php echo $day; ?>]" class="availability-select" required>
                                        <option value="" <?php echo $is_available === '' ? 'selected' : ''; ?>></option>
                                        <option value="1" <?php echo $is_available === '1' ? 'selected' : ''; ?>>Available</option>
                                        <option value="0" <?php echo $is_available === '0' ? 'selected' : ''; ?>>Not Available</option>
                                    </select>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="form-group">
                    <button type="submit">Save Availability</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script>
        $(document).ready(function() {
            // Fetch availability for the default week
            $.ajax({
                url: 'fetch_availability.php',
                method: 'POST',
                data: { week_start_date: $("#week_start_date").val() },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Update form fields with fetched availability
                        $('.availability-select').each(function() {
                            var day = $(this).attr('name').split('[')[1].split(']')[0];
                            $(this).val(response.availability[day]);
                        });
                    } else {
                        // Reset form fields if no availability found
                        $('.availability-select').each(function() {
                            $(this).val('');
                        });
                    }
                }
            });

            $("#week_start_date").datepicker({
                dateFormat: 'dd/mm/yy',
                firstDay: 1, // Set Monday as the first day of the week
                minDate: 0, // Disable past dates
                beforeShowDay: function(date) {
                    var day = date.getDay();
                    return [(day == 1), '']; // Enable only Mondays
                },
                onSelect: function(dateText, inst) {
                    var date = $(this).datepicker('getDate');
                    var formattedDate = ('0' + date.getDate()).slice(-2) + '/' + ('0' + (date.getMonth() + 1)).slice(-2) + '/' + date.getFullYear();
                    $(this).val(formattedDate); // Format to DD/MM/YYYY
                    
                    // Fetch availability for the selected week
                    $.ajax({
                        url: 'fetch_availability.php',
                        method: 'POST',
                        data: { week_start_date: formattedDate },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                // Update form fields with fetched availability
                                $('.availability-select').each(function() {
                                    var day = $(this).attr('name').split('[')[1].split(']')[0];
                                    $(this).val(response.availability[day]);
                                });
                            } else {
                                // Reset form fields if no availability found
                                $('.availability-select').each(function() {
                                    $(this).val('');
                                });
                            }
                        }
                    });
                }
            }).on('keydown', function(e) {
                e.preventDefault();
            });
        });
    </script>
</body>
</html>
