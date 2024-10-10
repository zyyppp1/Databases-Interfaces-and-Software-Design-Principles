<?php
session_start();
require("db.inc.php");

// 检查是否登录
if (!isset($_SESSION['username'])) {
    header('Location: login.html'); // 如果未登录，重定向到登录页面
    exit();
}

$conn = new mysqli($servername, $username, $password, $dbname);

// 初始化消息变量
$message = "";
$incidentExists = true; // 标记 Incident_ID 是否存在

// 当表单被提交时处理数据
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Fine_Amount = $_POST['Fine_Amount'];
    $Fine_Points = $_POST['Fine_Points'];
    $Incident_ID = $_POST['Incident_ID'];

    // 检查 Incident_ID 是否存在
    $incidentCheck = $conn->prepare("SELECT * FROM Incident WHERE Incident_ID = ?");
    $incidentCheck->bind_param("i", $Incident_ID);
    $incidentCheck->execute();
    $result = $incidentCheck->get_result();
    if ($result->num_rows == 0) {
        $message = "错误: Incident ID 不存在。";
        $incidentExists = false;
    }
    $incidentCheck->close();

    if ($incidentExists) {
        // 准备和绑定
        $stmt = $conn->prepare("INSERT INTO Fines (Fine_Amount, Fine_Points, Incident_ID) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $Fine_Amount, $Fine_Points, $Incident_ID);

        // 执行并检查
        if ($stmt->execute()) {
            $message = "罚款已成功关联到事故报告。";
        } else {
            $message = "错误: " . $conn->error;
        }
        $stmt->close();
    }

    // 记录操作到 AuditLog 表
    $auditUsername = $_SESSION['username']; // 获取当前登录的用户名
    $action = 'Associate Fine';
    $description = $incidentExists ? 'Associated fine with incident report: ' . $Incident_ID : 'Failed to associate fine: Incident ID does not exist';
    $auditStmt = $conn->prepare("INSERT INTO AuditLog (Username, Action, Description) VALUES (?, ?, ?)");
    $auditStmt->bind_param("sss", $auditUsername, $action, $description);
    $auditStmt->execute();
    $auditStmt->close();

    $conn->close();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Associate Fines</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        body {
            background-image: url('background1.png');
            background-size: cover;
            color: black;
        }
    </style>

</head>

<body>
        <?php include 'navbar.php'; ?>
    <div class="text">
        <h2>Associate Fines with Incident Reports</h2>
        <p><?php echo $message; ?></p>
        <form action="" method="post">
            <div class="form-group">
                <label for="Fine_Amount">Fine Amount:</label>
                <input type="number" id="Fine_Amount" name="Fine_Amount" required>
            </div>
            <div class="form-group">
                <label for="Fine_Points">Fine Points:</label>
                <input type="number" id="Fine_Points" name="Fine_Points" required>
            </div>
            <div class="form-group">
                <label for="Incident_ID">Incident ID:</label>
                <input type="number" id="Incident_ID" name="Incident_ID" required>
            </div>
            <input type="submit" value="Associate Fine" class="button">
        </form>
        <form action="main.php">
            <input type="submit" value="Return to Main" class="button">
        </form>

    </div>
</body>
</html>
