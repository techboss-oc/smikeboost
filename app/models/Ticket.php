<?php
require_once APP_PATH . '/models/BaseModel.php';

class Ticket extends BaseModel
{
    protected $table = 'tickets';

    public function create($data)
    {
        $sql = "INSERT INTO tickets (user_id, subject, category, priority, message, status) VALUES (:user_id, :subject, :category, :priority, :message, :status)";
        db_execute($sql, $data);
        return db()->lastInsertId();
    }
}
