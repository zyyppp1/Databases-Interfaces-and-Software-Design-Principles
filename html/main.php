<?php
session_start();  // 启动会话

// 检查用户是否已登录
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

// 检查用户是否为管理员
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Main Page</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        body {
            background-image: url('background2.png');
            background-size: cover;
        }
        .login-container {
            margin-top: 100px;
            text-align: center;
        }
        h2 {
            color: black;
        }
        .link-container .button {
            margin: 5px; /* 减少按钮的外边距 */
            padding: 10px 15px; /* 调整按钮的内边距 */
        }
    </style>
</head>
<body>
    <div class="main-container">
        <h2>Welcome to the Police System</h2>
        <div class="link-container">
            <!-- 常规用户功能 -->
            <a href="search.php" class="button">Search People</a>
            <a href="change_password.php" class="button">Change Password</a>
            <a href="search_vehicle.php" class="button">Search Vehicle</a>
            <a href="add_vehicle.php" class="button">Add Vehicle</a>
            <a href="file_report.php" class="button">File Report</a>
            <a href="search_reports_1.php" class="button">Search Report</a>
            <a href="audit.php" class="button">Audit</a>

            <?php if ($isAdmin): ?>
                <!-- 管理员特有功能 -->
                <a href="create_police_officer.php" class="button">Create Police Officer</a>
                <a href="associate_fines.php" class="button">Associate Fines</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
