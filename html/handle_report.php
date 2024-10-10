<?php
session_start();
require("db.inc.php");
$conn = new mysqli($servername, $username, $password, $dbname);
if (!isset($_SESSION['username'])) {
    header('Location: login.html'); // 如果未登录，重定向到登录页面
    exit();
}

// 初始化变量
$showPeopleForm = false;
$showVehicleForm = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 获取表单数据
    $Incident_Report = $_POST['statement'];
    $Incident_Date = $_POST['incident_time'];
    $Vehicle_licence = $_POST['vehicle_licence']; // 确保这里的字段名与表单中的一致
    $People_licence = $_POST['people_licence']; // 确保这里的字段名与表单中的一致
    $Offence_ID = $_POST['offence_id'];

    // 初始化 People_ID 和 Vehicle_ID
    $People_ID = null;
    $Vehicle_ID = null;


    // 检查 People_licence 是否存在
    $peopleCheck = $conn->prepare("SELECT People_ID FROM People WHERE People_licence = ?");
    $peopleCheck->bind_param("s", $People_licence);
    $peopleCheck->execute();
    $peopleResult = $peopleCheck->get_result();
    if ($peopleResult->num_rows > 0) {
        $peopleRow = $peopleResult->fetch_assoc();
        $People_ID = $peopleRow['People_ID'];
    } else {
        $showPeopleForm = true;
    }

    // 检查 Vehicle_licence 是否存在
    $vehicleCheck = $conn->prepare("SELECT Vehicle_ID FROM Vehicle WHERE Vehicle_licence = ?");
    $vehicleCheck->bind_param("s", $Vehicle_licence);
    $vehicleCheck->execute();
    $vehicleResult = $vehicleCheck->get_result();
    if ($vehicleResult->num_rows > 0) {
        $vehicleRow = $vehicleResult->fetch_assoc();
        $Vehicle_ID = $vehicleRow['Vehicle_ID'];
    } else {
        $showVehicleForm = true;
    }

    // 插入新的事故报告
    if (!$showPeopleForm && !$showVehicleForm && $People_ID !== null && $Vehicle_ID !== null) {
        $stmt = $conn->prepare("INSERT INTO Incident (Incident_Report, Incident_Date, Vehicle_ID, People_ID, Offence_ID) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiii", $Incident_Report, $Incident_Date, $Vehicle_ID, $People_ID, $Offence_ID);
        $stmt->execute();

        
        $auditStmt = $conn->prepare("INSERT INTO AuditLog (Username, Action, Description) VALUES (?, ?, ?)");
        $action = 'File Incident Report';
        $description = 'Filed a new incident report with People_licence: ' . $People_licence . ', Vehicle_licence: ' . $Vehicle_licence;
        $auditStmt->bind_param("sss", $_SESSION['username'], $action, $description);
        $auditStmt->execute();
        $auditStmt->close();

        $message = "新的事故报告已提交。";
    }
}


// HTML 开始
echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";
echo "    <title>Handle Report</title>";
echo "    <link rel='stylesheet' type='text/css' href='style.css'>";
echo "</head>";
echo "<body>";
echo "    <div class='main-container'>";

// 显示结果或表单
if (isset($message)) {
    echo "<p>$message</p>";
} else {
    if ($showPeopleForm || $showVehicleForm) {
        echo "<h2>Add New People or Vehicle</h2>";
        echo "<form action='add_people_vehicle.php' method='post'>";
        echo "<input type='hidden' name='Incident_Report' value='" . htmlspecialchars($Incident_Report) . "'>";
        echo "<input type='hidden' name='Incident_Date' value='" . htmlspecialchars($Incident_Date) . "'>";
        echo "<input type='hidden' name='Offence_ID' value='" . htmlspecialchars($Offence_ID) . "'>";
        echo "<input type='hidden' name='Vehicle_licence' value='" . htmlspecialchars($Vehicle_licence) . "'>";
        echo "<input type='hidden' name='People_licence' value='" . htmlspecialchars($People_licence) . "'>";

        if ($showPeopleForm) {
            echo "<div class='form-group'>";
            echo "    <label for='People_name'>Name:</label>";
            echo "    <input type='text' id='People_name' name='People_name' class='large-input' required>";
            echo "</div>";
            echo "<div class='form-group'>";
            echo "    <label for='People_address'>Address:</label>";
            echo "    <input type='text' id='People_address' name='People_address' class='large-input' required>";
            echo "</div>";
        }

        if ($showVehicleForm) {
            echo "<div class='form-group'>";
            echo "    <label for='Vehicle_type'>Type:</label>";
            echo "    <input type='text' id='Vehicle_type' name='Vehicle_type' class='large-input' required>";
            echo "</div>";
            echo "<div class='form-group'>";
            echo "    <label for='Vehicle_colour'>Colour:</label>";
            echo "    <input type='text' id='Vehicle_colour' name='Vehicle_colour' class='large-input' required>";
            echo "</div>";
        }

        echo "<input type='submit' value='Add' class='button'>";
        echo "</form>";
    }
}

echo "    </div>"; // main-container
echo "</body>";
echo "</html>";

$conn->close();
?>
