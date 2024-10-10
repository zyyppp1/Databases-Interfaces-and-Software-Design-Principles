<?php
session_start();

require("db.inc.php");  // 引入数据库配置文件

// 检查是否登录
if (!isset($_SESSION['username'])) {
    header('Location: login.html'); // 如果未登录，重定向到登录页面
    exit();
}

$changePasswordError = '';
$passwordChanged = false; // 新增变量来标记密码是否更改成功

// 处理表单提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    // 创建数据库连接
    $conn = new mysqli($servername, $username, $password, $dbname);

    // 检查连接
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $username = $_SESSION['username'];
    $auditUsername = $username; // 获取当前登录的用户名
    $action = 'Password Change Attempt';
    $description = '';

    // 验证新密码是否匹配
    if ($new_password != $confirm_new_password) {
        $description = "Password change failed: New passwords do not match.";
        $changePasswordError = "New passwords do not match!";
    } else {
        // 验证旧密码
        $sql = "SELECT password FROM Users WHERE username = '$username'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['password'] != $old_password) {
                $description = "Password change failed: Old password is incorrect.";
                $changePasswordError = "Old password is incorrect!";
            } else {
                // 更新密码
                $sql = "UPDATE Users SET password = '$new_password' WHERE username = '$username'";
                if ($conn->query($sql) === TRUE) {
                    $description = "Password changed successfully.";
                    $passwordChanged = true; // 标记密码已成功更改
                } else {
                    $description = "Password change failed: " . $conn->error;
                    $changePasswordError = "Error updating password: " . $conn->error;
                }
            }
        } else {
            $description = "Password change failed: User not found.";
            $changePasswordError = "User not found!";
        }
    }

    // 记录密码更改操作到 AuditLog 表
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
    <title>Change Password</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="search-container">
        <h2>Change Password</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Old Password:</label>
                <input type="password" name="old_password">
            </div>
            <div class="form-group">
                <label>New Password:</label>
                <input type="password" name="new_password">
            </div>
            <div class="form-group">
                <label>Confirm New Password:</label>
                <input type="password" name="confirm_new_password">
            </div>
            <input type="submit" value="Change Password" class="button">
        </form>
    </div>
</body>
</html>
