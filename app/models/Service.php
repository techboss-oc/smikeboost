<?php
require_once APP_PATH . '/models/BaseModel.php';

class Service extends BaseModel
{
    protected $table = 'services';

    public function create($data)
    {
        $sql = "INSERT INTO services (provider_id, api_service_id, platform, category, name, description, rate_per_1000, min_qty, max_qty, status)
                VALUES (:provider_id, :api_service_id, :platform, :category, :name, :description, :rate_per_1000, :min_qty, :max_qty, :status)";
        db_execute($sql, $data);
        return db()->lastInsertId();
    }

    public function getActive()
    {
        $sql = "SELECT * FROM {$this->table} WHERE status = 'enabled' AND deleted_at IS NULL ORDER BY platform ASC, category ASC, rate_per_1000 ASC";
        return db_fetch_all($sql);
    }

    public function getCategories()
    {
        $sql = "SELECT DISTINCT category FROM {$this->table} WHERE status = 'enabled' AND deleted_at IS NULL ORDER BY category ASC";
        $rows = db_fetch_all($sql);
        return array_filter(array_column($rows, 'category'));
    }

    public function getPlatforms()
    {
        $sql = "SELECT DISTINCT platform FROM {$this->table} WHERE status = 'enabled' AND deleted_at IS NULL ORDER BY platform ASC";
        $rows = db_fetch_all($sql);
        return array_filter(array_column($rows, 'platform'));
    }

    public function countActive()
    {
        $row = db_fetch("SELECT COUNT(*) AS total FROM {$this->table} WHERE status = 'enabled' AND deleted_at IS NULL");
        return (int) ($row['total'] ?? 0);
    }

    public function getByCategory($category)
    {
        $sql = "SELECT * FROM {$this->table} WHERE status = 'enabled' AND deleted_at IS NULL AND category = :category ORDER BY rate_per_1000 ASC";
        return db_fetch_all($sql, ['category' => $category]);
    }

    public function getByPlatform($platform)
    {
        $sql = "SELECT * FROM {$this->table} WHERE status = 'enabled' AND deleted_at IS NULL AND platform = :platform ORDER BY category ASC, rate_per_1000 ASC";
        return db_fetch_all($sql, ['platform' => $platform]);
    }

    public function search($term)
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE status = 'enabled' AND deleted_at IS NULL
                AND (name LIKE :term OR category LIKE :term OR platform LIKE :term)
                ORDER BY platform ASC, category ASC";
        return db_fetch_all($sql, ['term' => '%' . $term . '%']);
    }
}
