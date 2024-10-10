<?php
session_start();
require("db.inc.php");

// 检查是否登录且为管理员
if (!isset($_SESSION['username']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.html'); // 如果未登录或不是管理员，重定向到登录页面
    exit();
}

$conn = new mysqli($servername, $username, $password, $dbname);

// 初始化消息变量
$message = "";
$accountCreated = false; // 新增变量来标记账户是否创建成功

// 当表单被提交时处理数据
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = "police"; // 默认角色为警官
    
    // 准备和绑定
    $stmt = $conn->prepare("INSERT INTO Users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $role);

    // 执行并检查
    if ($stmt->execute()) {
        // 记录操作到 AuditLog 表
        $auditUsername = $_SESSION['username']; // 获取当前登录的管理员用户名
        $action = 'Create Account';
        $description = 'Created new police officer account: ' . $username;
        $auditStmt = $conn->prepare("INSERT INTO AuditLog (Username, Action, Description) VALUES (?, ?, ?)");
        $auditStmt->bind_param("sss", $auditUsername, $action, $description);
        $auditStmt->execute();
        $auditStmt->close();

        $accountCreated = true; // 标记账户已成功创建
    } else {
        $message = "错误: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Police Officer</title>
    <link rel="stylesheet" type="text/css" href="style2.css">
    <style>
        body {
            background-image: url('background1.png');
            background-size: cover;
            color: black;
        }
        .button {
            margin-top: 20px; /* 增加与上方元素的间距 */
        }
    </style>
    <script type="text/javascript">
        window.onload = function() {
            <?php if ($accountCreated): ?>
                alert("New police officer account created successfully!");
                window.location.href = 'main.php';
            <?php endif; ?>
        };
    </script>

</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="text">
        <h2>Create New Police Officer Account</h2>
        <p><?php echo $message; ?></p>
        <form action="" method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <input type="submit" value="Create Account" class="button">
        </form>
        <form action="main.php">
            <input type="submit" value="Return to Main" class="button">
        </form>

    </div>
</body>
</html>
