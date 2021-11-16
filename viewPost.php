<?php
//Required info to be able to add a comment:
/* Logged in user
 * postId
 */

session_start();
require_once '/home/mir/lib/db.php';

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

<body>
<?php
    if (isset($_SESSION['uid']) & isset($_SESSION['password'])) {
    require_once '/home/mir/lib/db.php';

    if (login($_SESSION['uid'], $_SESSION['password'])) {
        //We do nothing, if stuff is correct
    } else {
        $_SESSION["userinfo_correct"] = false;
        header("Location: /~seno/miniprojekt/login.php");
    }
    } else {
        header("Location: /~seno/miniprojekt/login.php");
    }
    if (isset($_GET['pid'])){
        //We have a pid as get argument, now we check if we have any data on this post:
        $post = get_post($_GET['pid']);
        if (empty($post)){
            //If we don't have any data on the post we just send the user back
            header("Location: /~seno/miniprojekt/main.php");
        }
    }
    $content="";
    //Modify comments
    if (isset($_GET['cid'])){
        //We also need to check if we're the author of the comment
        $comment=get_comment($_GET['cid']);
        if(!empty($comment) and !empty($comment['content'])){
            $content=$comment['content'];
        }
    }

?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //add_comment($_SESSION['uid'], $post['pid'], $_POST['content']);

    //Logik til tilføjelse af kommentarer:

    if (isset($_POST['pid'])){
        //We have a pid as get argument, now we check if we have any data on this post:
        $post = get_post($_POST['pid']);
        if (empty($post)){
            //If we don't have any data on the post we just send the user back
            header("Location: /~seno/miniprojekt/main.php");
        }else{
            $content = ($_POST["content"]);
            echo "Prøver at tilføje kommentar: " . $_SESSION['uid'] .  $post['pid'] . $content;
            add_comment($_SESSION['uid'], $post['pid'], $content);
        }
    }else{
        echo ":(";
    }

    //Logik til ændringer af posts:


    //logik til slettelse af kommentarer:


}
?>

<!--TODO We need to have a post ID to show the content below-->

<?php
$post=get_post($_GET['pid']);
$user = get_user($post['uid']);  //Vi benytter brugerid'et fra posten til at finde info om forfatteren.

$isEditingPost= ($_GET['edit']=="true" and $post['uid']==$_SESSION['uid']);

echo "<div class='col-lg-12' style='background-color:#FF8300;'><h2>" . $post['title'] . "</h2></div>" . "<h3>skrevet af: <a href=\"" . "user.php?uid=" . $user['uid'] . "\" >" .  $user['firstname'] . " " . $user['lastname'] . "</a></h3><div>". $post['date'] ."</div>";
            //Titlen bliver skrevet og et link bliver lagt ind til brugerens side med forfatterens navn

            //Indhold
            echo "<div class='col-lg-12' style='border:thick; border-style: groove; background-color: white' >";
            //Faktiske indhold:
            if($isEditingPost){
                //Hvis vi redigerer laver vi en form til vores indhold
                echo "
                <form method='post'>
                    Titel <br><input type=\"text\" name=\"title\" value=\"" . $post['title'] . "> <br>
                    Indhold <br><input type=\"textarea\"   name=\"content\" value=\"" . $post['content'] . "> <br>
                    <input type=\"submit\" value=\"Gem ændringer\" name='editPost'>
                ";


            }else{
                echo "<p>" . $post['content'] . "</p>";

            }

//. $post['content'] .
            echo "</div>";

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
                echo "<section style= 'border:thin; border-style: solid; background-color: white'>";
                $comment = get_comment($cid); //Vi henter information om den enkelte kommentar fra databasen.
                //Hvorefter vi indsætter et link til forfatterens. Og derefter kommentarens indhold.
                echo "<h5><a href=\"" . "user.php?uid=" . $comment['uid'] . "\" >" . $comment['uid']  . "</a>";


                //Sletning af egen kommentar eller kommentarer fra ens egen post
                if($comment['uid']==$_SESSION['uid'] or $post['uid']==$_SESSION['uid']){
                    echo "  <button value='Slet kommentar'></button>";

                }

                echo "</h5>";

                echo "<p>" . $comment['content'] . "</p>";
                echo "</section>";
            }

?>



<form method="post" action="<?php


echo htmlspecialchars($_SERVER["PHP_SELF"]);

?>">
    Indtast kommentar: <textarea name="content" rows="5" cols="40"><?php echo $content;?></textarea>
    <input type="number" name="pid" value="<?php echo $post['pid']; ?>">
    <br>
    <input type="submit" value="Gem kommentar">
</form>


</body>

