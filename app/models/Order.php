<?php
require_once APP_PATH . '/models/BaseModel.php';

class Order extends BaseModel
{
    protected $table = 'orders';

    public function create($data)
    {
        $sql = "INSERT INTO orders (user_id, service_id, link, quantity, amount, status, provider_order_id) VALUES (:user_id, :service_id, :link, :quantity, :amount, :status, :provider_order_id)";
        db_execute($sql, $data);
        return db()->lastInsertId();
    }

    public function byUser($userId, $limit = 50, $offset = 0)
    {
        $sql = "SELECT o.*, s.name AS service_name FROM orders o LEFT JOIN services s ON s.id = o.service_id WHERE o.user_id = :uid ORDER BY o.id DESC LIMIT :offset, :limit";
        $stmt = db()->prepare($sql);
        $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
