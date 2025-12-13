<?php
abstract class BaseModel
{
    protected $table;
    protected $primaryKey = 'id';

    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        return db_fetch($sql, ['id' => $id]);
    }

    public function all($limit = 100, $offset = 0)
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY {$this->primaryKey} DESC LIMIT :offset, :limit";
        $stmt = db()->prepare($sql);
        $paramInt = class_exists('PDO') ? PDO::PARAM_INT : 1;
        if (method_exists($stmt, 'bindValue')) {
            $stmt->bindValue(':offset', (int)$offset, $paramInt);
            $stmt->bindValue(':limit', (int)$limit, $paramInt);
        }
        if (method_exists($stmt, 'execute')) {
            $stmt->execute();
            if (method_exists($stmt, 'fetchAll')) {
                return $stmt->fetchAll();
            }
        }
        return [];
    }

    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        return db_execute($sql, ['id' => $id]);
    }
}
