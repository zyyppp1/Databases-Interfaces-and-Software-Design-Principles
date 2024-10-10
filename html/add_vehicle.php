<?php
session_start();
require("db.inc.php"); // 引入数据库配置文件

if (!isset($_SESSION['username'])) {
    header('Location: login.html'); // 如果未登录，重定向到登录页面
    exit();
}

$message = ''; // 用于存储操作消息
$success = false; // 标记操作是否成功

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vehicle_registration = $_POST['vehicle_registration'];
    $make = $_POST['make'];
    $model = $_POST['model'];
    $colour = $_POST['colour'];
    $licence_number = $_POST['licence_number'];
    $owner_name = $_POST['owner_name'];
    $owner_address = $_POST['owner_address'];

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // 检查车主是否已存在
    $stmt = $conn->prepare("SELECT People_ID FROM People WHERE People_licence = ?");
    $stmt->bind_param("s", $licence_number);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // 车主已存在，获取 People_ID
        $row = $result->fetch_assoc();
        $owner_id = $row['People_ID'];
    } else {
        // 车主不存在，检查是否提供了车主姓名和地址
        if (empty($owner_name) || empty($owner_address)) {
            $message = "Owner's name and address are required.";
        } else {
            // 添加新车主
            $stmt = $conn->prepare("INSERT INTO People (People_name, People_address, People_licence) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $owner_name, $owner_address, $licence_number);
            $stmt->execute();
            $owner_id = $conn->insert_id;

            // 记录添加人员操作到 AuditLog 表
            $auditUsername = $_SESSION['username'];
            $action = 'Add Person';
            $description = 'Added new person with licence: ' . $licence_number;
            $auditStmt = $conn->prepare("INSERT INTO AuditLog (Username, Action, Description) VALUES (?, ?, ?)");
            $auditStmt->bind_param("sss", $auditUsername, $action, $description);
            $auditStmt->execute();
            $auditStmt->close();
        }
    }

    if (!empty($owner_id)) {
        // 插入车辆信息
        $stmt = $conn->prepare("INSERT INTO Vehicle (Vehicle_licence, Vehicle_type, Vehicle_colour) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $vehicle_registration, $make, $model);
        $stmt->execute();
        $vehicle_id = $conn->insert_id;

        // 记录添加车辆操作到 AuditLog 表
        $action = 'Add Vehicle';
        $description = 'Added new vehicle with registration: ' . $vehicle_registration;
        $auditStmt = $conn->prepare("INSERT INTO AuditLog (Username, Action, Description) VALUES (?, ?, ?)");
        $auditStmt->bind_param("sss", $auditUsername, $action, $description);
        $auditStmt->execute();
        $auditStmt->close();

        // 将车辆与车主关联
        $stmt = $conn->prepare("INSERT INTO Ownership (People_ID, Vehicle_ID) VALUES (?, ?)");
        $stmt->bind_param("ii", $owner_id, $vehicle_id);
        $stmt->execute();

        // 记录添加所有权操作到 AuditLog 表
        $action = 'Add Ownership';
        $description = 'Associated vehicle ' . $vehicle_registration . ' with person ' . $licence_number;
        $auditStmt = $conn->prepare("INSERT INTO AuditLog (Username, Action, Description) VALUES (?, ?, ?)");
        $auditStmt->bind_param("sss", $auditUsername, $action, $description);
        $auditStmt->execute();
        $auditStmt->close();

        $message = "Vehicle added successfully.";
        $success = true;
    }

    $stmt->close();
    $conn->close();
}


?>
<!DOCTYPE html>
<html>
<head>
    <title>Add New Vehicle</title>
    <link rel="stylesheet" type="text/css" href="style2.css">
    <script type="text/javascript">
        window.onload = function() {
            <?php if (!empty($message)): ?>
                alert("<?php echo $message; ?>");
                <?php if ($success): ?>
                    window.location.href = 'main.php';
                <?php endif; ?>
            <?php endif; ?>
        };
    </script>
    
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="text">
        <h2>Add New Vehicle</h2>
        <form action="add_vehicle.php" method="post">
            <div class="form-group">
                <label>Vehicle Registration Number:</label>
                <input type="text" name="vehicle_registration" required>
            </div>
            <div class="form-group">
                <label>Make:</label>
                <input type="text" name="make" required>
            </div>
            <div class="form-group">
                <label>Model:</label>
                <input type="text" name="model" required>
            </div>
            <div class="form-group">
                <label>Colour:</label>
                <input type="text" name="colour" required>
            </div>
            <div class="form-group">
                <label>Owner's Licence Number:</label>
                <input type="text" name="licence_number" required>
            </div>
            <div class="form-group">
                <label>Owner's Name (If New):</label>
                <input type="text" name="owner_name">
            </div>
            <div class="form-group">
                <label>Owner's Address (If New):</label>
                <input type="text" name="owner_address">
            </div>
            <input type="submit" value="Add Vehicle" class="button">
            </form>
        </form>
    </div>
</body>
</html>
