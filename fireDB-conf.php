<?php
use Kreait\Firebase\Factory;

$factory = (new Factory)
    ->withServiceAccount("secret/penta-secret.json")
    ->withDatabaseUri("https://penta-20d7f-default-rtdb.firebaseio.com/");
$database = $factory->createDatabase();
$reference = $database->getReference("twitter/list/top-users");
