<?php session_start(); ?>

<!doctype html>
<html>
<head>
    <link rel="icon" href="/~seno/miniprojekt/sideicon.ico">
    <style>
        .center {
            margin: auto;
            position: absolute;
            left: 50%;
            top: 50%;
            -ms-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%);
        }
    </style>
</head>
<body style="background-color:wheat">
<?php

// Hvis brugeren har sat information ind, skal vi give det til sessionen
if (!isset($_POST['user']) & isset($_POST['pw'])) {
    $_SESSION["uid"] = $_POST['user'];
    $_SESSION["password"] = $_POST['pw'];
}

require_once '/home/mir/lib/db.php';

// vi checker om de brugerdata i sessionen findes i databasen, hvis ikke, siger vi at brugernavn eller kodeord er forkert.
if (isset($_POST['submitbutton']) & login($_SESSION['uid'], $_SESSION['password']) !== true){
    echo "Password or username is incorrect!";
} elseif (isset($_POST['submitbutton']) & login($_SESSION['uid'], $_SESSION['password']) == true){
    header("Location: /~seno/miniprojekt/main.php");
}

?>

<!-- Login-form, som er centreret på skærmen, inputs bliver gemt i $_POST variabler pga. formens method -->
<div class = "center">
<form action="login.php" method=post>
    <table>
        <tr>
            <td>User name:</td>
            <td><input type="text" name="user"></td>
        </tr>
        <tr>
            <td>Password:</td>
            <td><input type="password" name="pw"></td>
        </tr>
        <tr>
            <td></td>
            <td><input type="submit" name="submitbutton" value="Login!"></td>
        </tr>
    </table>
</form>
</div>
</body>
</html>