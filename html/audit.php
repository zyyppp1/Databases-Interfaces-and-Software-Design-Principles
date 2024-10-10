<?php
session_start();
require("db.inc.php"); // 引入数据库配置文件

$searchError = '';
$search = '';
$output = ''; // 初始化输出字符串

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $search = $_POST['search'];

    // 创建数据库连接
    $conn = new mysqli($servername, $username, $password, $dbname);

    // 检查连接
    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM AuditLog WHERE Username LIKE '%$search%' OR Action LIKE '%$search%' OR Description LIKE '%$search%'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // 构建查询结果字符串
        while($row = $result->fetch_assoc()) {
            $output .= "<div class='auditlog-entry'>";
            $output .= "Username: " . htmlspecialchars($row["Username"]) . "<br>";
            $output .= "Action: " . htmlspecialchars($row["Action"]) . "<br>";
            $output .= "Description: " . htmlspecialchars($row["Description"]) . "<br>";
            $output .= "Timestamp: " . htmlspecialchars($row["Timestamp"]) . "<br>";
            $output .= "</div><br>";
        }
    } else {
        $searchError = "没有找到匹配的审计日志";
    }

    $auditUsername = $_SESSION['username']; // 获取当前登录的用户名
    $action = 'Search Audit';
    $description = 'Searched for: ' . $search;
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
    <title>Search AuditLog</title>
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
        input[type="text"], button {
            color: black;
        }
        button {
            display: block;
            margin: 0 auto;
            background-color: #0056b3; /* 设置按钮背景颜色 */
            color: white; /* 设置按钮文字颜色为白色 */
            padding: 10px 20px; /* 设置按钮内边距 */
            border: none; /* 移除边框 */
            border-radius: 4px; /* 设置圆角边框 */
            cursor: pointer; /* 设置鼠标指针为手形 */
        }
        .auditlog-results {
            color: black;
            max-height: 400px; /* 设置最大高度 */
            overflow-y: auto; /* 添加垂直滚动条 */
            max-width: 600px; /* 设置最大宽度 */
            margin: 0 auto; /* 居中显示 */
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            display: none; /* 默认不显示 */
        }
        .auditlog-entry {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #f9f9f9;
            color: black;
        }
    </style>
    <?php if ($searchError != ''): ?>
        <script type="text/javascript">
            window.onload = function() {
                alert("<?php echo $searchError; ?>");
            };
        </script>
    <?php endif; ?>
</head>
<body>
    <?php include 'navbar.php'; ?> <!-- 引入导航栏 -->
    <div class="search-container">
        <h2>Search AuditLog</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                Search (Username, Action, Description): <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"><br>
            </div>
            <div class="form-group">
                <button type="submit">Search</button>
            </div>
        </form>

        <?php if (!empty($output)): ?>
            <div class="auditlog-results" style="display: block;">
                <?php echo $output; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
