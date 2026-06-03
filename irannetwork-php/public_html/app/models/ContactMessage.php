<?php
declare(strict_types=1);

class ContactMessage extends BaseModel
{
    protected string $table = 'contact_messages';
    protected array $fillable = ['name','phone','email','service','message','status'];

    public function unreadCount(): int
    {
        return $this->count("status = 'new'");
    }
}
