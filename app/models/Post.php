<?php
require_once 'BaseModel.php';

class Post extends BaseModel
{
    protected $table = 'posts';

    public function findBySlug($slug)
    {
        $sql = "SELECT * FROM {$this->table} WHERE slug = :slug AND status = 'published' LIMIT 1";
        return db_fetch($sql, ['slug' => $slug]);
    }

    public function getPublished($limit = 10, $offset = 0)
    {
        $sql = "SELECT * FROM {$this->table} WHERE status = 'published' ORDER BY created_at DESC LIMIT :offset, :limit";
        $stmt = db()->prepare($sql);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
