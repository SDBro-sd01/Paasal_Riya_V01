<?php
session_start();

include "db_connection.php";

if (isset($_POST["btn-login"])) {

   
    $login_identifier = $_POST["login_identifier"]; // username / email / mobile / nic
    $password = $_POST["password"];

    
    if (empty($login_identifier) || empty($password)) {
        $_SESSION["login_error"] = "Both fields are required.";
        header("Location: ../log_in.php");
        exit();
    }

    
    $query = $conn->prepare("SELECT user_id, username, fullname, email, mobile, nic, user_type, password 
                              FROM users 
                              WHERE BINARY username = ? OR email = ? OR mobile = ? OR nic = ?");
    $query->bind_param("ssss", $login_identifier, $login_identifier, $login_identifier, $login_identifier);
    $query->execute();
    $result = $query->get_result();

   
    if ($result->num_rows === 0) {
        $_SESSION["login_error"] = "No account found with that identifier.";
        header("Location: ../log_in.php");
        exit();
    }

    
    $user = $result->fetch_assoc();

    
    if (!password_verify($password, $user['password'])) {
        $_SESSION["login_error"] = "Incorrect password.";
        header("Location: ../log_in.php");
        exit();
    }

    
    $_SESSION["user_id"] = $user['user_id'];
    $_SESSION["username"] = $user['username'];
    $_SESSION["fullname"] = $user['fullname'];
    $_SESSION["email"] = $user['email'];
    $_SESSION["mobile"] = $user['mobile'];
    $_SESSION["nic"] = $user['nic'];
    $_SESSION["user_type"] = $user['user_type'];
    $_SESSION["logged_in"] = true;

    /* ======= ADD COOKIES HERE ======= */

    // With Expire Date

        // // Cookie expire time (1 hour example)
        // $expire = time() + (60 * 60); 

        // // abc cookie -> store user_id
        // setcookie("abc", $user['user_id'], $expire, "/");

        // // islogged cookie -> set to 1
        // setcookie("islogged", 1, $expire, "/");

            // Create array with both values
            $cookie_data = [
                "user_id" => $user['user_id'],
                "islogged" => 1
            ];

            // Encode as JSON
            $cookie_value = json_encode($cookie_data);

            // Set cookie (10 years)
            $expire = time() + (10 * 365 * 24 * 60 * 60);
            setcookie("abc", $cookie_value, $expire, "/");

    // Without Expire Date

        // // abc cookie -> store user_id (no expire time)
        // setcookie("abc", $user['user_id'], 0, "/");

        // // islogged cookie -> set to 1 (no expire time)
        // setcookie("islogged", 1, 0, "/");

        /* ======= REDIRECT ======= */

    
    header("Location: ../index.php"); 
    exit();

    $query->close();
    $conn->close();
}
?>