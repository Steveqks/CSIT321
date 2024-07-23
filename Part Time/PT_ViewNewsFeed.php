<?php
session_start();
include 'db_connection.php';
include '../Session/session_check_user_PT.php';

$user_id = $_SESSION['UserID'];
$FirstName = $_SESSION['FirstName'];
$companyID = $_SESSION['CompanyID'];
$role = $_SESSION['Role'];

// Connect to the database
$conn = OpenCon();

// Fetch news feed for the user's company
$sql = "SELECT CONCAT(b.FirstName, ' ', b.LastName) AS fullName, a.NewsFeedID, a.NewsTitle, a.NewsDesc, a.DatePosted 
        FROM newsfeed a
        INNER JOIN existinguser b ON a.ManagerID = b.UserID
        WHERE b.CompanyID = ?
        ORDER BY a.DatePosted DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $companyID);
$stmt->execute();
$result = $stmt->get_result();
$newsFeed = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
CloseCon($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View News Feed - Part-Time Staff</title>
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

        .content-section {
            padding: 20px;
            flex-grow: 1;
        }

        .news-header {
            display: inline-flex;
            align-items: center;
            border-bottom: 1px solid black;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }

        .news-header i {
            margin-right: 10px;
        }

        .news-header h2 {
            margin: 0;
        }

        .news-feed {
            margin-bottom: 20px;
        }

        .news-feed .name-date {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
        }

        .news-feed .title {
            font-weight: bold;
        }

        .news-feed .description {
            margin: 5px 0;
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
        
        <!-- RIGHT SECTION (NEWS FEED) -->
        <div class="content-section">
            <div class="news-header">
                <i class="fas fa-newspaper"></i>
                <h2>View News Feed</h2>
            </div>
            <div class="news-feed-list">
                <?php if (count($newsFeed) > 0): ?>
                    <?php foreach ($newsFeed as $news): ?>
                        <div class="news-feed">
                            <div class="name-date">
                                <span><?php echo htmlspecialchars($news['fullName']); ?></span>
                                <span><?php echo date('F j, Y', strtotime($news['DatePosted'])); ?></span>
                            </div>
                            <div class="title"><?php echo htmlspecialchars($news['NewsTitle']); ?></div>
                            <div class="description"><?php echo htmlspecialchars($news['NewsDesc']); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No news feed available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
