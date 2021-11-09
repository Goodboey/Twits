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

<?php

if (isset($_SESSION["userinfo_correct"]) & !$_SESSION["userinfo_correct"]){
    echo "Password or username is incorrect!";
}
if (isset($_POST['user']) & isset($_POST['pw'])) {
    $_SESSION["uid"] = $_POST['user'];
    $_SESSION["password"] = $_POST['pw'];
}

/*if (isset($_SESSION["uid"]) & isset($_POST['pw'])) {
    echo "uid = ", $_SESSION["uid"];
    echo "   &   password er også sat";
} else {
    echo "der er ikke noget sat";
}*/

if (isset($_POST['submitbutton']) & isset($_SESSION['uid'])) {
    header("Location: /~seno/miniprojekt/main.php");
}

?>

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

</html>