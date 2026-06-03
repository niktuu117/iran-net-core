<?php
declare(strict_types=1);

class Tag extends BaseModel
{
    protected string $table = 'tags';
    protected array $fillable = ['name','slug'];
}
