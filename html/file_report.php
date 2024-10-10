<!DOCTYPE html>
<html>
<head>
    <title>Incident Report</title>
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
                /* style2.css */
        /* 其他样式保持不变 */

        /* 添加输入框的样式 */
        input[type="text"], input[type="datetime-local"], select, textarea {
            border: 1px solid #ddd; /* 边框样式 */
            padding: 8px; /* 内边距 */
            border-radius: 4px; /* 圆角边框 */
            width: 100%; /* 宽度 */
        }

        /* 调整表单组样式 */
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block; /* 使标签成为块级元素 */
            margin-bottom: 5px; /* 在标签和输入框之间添加间距 */
        }

    </style>

</head>
<body>
    <?php include 'navbar.php'; ?> <!-- 引入导航栏 -->


    <h2>File a New Incident Report</h2>
    <form action="handle_report.php" method="post">
        <div class="form-group">
            <label for="statement">Incident Statement:</label>
            <textarea id="statement" name="statement" required></textarea>
        </div>

        <div class="form-group">
            <label for="incident_time">Time of Incident:</label>
            <input type="datetime-local" id="incident_time" name="incident_time" required>
        </div>

        <div class="form-group">
            <label for="vehicle_licence">Vehicle Licence Number:</label>
            <input type="text" id="vehicle_licence" name="vehicle_licence">
        </div>

        <div class="form-group">
            <label for="people_licence">Person Licence Number:</label>
            <input type="text" id="people_licence" name="people_licence">
        </div>

        <div class="form-group">
            <label for="offence_id">Offence:</label>
            <select id="offence_id" name="offence_id">
                <?php 
                require("db.inc.php"); 
                $conn = new mysqli($servername, $username, $password, $dbname);
                $sql = "SELECT Offence_ID, Offence_description FROM Offence";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row["Offence_ID"] . "'>" . $row["Offence_description"] . "</option>";
                    }
                } else {
                    echo "<option>No offences found</option>";
                }
                ?>
            </select>
        </div>

        <input type="submit" value="Submit Report" class="button">
    </form>


</div>

</body>
</html>
