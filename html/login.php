<?php
session_start();
require("db.inc.php"); // 引入数据库配置文件

// 定义变量以存储登录状态和错误消息
$loginError = '';
$auditUsername = ''; // 用于审计的用户名

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 获取用户输入的用户名和密码
    $inputUsername = $_POST['username'];
    $inputPassword = $_POST['password'];
    $auditUsername = $inputUsername; // 保留尝试登录的用户名

    // 使用 db.inc.php 中的变量创建数据库连接
    $conn = new mysqli($servername, $username, $password, $dbname);

    // 检查连接
    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    }

    // 使用预处理语句进行安全查询
    $stmt = $conn->prepare("SELECT username, role FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $inputUsername, $inputPassword);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // 登录成功
        $user = $result->fetch_assoc();
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = ($user['role'] == 'admin'); // 如果用户角色是 'admin'，则设置为 true

        // 记录登录成功操作到 AuditLog 表
        $action = 'Login Success';
        $description = 'User logged in successfully';
    } else {
        // 登录失败
        $loginError = "wrong username or password";

        // 记录登录失败操作到 AuditLog 表
        $action = 'Login Failure';
        $description = 'Failed login attempt';
    }

    // 插入审计日志
    $auditStmt = $conn->prepare("INSERT INTO AuditLog (Username, Action, Description) VALUES (?, ?, ?)");
    $auditStmt->bind_param("sss", $auditUsername, $action, $description);
    $auditStmt->execute();
    $auditStmt->close();

    if ($result->num_rows > 0) {
        // 重定向到其他页面
        header("Location: main.php");
        exit;
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        body {
            background-image: url('background1.png');
            background-size: cover;
            color: white; /* 设置文本颜色为白色 */
        }
        .login-container {
            margin-top: 100px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px; /* 增加表单元素之间的间距 */
        }
        input[type="text"], input[type="password"], input[type="submit"] {
            color: black; /* 输入框和按钮内的文本颜色 */
        }
        input[type="submit"] {
            display: block; /* 使按钮成为块级元素 */
            margin: 0 auto; /* 水平居中按钮 */
        }
    </style>
    <?php if ($loginError != ''): ?>
        <script type="text/javascript">
            window.onload = function() {
                alert("<?php echo $loginError; ?>");
            };
        </script>
    <?php endif; ?>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                Username: <input type="text" name="username" value="<?php echo htmlspecialchars($auditUsername); ?>"><br>
            </div>
            <div class="form-group">
                Password: <input type="password" name="password"><br>
            </div>
            <div class="form-group">
                <input type="submit" value="Login">
            </div>
        </form>
    </div>
</body>
</html>
