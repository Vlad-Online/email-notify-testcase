<?php

global $db;
while (true) {
    try {
        $db = new PDO('pgsql:dbname=test;host=db', 'test', 'password', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        break;
    } catch (PDOException){}
    sleep(1);
}

