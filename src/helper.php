<?php

const DAY = 60 * 60 * 24;
const DAYS_3 = DAY * 3;

function check_email(string $email): bool
{
    printLn('Checking email: ' . $email);
    sleep(rand(1, 60));
    return (bool)rand(0, 1);
}

function send_email($from, $to, $text): void
{
    printLn('Sending message to: ' . $to);
    sleep(rand(1, 10));
}

function getEmailText(string $userName): string
{
    return $userName . ', your subscription is expiring soon';
}

function printLn(string $message): void
{
    $lastMessageAt = date('Y-m-d H:i:s');
    echo "[$lastMessageAt] $message \r\n";
}

function getInsertQuery(Generator $generator, string $table = 'subscribers', int $batchSize = 10000): array
{
    $query = '';
    $plNo = 0;
    $placeHolders = [];
    $rows = [];
    foreach ($generator as $i => $rowData) {
        if ($i >= $batchSize) {
            break;
        }
        if (!$query) {
            $query = "INSERT INTO $table (" . implode(',', array_keys($rowData)) . ') VALUES ';
        }

        $queryRow = [];
        foreach ($rowData as $value) {
            $placeholder = ':p' . $plNo;
            $queryRow[] = $placeholder;
            $placeHolders[$placeholder] = $value;
            $plNo++;
        }
        $rows[] = '(' . implode(',', $queryRow) . ')';
    }
    $query .= implode(',', $rows);
    return [$query, $placeHolders];
}


function updateBatch(array $data, array $columns, string $table = 'subscribers'): void
{
    if (empty($data)) {
        return;
    }
    printLn('Updating ' . count($data) . " $table...");
    $tempTable = 'tmp_' . uniqid();
    global $db;
    $db->exec("CREATE TEMPORARY TABLE $tempTable ( LIKE $table INCLUDING DEFAULTS )");
    $generator = function (array $data) {
        foreach ($data as $row) {
            foreach ($row as &$column) {
                if (gettype($column) == 'boolean') $column = (int)$column;
            }
            yield $row;
        }
    };
    [$query, $placeHolders] = getInsertQuery($generator($data), $tempTable, count($data));
    if (!$db->prepare($query)->execute($placeHolders)) {
        die(print_r($db->errorInfo(), true));
    }

    $set = array_map(fn($column) => $column . ' = u.' . $column, $columns);
    $set = implode(',', $set);
    $query = <<<SQL
UPDATE $table SET $set
FROM $tempTable u
WHERE $table.id = u.id
SQL;
    if (!$db->query($query)) {
        die(print_r($db->errorInfo(), true));
    }
    if (!$db->query("DROP TABLE $tempTable")) {
        die(print_r($db->errorInfo(), true));
    }
    printLn('Update complete');
}
