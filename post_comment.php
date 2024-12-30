<?php
include 'php/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $observation_id = (int)$_POST['observation_id'];
    $user_id = $_SESSION['user_id']; // Ensure the user is logged in
    $comment = htmlspecialchars($_POST['comment']);

    $query = "INSERT INTO comments (observation_id, user_id, comment) VALUES (:observation_id, :user_id, :comment)";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':observation_id', $observation_id, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':comment', $comment, PDO::PARAM_STR);

    if ($stmt->execute()) {
        header("Location: view_observation.php?id=$observation_id");
        exit;
    } else {
        echo "Failed to post comment.";
    }
}
?>
