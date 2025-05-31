<?php
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', '');
    define('DB_DATABASE', 'its_db');

    $db = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
    
    if(!$db){
        die("Connection failed: " . mysqli_connect_error());
        echo "Connection failed: " . mysqli_connect_error();
    }
?>