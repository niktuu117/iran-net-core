<?php
declare(strict_types=1);

class Category extends BaseModel
{
    protected string $table = 'categories';
    protected array $fillable = ['name','slug','description'];
}
