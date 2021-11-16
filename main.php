<?php

session_start();

?>

<!doctype html>

<head>
    <link rel="icon" href="/~seno/miniprojekt/sideicon.ico">
    <title>Twits</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <style>
        img {
            max-width: 100%;
            max-height: 100%
        }
        a.fill-div {
            display: block;
            height: 100%;
            width: 100%;
            text-decoration: none;
            position: absolute;
        }
    </style>
</head>
<html>

<body style="background-color:wheat">


<?php
$loggedtrue= false;
if (isset($_SESSION['uid']) & isset($_SESSION['password'])) {
    require_once '/home/mir/lib/db.php';

    if (login($_SESSION['uid'], $_SESSION['password'])) {
        $loggedtrue = true;
    } else {
        $_SESSION["userinfo_correct"] = false;
        header("Location: /~seno/miniprojekt/login.php");
    }
} else {
    header("Location: /~seno/miniprojekt/login.php");
}

?>
<div class='row'>

    <div class='col-lg-1'>


    </div>
    <div class='col-lg-9' style='border:darkslateblue; border-style: solid; overflow-y: auto; max-height: 100vh'>
        <?php
        if ($loggedtrue == true){
            echo "<div class='col-lg-12' style='border:black; border-style: solid;'> 
            <form action='main.php' method=post>
            <table>
                <tr>
                    <td>Titel:</td>
                    <td><input type='text' name='ptitle'></td>
                </tr>
                <tr>
                    <td>Vis mig dine tanker:</td>
                    <td><input type='text' name='pcontent'></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type='submit' name='submitbtn' value='Post!'></td>
                </tr>
            </table>
        </form>
                  </div>";
            if (/*isset($_POST['ptitel']) & isset($_POST['pcontent']) & */isset($_POST['submitbtn'])) {
                add_post($_SESSION['uid'], $_POST['ptitle'], $_POST['pcontent']);
                header("Refresh:0");
            }
        }

        $pids = get_pids();
        foreach ($pids as $pid) {
            $posts[] = get_post($pid);
        }
        $order = array();
        foreach ($posts as $key => $value) {
            $order[] = strtotime($value['date']);
        }
        array_multisort($order, SORT_DESC, $posts);
        foreach ($posts as $post) {
            $user = get_user($post['uid']);  //Vi benytter brugerid'et fra posten til at finde info om forfatteren.
            echo "<div onclick='location.href=".'"postview.php"'.";' style='cursor: pointer;'>";
            //echo "<a href='postview.php' class='fill-div'></a>";
            //Titel og forfatter
            echo "<div class='row'>";

            echo "<div class='col-lg-12' style='background-color:#FF8300;'><h2>" . $post['title'] . "</h2></div>" . "<h3>skrevet af: <a href=\"" . "user.php?uid=" . $user['uid'] . "\" >" .  $user['firstname'] . " " . $user['lastname'] . "</a></h3><div>". $post['date'] ."</div>";
            //Titlen bliver skrevet og et link bliver lagt ind til brugerens side med forfatterens navn

            //Indhold
            echo "<p><div class='col-lg-12' style='border:thick; border-style: groove; background-color: white' >" . $post['content'] . "</div></p>";

            //Billeder

            $images = get_iids_by_pid($post['pid']); //Vi henter alle de billed id'er som er knyttet til posten.
            echo "<div class = 'row'>";
            foreach ($images as $iid) { //for hvert billede knyttet til posten tilføjer vi et html img tag med billedets path.
                $path = get_image($iid)['path'];

                echo "<div class = 'col-lg-6'><img class='rounded float-right' src=\"" . $path . "\"" . "></div>";
            }
            echo "</div>";
            echo "</div>";

            //Kommentarer
            $comments = get_cids_by_pid($post['pid']); //Vi henter et array af comment ids til oplæggets id
            foreach ($comments as $cid) { //Vi kører igennem arrayet for hvert kommentar ID

                if($toomanycomments > 4){
                    //echo "<div><h5> 'Vis alle kommentarer' </h5></div>";
                    $toomanycomments = 0;
                    //break;
                }
                echo "<section style= 'border:thin; border-style: solid; background-color: white'>";
                $comment = get_comment($cid); //Vi henter information om den enkelte kommentar fra databasen.
                //Hvorefter vi indsætter et link til forfatterens. Og derefter kommentarens indhold.
                echo "<h5><a href=\"" . "user.php?uid=" . $comment['uid'] . "\" >" . $comment['uid']  . "</a>";

                //Edit a comment written by us:
                if($loggedtrue and $comment['uid']==$_SESSION['uid']){
                    echo "  <a href=''>Rediger kommentar</a>";

                }

                //Sletning af egen kommentar eller kommentarer fra ens egen post
                if($loggedtrue and ($comment['uid']==$_SESSION['uid'] or $post['uid']==$_SESSION['uid'])){
                    echo "  <a href=''>Slet kommentar</a>";
                }

                echo "</h5>";

                echo "<p>" . $comment['content'] . "</p>";
                echo "</section>";
                $toomanycomments++;

                if ($toomanycomments == count($comments)){
                    $toomanycomments = 0;
                }

            }
            echo "</div><div class='row'> <br> </div>";
        }


        ?>
    </div>

    <div class='col-lg-2'>
        <?php
        if($loggedtrue) {
            echo "<p><a href='logout.php'>Log ud</p></a>";
        }else{
            echo "<p><a href='login.php.php'>Log ind</p></a>";
            echo "<p><a href='signup.php'>Opret konto</a></p>";
        }


        ?>
    </div>
</div>
</body>
</html>