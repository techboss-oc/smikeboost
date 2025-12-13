<?php
require_once APP_PATH . '/models/BaseModel.php';

class Transaction extends BaseModel
{
    protected $table = 'transactions';

    public function create($data)
    {
        $fields = ['user_id', 'amount', 'type', 'gateway', 'status', 'reference'];
        $params = [
            'user_id' => $data['user_id'],
            'amount' => $data['amount'],
            'type' => $data['type'],
            'gateway' => $data['gateway'],
            'status' => $data['status'],
            'reference' => $data['reference']
        ];
        
        if (isset($data['proof_image']) && $data['proof_image']) {
            $fields[] = 'proof_image';
            $params['proof_image'] = $data['proof_image'];
        }

        $placeholders = array_map(function($f) { return ':' . $f; }, $fields);
        $sql = "INSERT INTO transactions (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        db_execute($sql, $params);
        return db()->lastInsertId();
    }

    public function getByUserId($userId, $limit = 20)
    {
        return db_fetch_all(
            "SELECT * FROM transactions WHERE user_id = :uid ORDER BY created_at DESC LIMIT :limit",
            ['uid' => $userId, 'limit' => $limit]
        );
    }

    public function getPending()
    {
        return db_fetch_all(
            "SELECT t.*, u.username, u.email FROM transactions t 
             JOIN users u ON t.user_id = u.id 
             WHERE t.status = 'pending' 
             ORDER BY t.created_at DESC"
        );
    }

    public function approve($id)
    {
        $tx = db_fetch("SELECT * FROM transactions WHERE id = :id", ['id' => $id]);
        if (!$tx || $tx['status'] !== 'pending') {
            return false;
        }

        // Credit user wallet
        db_execute(
            "UPDATE users SET wallet_balance = wallet_balance + :amount WHERE id = :uid",
            ['amount' => $tx['amount'], 'uid' => $tx['user_id']]
        );

        // Update transaction status
        db_execute("UPDATE transactions SET status = 'completed' WHERE id = :id", ['id' => $id]);
        
        return true;
    }

    public function reject($id)
    {
        $tx = db_fetch("SELECT status FROM transactions WHERE id = :id", ['id' => $id]);
        if (!$tx || $tx['status'] !== 'pending') {
            return false;
        }

        return db_execute("UPDATE transactions SET status = 'failed' WHERE id = :id", ['id' => $id]);
    }
}
