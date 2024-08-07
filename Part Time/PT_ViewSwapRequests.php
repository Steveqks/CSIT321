<?php
session_start();
include 'db_connection.php';
include '../Session/session_check_user_PT.php';

$user_id = $_SESSION['UserID'];
$Email = $_SESSION['Email'];
$FirstName = $_SESSION['FirstName'];
$specialisation_id = $_SESSION['SpecialisationID'];

// Connect to the database
$conn = OpenCon();

$limit = 10; // Number of entries to show in a page.
if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 1;
}

$start_from = ($page - 1) * $limit;

// Fetch pending swap requests where the logged-in user is either the requestor or the requested user
$sql_swap_requests = "SELECT sr.SwapRequestID, sr.RequestorScheduleID, sr.RequestedScheduleID, 
                      s1.WorkDate AS RequestorWorkDate, s1.StartWork AS RequestorStartWork, s1.EndWork AS RequestorEndWork,
                      s2.WorkDate AS RequestedWorkDate, s2.StartWork AS RequestedStartWork, s2.EndWork AS RequestedEndWork,
                      e1.FirstName AS RequestorFirstName, e1.LastName AS RequestorLastName, e2.FirstName AS RequestedFirstName, e2.LastName AS RequestedLastName,
                      sr.RequestorUserID, sr.RequestedUserID, sr.Status
                      FROM swap_requests sr
                      INNER JOIN schedule s1 ON sr.RequestorScheduleID = s1.ScheduleID
                      INNER JOIN schedule s2 ON sr.RequestedScheduleID = s2.ScheduleID
                      INNER JOIN existinguser e1 ON s1.UserID = e1.UserID
                      INNER JOIN existinguser e2 ON s2.UserID = e2.UserID
                      WHERE (sr.Status = 'Pending') AND (sr.RequestorUserID = ? OR sr.RequestedUserID = ?)
                      LIMIT $start_from, $limit";
$stmt_swap_requests = $conn->prepare($sql_swap_requests);
$stmt_swap_requests->bind_param("ii", $user_id, $user_id);
$stmt_swap_requests->execute();
$result_swap_requests = $stmt_swap_requests->get_result();
$swap_requests = $result_swap_requests->fetch_all(MYSQLI_ASSOC);

// Get the total number of records
$sql_total = "SELECT COUNT(*) FROM swap_requests WHERE (Status = 'Pending') AND (RequestorUserID = ? OR RequestedUserID = ?)";
$stmt_total = $conn->prepare($sql_total);
$stmt_total->bind_param("ii", $user_id, $user_id);
$stmt_total->execute();
$stmt_total->bind_result($total_records);
$stmt_total->fetch();

$total_pages = ceil($total_records / $limit);

$stmt_swap_requests->close();
$stmt_total->close();
CloseCon($conn);

// Function to calculate pagination range
function getPaginationRange($current_page, $total_pages, $display_range = 10)
{
    if ($total_pages == 0) {
        return [];
    }

    $start = max(1, $current_page - intval($display_range / 2));
    $end = min($total_pages, $start + $display_range - 1);

    if ($end - $start < $display_range - 1) {
        $start = max(1, $end - $display_range + 1);
    }

    return range($start, $end);
}

$pagination_range = getPaginationRange($page, $total_pages);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Swap Requests</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

        .swap-requests-section {
            padding: 20px;
            flex-grow: 1;
        }

        .swap-header {
            display: inline-flex;
            align-items: center;
            border-bottom: 1px solid black;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }

        .swap-header i {
            margin-right: 10px;
        }

        .swap-header h2 {
            margin: 0;
        }

        .swap-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }

        .swap-table th, .swap-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .swap-table th {
            background-color: #cccccc;
            color: black;
        }

        .swap-actions button {
            padding: 5px 10px;
            margin-right: 5px;
            border: none;
            cursor: pointer;
        }

        .approve-button {
            background-color: green;
            color: white;
        }

        .approve-button:hover {
            background-color: darkgreen;
        }

        .reject-button {
            background-color: red;
            color: white;
        }

        .reject-button:hover {
            background-color: darkred;
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

        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            list-style: none;
            padding: 0;
        }

        .pagination li {
            display: inline;
        }

        .pagination a {
            display: inline-block;
            padding: 8px 12px;
            margin: 0 2px;
            text-decoration: none;
            color: #007BFF;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .pagination a:hover {
            background-color: #ddd;
        }

        .pagination .active a {
            background-color: #007BFF;
            color: white;
            border-color: #007BFF;
        }

        .pagination .disabled a {
            color: #ccc;
            pointer-events: none;
            cursor: default;
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
        <?php include 'navbar.php'; ?>

        <!-- RIGHT SECTION (SWAP REQUESTS) -->
        <div class="swap-requests-section">
            <div class="swap-header">
                <i class="fas fa-exchange-alt"></i>
                <h2>View Swap Requests</h2>
            </div>

            <!-- Display feedback messages -->
            <?php if (isset($_GET['message'])): ?>
                <div class="message success"><?php echo htmlspecialchars($_GET['message']); ?></div>
            <?php elseif (isset($_GET['error'])): ?>
                <div class="message error"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>

            <table class="swap-table">
                <thead>
                    <tr>
                        <th>Your Shift</th>
                        <th>Swap With</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($swap_requests) > 0): ?>
                        <?php foreach ($swap_requests as $request): ?>
                            <?php
                                if ($request['RequestorUserID'] == $user_id) {
                                    $your_shift = $request['RequestorWorkDate'] . ' (' . $request['RequestorStartWork'] . ' - ' . $request['RequestorEndWork'] . ')';
                                    $swap_with = $request['RequestedWorkDate'] . ' (' . $request['RequestedStartWork'] . ' - ' . $request['RequestedEndWork'] . ') - ' . $request['RequestedFirstName'] . ' ' . $request['RequestedLastName'];
                                } else {
                                    $your_shift = $request['RequestedWorkDate'] . ' (' . $request['RequestedStartWork'] . ' - ' . $request['RequestedEndWork'] . ')';
                                    $swap_with = $request['RequestorWorkDate'] . ' (' . $request['RequestorStartWork'] . ' - ' . $request['RequestorEndWork'] . ') - ' . $request['RequestorFirstName'] . ' ' . $request['RequestorLastName'];
                                }
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($your_shift); ?></td>
                                <td><?php echo htmlspecialchars($swap_with); ?></td>
                                <td><?php echo htmlspecialchars($request['Status']); ?></td>
                                <td class="swap-actions">
                                    <?php if ($request['RequestedUserID'] == $user_id): ?>
                                        <form action="PT_HandleSwapRequest.php" method="post" style="display:inline;">
                                            <input type="hidden" name="request_id" value="<?php echo $request['SwapRequestID']; ?>">
                                            <button type="submit" name="action" value="approve" class="approve-button">Approve</button>
                                        </form>
                                        <form action="PT_HandleSwapRequest.php" method="post" style="display:inline;">
                                            <input type="hidden" name="request_id" value="<?php echo $request['SwapRequestID']; ?>">
                                            <button type="submit" name="action" value="reject" class="reject-button">Reject</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No swap requests found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <?php if ($total_pages > 1): ?>
            <!-- Pagination -->
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li><a href="PT_ViewSwapRequests.php?page=<?php echo $page - 1; ?>">&laquo;</a></li>
                <?php else: ?>
                    <li class="disabled"><a href="#">&laquo;</a></li>
                <?php endif; ?>

                <?php foreach ($pagination_range as $p): ?>
                    <?php if ($p == $page): ?>
                        <li class="active"><a href="#"><?php echo $p; ?></a></li>
                    <?php else: ?>
                        <li><a href="PT_ViewSwapRequests.php?page=<?php echo $p; ?>"><?php echo $p; ?></a></li>
                    <?php endif; ?>
                <?php endforeach; ?>

                <?php if ($page < $total_pages): ?>
                    <li><a href="PT_ViewSwapRequests.php?page=<?php echo $page + 1; ?>">&raquo;</a></li>
                <?php else: ?>
                    <li class="disabled"><a href="#">&raquo;</a></li>
                <?php endif; ?>
            </ul>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
