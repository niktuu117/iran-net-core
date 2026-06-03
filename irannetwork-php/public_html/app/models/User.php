<?php
declare(strict_types=1);

class User extends BaseModel
{
    protected string $table = 'users';
    protected array $fillable = ['name','email','password_hash','role','status'];

    public function findByEmail(string $email): ?array
    {
        return Database::fetch('SELECT * FROM users WHERE email = ? LIMIT 1', [strtolower(trim($email))]);
    }

    public function hasAny(): bool
    {
        return $this->count() > 0;
    }
}
