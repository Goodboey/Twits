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
    </style>
</head>
<html>

<body style="background-color:wheat">

</body>

<?php
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
            echo "<div>";
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
            $comments = get_cids_by_pid($post); //Vi henter et array af comment ids til oplæggets id
            foreach ($comments as $cid) { //Vi kører igennem arrayet for hvert kommentar ID
                echo "<section style= 'border:thin; border-style: solid; background-color: white'>";
                $comment = get_comment($cid); //Vi henter information om den enkelte kommentar fra databasen.
                //Hvorefter vi indsætter et link til forfatterens. Og derefter kommentarens indhold.
                echo "<h5><a href=\"" . "user.php?uid=" . $comment['uid'] . "\" >" . $comment['uid']  . "</a></h5>";
                echo "<p>" . $comment['content'] . "</p>";
                echo "</section>";
            }
            echo "</div><div class='row'> <br> </div>";
        }


        ?>
    </div>

    <div class='col-lg-2'>
        <?php
        echo "<p><a href='logout.php'>Log ud</p></a>";
        ?>
    </div>
</div>

</html>