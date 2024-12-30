<?php
include 'php/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $follower_id = $_SESSION['user_id'];
    $followee_id = (int)$_POST['followee_id'];
    $action = $_POST['action'];

    if ($action === 'follow') {
        // Add a follow relationship
        $query = "INSERT INTO followers (follower_id, followee_id) VALUES (:follower_id, :followee_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':follower_id', $follower_id, PDO::PARAM_INT);
        $stmt->bindValue(':followee_id', $followee_id, PDO::PARAM_INT);
        $stmt->execute();
    } elseif ($action === 'unfollow') {
        // Remove the follow relationship
        $query = "DELETE FROM followers WHERE follower_id = :follower_id AND followee_id = :followee_id";
        $stmt = $conn->prepare($query);
        $stmt->bindValue(':follower_id', $follower_id, PDO::PARAM_INT);
        $stmt->bindValue(':followee_id', $followee_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Redirect back to the profile page
    header("Location: view_user.php?id=$followee_id");
    exit;
}
?>
