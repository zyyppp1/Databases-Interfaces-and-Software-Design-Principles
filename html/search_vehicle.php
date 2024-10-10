<?php
session_start();
require("db.inc.php"); // 引入数据库配置文件

// 检查是否登录
if (!isset($_SESSION['username'])) {
    header('Location: login.html'); // 如果未登录，重定向到登录页面
    exit();
}

$searchError = ''; // 用于存储搜索错误信息
$output = ''; // 初始化输出字符串

// 检查是否有表单提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vehicle_registration = $_POST['vehicle_registration'];

    // 连接数据库
    $conn = new mysqli($servername, $username, $password, $dbname);

    // 检查连接
    if ($conn->connect_error) {
        $searchError = "Connection failed: " . $conn->connect_error;
    } else {
        // SQL 查询
        $sql = "SELECT Vehicle.Vehicle_type, Vehicle.Vehicle_colour, People.People_name, People.People_licence
                FROM Vehicle
                LEFT JOIN Ownership ON Vehicle.Vehicle_ID = Ownership.Vehicle_ID
                LEFT JOIN People ON Ownership.People_ID = People.People_ID
                WHERE Vehicle.Vehicle_licence = ?";

        // 预处理 SQL 语句
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $vehicle_registration);

        // 执行查询
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // 构建查询结果字符串
            while($row = $result->fetch_assoc()) {
                $output .= "<p>Type: " . $row["Vehicle_type"] . "<br>";
                $output .= "Colour: " . $row["Vehicle_colour"] . "<br>";
                $output .= "Owner's Name: " . $row["People_name"] . "<br>";
                $output .= "Owner's Licence: " . $row["People_licence"] . "</p><br>";
            }
        } else {
            $searchError = "No vehicle found with this registration number.";
        }

        // 记录车辆搜索操作到 AuditLog 表
        $auditUsername = $_SESSION['username']; // 获取当前登录的用户名
        $action = 'Vehicle Search';
        $description = 'Searched for vehicle registration: ' . $vehicle_registration;
        $auditStmt = $conn->prepare("INSERT INTO AuditLog (Username, Action, Description) VALUES (?, ?, ?)");
        $auditStmt->bind_param("sss", $auditUsername, $action, $description);
        $auditStmt->execute();
        $auditStmt->close();

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Vehicle</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        body {
            background-image: url('background1.png');
            background-size: cover;
            color: white;
        }
        .search-container {
            margin-top: 100px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        input[type="text"], input[type="submit"] {
            color: white; /* 修改文本颜色为白色 */
        }
        input[type="submit"] {
            display: block;
            margin: 0 auto;
        }
        .search-results {
            color: black;
            max-height: 400px; /* 设置最大高度 */
            overflow-y: auto; /* 添加垂直滚动条 */
            margin-top: 20px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            padding: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        .button {
            border: none;
            padding: 5px 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="search-container">
        <h2>Search Vehicle</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Vehicle Registration Number:</label>
                <input type="text" name="vehicle_registration">
            </div>
            <input type="submit" value="Search" class="button">
        </form>

        <!-- 显示搜索结果的条件 -->
        <?php if (!empty($output)): ?>
            <div class="search-results">
                <?php echo $output; ?>
            </div>
        <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
            <p><?php echo htmlspecialchars($searchError); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
