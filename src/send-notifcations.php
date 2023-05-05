<?php

require_once 'db.php';
require_once 'helper.php';

function getSubscribersForNotify(int $intervalBefore, int $limit = 1000): false|array
{
    global $db;
    $timeMin = time() + $intervalBefore;
    $timeMax = time() + $intervalBefore + DAY;
    $notifyInterval = $intervalBefore + DAY;
    $query = <<<SQL
SELECT *
FROM subscribers
WHERE
   validts < $timeMax
  AND validts >= $timeMin
  AND valid
AND $notifyInterval < validts - last_notify_at 
LIMIT $limit
SQL;
    return $db->query($query)->fetchAll(PDO::FETCH_ASSOC);
}

while (true) {
    try {
        $subscribersEarly = getSubscribersForNotify(DAYS_3);
        $subscribers = getSubscribersForNotify(DAY);

        $updateData = [];
        foreach (array_merge($subscribers, $subscribersEarly) as $subscriber) {
            send_email('test@example.com', $subscriber['email'], getEmailText($subscriber['username']));
            $subscriber['last_notify_at'] = time();
            $updateData[] = $subscriber;
        }
        updateBatch($updateData, ['last_notify_at']);
    } catch (PDOException $exception) {}
    sleep(1);
}


