<?php
    session_start();
    session_destroy();
    header("Location: /~seno/miniprojekt/main.php");
?>