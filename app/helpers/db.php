<?php
require_once APP_PATH . '/config/database.php';

function db() {
    return Database::connection();
}

function db_query($sql, $params = []) {
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

function db_fetch($sql, $params = []) {
    return db_query($sql, $params)->fetch();
}

function db_fetch_all($sql, $params = []) {
    return db_query($sql, $params)->fetchAll();
}

function db_execute($sql, $params = []) {
    $stmt = db()->prepare($sql);
    return $stmt->execute($params);
}
