<?php
require_once APP_PATH . '/models/BaseModel.php';

class User extends BaseModel
{
    protected $table = 'users';

    public function create($data)
    {
        $fields = ['name', 'username', 'email', 'password_hash', 'role', 'api_token'];
        $placeholders = [':name', ':username', ':email', ':password_hash', ':role', ':api_token'];

        // Prepare parameters to match the SQL query exactly
        $params = [
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password_hash' => $data['password_hash'],
            'role' => $data['role'],
            'api_token' => $data['api_token']
        ];

        if (isset($data['referrer_id'])) {
            $fields[] = 'referrer_id';
            $placeholders[] = ':referrer_id';
            $params['referrer_id'] = $data['referrer_id'];
        }

        $sql = "INSERT INTO users (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
        db_execute($sql, $params);
        return db()->lastInsertId();
    }

    public function findByEmail($email)
    {
        return db_fetch("SELECT * FROM users WHERE email = :email LIMIT 1", ['email' => $email]);
    }

    public function findByUsername($username)
    {
        return db_fetch("SELECT * FROM users WHERE username = :username LIMIT 1", ['username' => $username]);
    }

    public function findByApiToken($token)
    {
        return db_fetch("SELECT * FROM users WHERE api_token = :token LIMIT 1", ['token' => $token]);
    }

    public function findAdminByIdentity($identity)
    {
        $sql = "SELECT * FROM {$this->table} WHERE (username = :uname OR email = :email) AND role IN ('admin','superadmin') LIMIT 1";
        return db_fetch($sql, ['uname' => $identity, 'email' => $identity]);
    }

    public function updateWallet($id, $amount)
    {
        $sql = "UPDATE users SET wallet_balance = :amount WHERE id = :id";
        return db_execute($sql, ['amount' => $amount, 'id' => $id]);
    }
}
