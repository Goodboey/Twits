<?php
    // Vi smadrer sessionen og refererer tilbage til hovedsiden
    session_start();
    session_destroy();
    header("Location: /~seno/miniprojekt/main.php");
?>