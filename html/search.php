<?php
session_start();
require("db.inc.php"); // 引入数据库配置文件

$searchError = '';
$search = '';
$results = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $search = $_POST['search'];

    // 创建数据库连接
    $conn = new mysqli($servername, $username, $password, $dbname);

    // 检查连接
    if ($conn->connect_error) {
        die("连接失败: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM People WHERE People_name LIKE '%$search%' OR People_licence LIKE '%$search%'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // 存储每行数据
        while($row = $result->fetch_assoc()) {
            $results[] = $row;
        }

        // 记录搜索操作到 AuditLog 表
        $auditUsername = $_SESSION['username']; // 获取当前登录的用户名
        $action = 'People Search';
        $description = 'Searched people for: ' . $search;
        $auditStmt = $conn->prepare("INSERT INTO AuditLog (Username, Action, Description) VALUES (?, ?, ?)");
        $auditStmt->bind_param("sss", $auditUsername, $action, $description);
        $auditStmt->execute();
        $auditStmt->close();

    } else {
        $searchError = "No result found!!!";
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Search People</title>
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
        input[type="text"], input[type="submit"] {
            color: white;
        }
        input[type="submit"] {
            display: block;
            margin: 10 auto;
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
        
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="search-container">
        <h2>Search People</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                Name or License Number: <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"><br>
            </div>
            <input type="submit" value="Search" class="button search-button">
        </form>


        <?php if (!empty($results)): ?>
            <div class="search-results">
                <table>
                    <tr>
                        <th>Name</th>
                        <th>Address</th>
                        <th>License</th>
                    </tr>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['People_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['People_address']); ?></td>
                            <td><?php echo htmlspecialchars($row['People_licence']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
            <p><?php echo htmlspecialchars($searchError); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
