<?php

require_once 'db.php';
global $db;

$db->query(<<<'SQL'
DROP TABLE IF EXISTS subscribers;
SQL
);

$db->query(<<<'SQL'
CREATE TABLE IF NOT EXISTS subscribers
(
    id        serial constraint id primary key,
    username  varchar            not null,
    email     varchar            not null,
    validts   integer          not null,
    confirmed bool default false not null,
    checked   bool default false not null,
    valid     bool default false not null,
    last_notify_at   integer not null DEFAULT 0   
);
SQL
);

$db->query(<<<'SQL'
create index subscribers_validts_last_notify_at_diff_valid_index
    on subscribers (validts, (validts - last_notify_at), valid);
SQL
);

$db->query(<<<'SQL'
create index subscribers__confirmed_checked_index
    on subscribers (confirmed, checked);
SQL
);



