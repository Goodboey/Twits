<?php

session_start();

?>

<!doctype html>

<head>
    <link rel="icon" href="/~seno/miniprojekt/goofy.ico">
    <title>Twits</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <style>
        body {
            overflow: hidden; /* vi gemmer scrollbaren */
            }
        img { /* prøver at fikse billeder */
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
	
	    .image-wrapper > div {
		    position: relative;
	    }
	
	    .image-wrapper > div > div {
		    position: absolute;
		    top: 0;
		    right: 0;
		    bottom: 0;
		    left: 0;
	    }
	
	    .image-wrapper > div > div > img {
		    max-width: 100%;
	    }
        p{
            overflow-wrap: anywhere;
        }
    </style>
</head>
<html>

<body style="background-color:wheat">


<?php
require_once '/home/mir/lib/db.php';
$loggedtrue= false; // altid sætte login = false som standard før vi checker om brugeren er logget ind (ellers holder logikken ikke)

// Check om brugeren er logget ind.
if (isset($_SESSION['uid']) & isset($_SESSION['password'])) {
    if (login($_SESSION['uid'], $_SESSION['password'])) {
        $loggedtrue = true;
    }
}

?>

<div class="jumbotron jumbotron-fluid" style= "background: orange"> <!-- Flot overskrift til siden med en jumbotron -->
    <div class="container" >
        <h1 class="display-4">Twits</h1>
        <p class="lead">Saml folket på ét sted, på internettet, når som helst.</p>
    </div>
</div>

<div class='row'>

    <!-- Filler kolonne, for design-reasons.. -->
    <div class='col-lg-1'>


    </div>
    <div class='col-lg-9' style='border:darkslateblue; border-style: solid; overflow-y: auto; max-height: 100vh'>
        <?php
        // Hvis vi er logget ind
        if ($loggedtrue == true){
            echo "<div class= row style='border:grey; border-style: solid; width: relative;'> 
            <form action='main.php' method=post enctype='multipart/form-data' style='width: relative'>
            <table>
                <tr>
                    <td>Titel:</td>
                </tr>
                <tr>
                    <td><input type='text' name='ptitle'></td>
                </tr>
                <tr>
                    <td><textarea type='text' name='pcontent' rows='6' cols='100' width='100%' placeholder='Hvad har du på hjerte?' style = 'resize: none;'></textarea></td>
                </tr>
                <tr>
                    <td><input type='file' class='btn btn-success btn-lg' name='picture[]' value='Upload et billede!' multiple='multiple'></td>
                </tr>
                <tr>
                    <td><input type='submit' class='btn btn-warning btn-lg' name='submitbtn' value='Post!'></td>
                </tr>
            </table>
            </form>
            </div>";
            // Hvis brugeren trykker på submitknap og ihvertfald har udfyldt opslag og titel, så lader vi brugeren poste opslaget.
            if (isset($_POST['submitbtn']) & !empty($_POST['ptitle']) & !empty($_POST['pcontent'])) {
                // tilføjer opslaget og henter post id til en variabel
                $pid = add_post($_SESSION['uid'], $_POST['ptitle'], $_POST['pcontent']);
                // hvis brugeren har tilføjet et billede tilføjer vi billedet til postet.
                if (isset($_FILES['picture']['type'])){
                    // vi finder filtyperne for alle billeder og rykker dem til den rigtige placering på serveren
                    // herefter tilføjer vi billedet til opslaget.
                    // vi itererer over alle filoverførelser, hvor i er mindre end mængden af billeder..
                    for ($i=0; $i < count($_FILES['picture']['name']); $i++){
                        
                        $tempFilePath = $_FILES['picture']['tmp_name'][$i]; // Vi tager den temp fil-sti fra det i'de billede
                        $tempType = "." . substr($_FILES['picture']['type'][$i], strpos($_FILES['picture']['type'][$i],'/')+1); // Vi kigger på filtypen og gemmer den til en enkel streng
                        $iid = add_image($tempFilePath, $tempType); // vi tilføjer billedet, og tilskriver det en variabel da den returnerer image-ID.
                    
                        if ($iid > 0){ // Check om billede id'et er større end 0, måske lidt dumt skrevet men det sørger for at spøgelses billeder ikke kommer med.
                            $newFilePath = $_FILES['picture']['name'][$i]; //Vi finder den nye filpath under name
                            move_uploaded_file($tempFilePath, $newFilePath); //Vi flytter den uploadede fil til den nye filepath
                            add_attachment($pid, $iid); //Vi tilføjer billedet til det korresponderende post
                        }
                    }               
                }
                
                header("Refresh:0"); // Vi refresher siden så brugeren kan se sit nye post
            }
        }

        $pids = get_pids(); // vi fanger post id'erne fra databasen
        
        foreach ($pids as $pid) { // for hver post tager vi posts ud til et array
            $posts[] = get_post($pid);
        }

        $order = array(); // laver et ordensarray
        
        foreach ($posts as $post) { // Vi gemmer alle datoerne til vores ordensarray
            $order[] = $post['date'];
        }
        
        array_multisort($order, SORT_DESC, $posts); // multisort tager et ordens array, med nøgler (datoer) og sorterer datoerne i descending order så vi har de nyeste posts først.
        
        foreach ($posts as $post) {
            $user = get_user($post['uid']);  //Vi benytter brugerid'et fra posten til at finde info om forfatteren.
            
            //Titel og forfatter
            
            echo "<div class= 'row' style='border:solid chartreuse; border-style:outset; width: relative'>";
 

            // Vi anvender bootstrap grid til at udforme vores post - "tidslinje"
            echo "<div class='col-lg-12' style='background-color:#FF8300;'><h2>" . htmlentities($post['title']) . "</h2></div>" . 
                 "<h3>skrevet af: ".  htmlentities($user['firstname']) . " " . htmlentities($user['lastname']) . "</h3><div>". $post['date'] ."</div>";
            
                 
            //Titlen bliver skrevet og et link bliver lagt ind til brugerens side med forfatterens navn
            if($loggedtrue){
                echo "<div onclick='location.href=".'"viewpost.php?pid='.$post['pid'].'"'.";' style='cursor: pointer;'>"; // hvis man er logget ind kan man trykke på postet
            } else {
                echo "<div>"; // hvis man ikke er logget ind laver vi bare en tom åben-div, den bliver afsluttet ligemeget hvad.
            }
            //Indhold
            echo "<p><div class='col-lg-12' style='border:thick; border-style: groove; background-color: white' >" . htmlentities($post['content']) . "</div></p>";

            //Billeder

            

            $images = get_iids_by_pid($post['pid']); //Vi henter alle de billed id'er som er knyttet til posten.
            
            echo "<div class = 'row'>";
            foreach ($images as $iid) { //for hvert billede knyttet til posten tilføjer vi et html img tag med billedets path.
                $path = get_image($iid)['path'];

                // Vi gør billedet lidt flottere med noget css og image wrapper så det ser ud som vi vil have det.
                echo "<div class = 'image-wrapper' style='max-width: 400px;'><div style='padding: 0 0 60%'><div>
                      <img class='rounded float-right' src=\"" . $path . "\"" . "></div></div></div>";
            }
            echo "</div>";
            

            //Kommentarer
            $comments = get_cids_by_pid($post['pid']); //Vi henter et array af comment ids til oplæggets id
            foreach ($comments as $cid) { //Vi kører igennem arrayet for hvert kommentar ID

                echo "<section style= 'border:thin; border-style: solid; background-color: white'>";
                $comment = get_comment($cid); //Vi henter information om den enkelte kommentar fra databasen.
                //Hvorefter vi indsætter et link til forfatterens. Og derefter kommentarens indhold.
                echo "<h5>" . htmlentities($comment['uid'])  . "</h5>";
                echo "<p style = 'overflow-wrap: anywhere;'>" . htmlentities($comment['content']) . "</p>";
                echo "</section>";

            }
            echo "</div></div><div class='row'> <br> </div>";
        }


        ?>
    </div>
    <!-- Venstre kolonne/navbar, lige nu har vi bare knapper til at logge ud/ind og lave ny bruger hvis man ikke er logget ind. -->
    <div class='col-lg-2'>
        <?php
        if($loggedtrue) {
            echo "<p><a href='logout.php'>Log ud</p></a>";
        }else{
            echo "<p><a href='login.php'>Log ind</p></a>";
            echo "<p><a href='signup.php'>Opret konto</a></p>";
        }


        ?>
    </div>
</div>
</body>
</html>