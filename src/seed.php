<?php

require_once 'db.php';
require_once 'helper.php';
include 'migration.php';

function getSeedData(): Generator
{
    while (true) {
        yield [
            'username'  => uniqid(),
            'email'     => uniqid() . '@' . uniqid() . '.com',
            'validts'   => time() + rand(0, DAY * 4),
            'confirmed' => rand(0, 1),
            'checked'   => rand(0, 1),
            'valid'     => rand(0, 1)
        ];
    }
}


global $db;
for ($i = 0; $i < 200; $i++) {
    [$query, $placeHolders] = getInsertQuery(getSeedData());
    if (!$db->prepare($query)->execute($placeHolders)) {
        die(print_r($db->errorInfo(), true));
    }
}
