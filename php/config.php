
 <!-- $con = mysqli_connect("localhost:8081","root","wildwindow_db")or die("Couldn't connect"); -->


<!-- // $db_server = "localhost";
// $db_user = "root";
// $db_pass = ""; // Add your password if required
// $db_name = "wildwindow_db";
// $conn = "";

// try {
//     $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
//     if ($conn) {
//         // echo "You are connected";
//     } else {
//         echo "Couldn't connect";
//     }
// } catch (mysqli_sql_exception $e) {
//     echo "Connection failed: " . $e->getMessage();
// } -->

<?php
// Example: PDO connection (db.php or top of home.php)
$host     = 'localhost';
$dbname   = 'wildwindow_db';
$username = 'root';
$password = '';

try {
    // Create a new PDO connection
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Set error mode to exceptions
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Connection Failed: " . $e->getMessage());
}

// Example user ID
$userId = 1;

if (isset($_POST['upload'])) {
    // Upload logic...
    // After moving the file, do your DB insert with PDO:

    try {
        $stmt = $conn->prepare("INSERT INTO photos (user_id, filename) VALUES (:user_id, :filename)");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':filename', $fileName, PDO::PARAM_STR);
        $stmt->execute();
        echo "<p style='color:green;'>Database Insert (PDO) successful!</p>";
    } catch (PDOException $e) {
        echo "<p style='color:red;'>Database Error: " . $e->getMessage() . "</p>";
    }
}
?>
