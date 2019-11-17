<?php

try{
    $bdd = new PDO(
        'mysql:host=localhost;dbname=miw_world;charset=utf8;port=3307',
        'root',
        '',
        array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING)
    );
}
catch(Exception $e){
    die('Erreur : '.$e->getMessage());
}
