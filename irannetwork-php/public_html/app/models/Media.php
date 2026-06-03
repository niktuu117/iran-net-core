<?php
declare(strict_types=1);

class Media extends BaseModel
{
    protected string $table = 'media';
    protected array $fillable = ['title','alt','caption','filename','original_name','mime_type','size','url'];
}
