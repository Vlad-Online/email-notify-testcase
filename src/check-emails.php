<?php

require_once 'db.php';
require_once 'helper.php';

function getNotChecked(int $limit = 1000): false|array
{
    global $db;
    return $db->query(<<<SQL
SELECT *
FROM subscribers
WHERE confirmed AND not checked LIMIT $limit;
SQL
    )->fetchAll(PDO::FETCH_ASSOC);
}

while (true) {
    $updateData = [];
    printLn('Checking subscribers emails...');
    $valid = $notValid = 0;
    try {
        foreach (getNotChecked() as $subscriber) {
            $subscriber['checked'] = true;
            if ($subscriber['valid'] = check_email($subscriber['email'])) {
                $valid++;
            } else {
                $notValid++;
            }
            $updateData[] = $subscriber;
        }
        printLn('Valid emails: ' . $valid);
        printLn('Not valid emails: ' . $notValid);
        updateBatch($updateData, ['checked', 'valid']);
    } catch (PDOException $exception) {}
    sleep(1);
}


