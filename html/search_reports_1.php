<?php
session_start();
require("db.inc.php"); // 引入数据库配置文件

// 检查是否登录
if (!isset($_SESSION['username'])) {
    header('Location: login.html'); // 如果未登录，重定向到登录页面
    exit();
}

$searchError = '';
$output = '';
$updateMessage = '';
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

// 处理更新请求 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $incident_id = $_POST['incident_id'];
    $new_incident_report = $_POST['incident_report'];
    $new_people_id = $_POST['people_id'];
    $new_vehicle_id = $_POST['vehicle_id'];

    // 检查 People_ID 和 Vehicle_ID 是否存在
    $peopleExists = $conn->query("SELECT 1 FROM People WHERE People_ID = '$new_people_id'")->num_rows > 0;
    $vehicleExists = $conn->query("SELECT 1 FROM Vehicle WHERE Vehicle_ID = '$new_vehicle_id'")->num_rows > 0;

    if (!$peopleExists || !$vehicleExists) {
        $updateMessage = "更新失败: 提供的 People ID 或 Vehicle ID 不存在。";
    } else {
        // 获取更新前的数据
        $preUpdateSql = "SELECT People_ID, Vehicle_ID, Incident_Report FROM Incident WHERE Incident_ID = ?";
        $preUpdateStmt = $conn->prepare($preUpdateSql);
        $preUpdateStmt->bind_param("s", $incident_id);
        $preUpdateStmt->execute();
        $preUpdateResult = $preUpdateStmt->get_result()->fetch_assoc();
        $preUpdateStmt->close();

        // 更新 Incident 表
        $sql = "UPDATE Incident SET Incident_Report = ?, People_ID = ?, Vehicle_ID = ? WHERE Incident_ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siis", $new_incident_report, $new_people_id, $new_vehicle_id, $incident_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $updateMessage = "事故报告已更新。";
        } else {
            $updateMessage = "更新失败或没有变化。";
        }

        // 构建更新详情
        $updateDetails = "Incident ID: $incident_id | ";
        $updateDetails .= "People ID: " . $preUpdateResult['People_ID'] . " -> $new_people_id | ";
        $updateDetails .= "Vehicle ID: " . $preUpdateResult['Vehicle_ID'] . " -> $new_vehicle_id | ";
        $updateDetails .= "Incident Report: '" . $preUpdateResult['Incident_Report'] . "' -> '$new_incident_report'";

        // 记录更新操作到 AuditLog 表
        $auditUsername = $_SESSION['username']; // 获取当前登录的用户名
        $action = 'Update Incident';
        $description = 'Updated incident report. Details: ' . $updateDetails;
        $auditStmt = $conn->prepare("INSERT INTO AuditLog (Username, Action, Description) VALUES (?, ?, ?)");
        $auditStmt->bind_param("sss", $auditUsername, $action, $description);
        $auditStmt->execute();
        $auditStmt->close();

        $stmt->close();
    }
}

// 处理搜索请求
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
    $search = $_POST['search_term'];

    // 连接数据库
    $conn = new mysqli($servername, $username, $password, $dbname);

    // 检查连接
    if ($conn->connect_error) {
        $searchError = "Connection failed: " . $conn->connect_error;
    } else {
        // SQL 查询 
        $sql = "SELECT * FROM Incident WHERE Incident_Report LIKE '%$search%' OR  Incident_ID LIKE '%$search%' OR  Incident_Date LIKE '%$search%' OR  People_ID LIKE '%$search%'";

        // 执行查询
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // 构建查询结果字符串
            while($row = $result->fetch_assoc()) {
                $output .= "<div class='incident-report'>";
                $output .= "<form action='' method='post'>";
                $output .= "<input type='hidden' name='incident_id' value='" . $row["Incident_ID"] . "'>";
                $output .= "<p><strong>Incident ID:</strong> " . $row["Incident_ID"] . "</p>";
                $output .= "<p><strong>People ID:</strong> <input type='text' name='people_id' value='" . $row["People_ID"] . "'></p>";
                $output .= "<p><strong>Vehicle ID:</strong> <input type='text' name='vehicle_id' value='" . $row["Vehicle_ID"] . "'></p>";
                $output .= "<p><strong>Incident Report:</strong> <textarea name='incident_report'>" . $row["Incident_Report"] . "</textarea></p>";
                $output .= "<input type='submit' name='update' value='Update Report' class='button'>";
                $output .= "</form>";
                $output .= "</div><br>";
                }
        } else {
            $searchError = "No incident report found with this search term.";
        }
        $auditUsername = $_SESSION['username']; // 获取当前登录的用户名
        $action = 'Search Incident';
        $description = 'Searched for incident report: ' . $search;
        $auditStmt = $conn->prepare("INSERT INTO AuditLog (Username, Action, Description) VALUES (?, ?, ?)");
        $auditStmt->bind_param("sss", $auditUsername, $action, $description);
        $auditStmt->execute();
        $auditStmt->close();

        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Search and Update Incident Reports</title>
    <link rel="stylesheet" type="text/css" href="style2.css">
    <style>
        body {
            background-image: url('background1.png');
            background-size: cover;
            color: black;
        }
        .main-container {
            margin-top: 100px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        input[type="text"], input[type="submit"] {
            color: black;
        }
        input[type="submit"] {
            display: block;
            margin: 0 auto;
        }
        .search-results {
            color: black;
            max-height: 400px; /* 设置最大高度 */
            overflow-y: auto; /* 添加垂直滚动条 */
        }
        .incident-report {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #f9f9f9;
        }
        input[type="text"], input[type="datetime-local"], select, textarea {
            border: 1px solid #ddd; /* 边框样式 */
            padding: 8px; /* 内边距 */
            border-radius: 4px; /* 圆角边框 */
            width: 100%; /* 宽度 */
        }

        
    </style>
    <script type="text/javascript">
        window.onload = function() {
            if ("<?php echo $searchError; ?>" != "") {
                alert("<?php echo $searchError; ?>");
            }
            if ("<?php echo $updateMessage; ?>" != "") {
                alert("<?php echo $updateMessage; ?>");
            }
        };
    </script>
</head>
<body>
    <?php include 'navbar.php'; ?> <!-- 引入导航栏 -->
    <div class="text">
        <h2>Search and Update Incident Reports</h2>
        <form action="" method="post">
            <div class="form-group">
                <label>Search Term:</label>
                <input type="text" name="search_term">
                <input type="submit" name="search" value="Search" class="button">
            </div>
        </form>

        <div class="search-results">
            <?php echo $output; ?>
        </div>
    </div>
</body>
</html>
