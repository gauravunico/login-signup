<?php
session_start();
include 'connect.php';

if (isset($_POST['signup'])) {
    $firstName = $_POST['fName'];
    $lastName = $_POST['lName'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $checkEmail = $conn->prepare("SELECT * FROM user WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $result = $checkEmail->get_result();

    if ($result->num_rows > 0) {
        echo "Email Address Already Exists!";
    } else {
        $insertQuery = $conn->prepare("INSERT INTO user (firstName, lastName, email, password) VALUES (?, ?, ?, ?)");
        $insertQuery->bind_param("ssss", $firstName, $lastName, $email, $hashedPassword);

        if ($insertQuery->execute()) {
            header("Location: index.php");
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

if (isset($_POST['signIn'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = $conn->prepare("SELECT * FROM user WHERE email = ?");
    $sql->bind_param("s", $email);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['email'] = $row['email'];
            header("Location: homepage.php");
            exit();
        } else {
            echo "Incorrect Password!";
        }
    } else {
        echo "Email Not Found!";
    }
}
?>
