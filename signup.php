<?php
require_once '/home/mir/lib/db.php';
session_start();



//Validate that we don't try to create an already existing user.
function userAlreadyExists($userID){
    $allUsers=get_uids();
    if(in_array($userID,$allUsers)){ //Dunno if this actually works, but let's test it?!
        return true;
    }
    return false;

}
echo "<!doctype html>
<html>
  <head>
    <title>Signup</title>
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
  <body>
  <div class='center'>";

if (isset($_POST['username']))
{
    $uid = $_POST['username'];
    //We have gotten a userID as input into signup.
    //First we check if this user already exists
    if(empty($uid) or empty($_POST['password']) or empty($_POST['firstname'])){
        echo "<p>Please enter a username and a password to create an account</p>";
    }else{
        if(!userAlreadyExists($uid)){
            //We create a user with the inserted data:
            add_user($uid, $_POST['firstname'], $_POST['lastname'], $_POST['password']);
            if (login($uid,$_POST['password'])){
                $_SESSION['uid']= $uid;
                $_SESSION['password']=$_POST['password'];
                header("Location: /~seno/miniprojekt/main.php");
            }else{
                header("Location: /~seno/miniprojekt/login.php");
            }

        }else{
            //User exists, either we can just login with the password provided or we give them error
            if(login($uid,$_POST['password'])){
                //successfully logged in with the infomation! YAaay
                $_SESSION['uid']= $uid;
                $_SESSION['password']=$_POST['password'];
                header("Location: /~seno/miniprojekt/main.php");
            }else{
                //Nono, incorrect password for already existing user.
                echo "<p >Error, already existing user with different password than provided </p>";
            }
        }
    }


}
echo "
    <form method=\"POST\" action=\"\">
    
      Brugernavn <br><input type=\"text\" name=\"username\"> <br>
      Kodeord <br><input type=\"text\" name=\"password\"> <br>
      Fornavn <br><input type=\"text\" name=\"firstname\"> <br>
      Efternavn <br><input type=\"text\" name=\"lastname\"> <br>
      <input type=\"submit\" value=\"Signup\">
    </form>
    </div>
  </body>
</html>";