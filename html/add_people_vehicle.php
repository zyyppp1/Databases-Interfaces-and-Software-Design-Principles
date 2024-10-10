<?php
session_start();
require("db.inc.php");
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查数据库连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
}

$message = '';
$success = false;
$People_licence = $_POST['People_licence'];
$Vehicle_licence = $_POST['Vehicle_licence'];

// 检查是否有新的人员数据提交
if (isset($_POST['People_name'])) {
    $People_name = $_POST['People_name'];
    $People_address = $_POST['People_address'];
    $People_licence = $_POST['People_licence'];

    // 插入新的人员数据
    $stmt = $conn->prepare("INSERT INTO People (People_name, People_address, People_licence) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $People_name, $People_address, $People_licence);
    $stmt->execute();
    $People_ID = $conn->insert_id; // 获取新插入的 People_ID
    $stmt->close();

    // 记录到 AuditLog
    $auditStmt = $conn->prepare("INSERT INTO AuditLog (Username, Action, Description) VALUES (?, ?, ?)");
    $action = 'Add Person';
    $description = 'Added new person: ' . $People_name;
    $auditStmt->bind_param("sss", $_SESSION['username'], $action, $description);
    $auditStmt->execute();
    $auditStmt->close();
}

// 检查是否有新的车辆数据提交
if (isset($_POST['Vehicle_type'])) {
    $Vehicle_type = $_POST['Vehicle_type'];
    $Vehicle_colour = $_POST['Vehicle_colour'];
    $Vehicle_licence = $_POST['Vehicle_licence'];

    // 插入新的车辆数据
    $stmt = $conn->prepare("INSERT INTO Vehicle (Vehicle_type, Vehicle_colour, Vehicle_licence) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $Vehicle_type, $Vehicle_colour, $Vehicle_licence);
    $stmt->execute();
    $Vehicle_ID = $conn->insert_id; // 获取新插入的 Vehicle_ID
    $stmt->close();

    // 记录到 AuditLog
    $auditStmt = $conn->prepare("INSERT INTO AuditLog (Username, Action, Description) VALUES (?, ?, ?)");
    $action = 'Add Vehicle';
    $description = 'Added new vehicle: ' . $Vehicle_licence;
    $auditStmt->bind_param("sss", $_SESSION['username'], $action, $description);
    $auditStmt->execute();
    $auditStmt->close();
}

// 获取原始事故报告的数据
$Incident_Report = $_POST['Incident_Report'];
$Incident_Date = $_POST['Incident_Date'];
$Offence_ID = $_POST['Offence_ID'];

// 插入新的事故报告
$stmt = $conn->prepare("INSERT INTO Incident (Incident_Report, Incident_Date, Vehicle_ID, People_ID, Offence_ID) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssiii", $Incident_Report, $Incident_Date, $Vehicle_ID, $People_ID, $Offence_ID);
$stmt->execute();

// 记录到 AuditLog
$auditStmt = $conn->prepare("INSERT INTO AuditLog (Username, Action, Description) VALUES (?, ?, ?)");
$action = 'File Incident Report';
$description = 'Filed a new incident report';
$auditStmt->bind_param("sss", $_SESSION['username'], $action, $description);
$auditStmt->execute();
$auditStmt->close();

$stmt->close();
$conn->close();
$message = "New incident report have already filed";
$success = true;

?>
<!DOCTYPE html>
<html>
<head>
    <title>Add People or Vehicle</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<div class="main-container">
    <h2>Add New People or Vehicle</h2>
    <p><?php echo $message; ?></p>
</div>

<?php if ($success): ?>
    <script type="text/javascript">
        alert("<?php echo $message; ?>");
        window.location.href = 'main.php';
    </script>
<?php endif; ?>

</body>
</html>
