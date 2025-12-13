<?php
require_once APP_PATH . '/models/BaseModel.php';

class Provider extends BaseModel
{
    protected $table = 'providers';

    public function create($data)
    {
        $sql = "INSERT INTO providers (name, api_key, api_url, auto_sync) VALUES (:name, :api_key, :api_url, :auto_sync)";
        db_execute($sql, $data);
        return db()->lastInsertId();
    }
}
