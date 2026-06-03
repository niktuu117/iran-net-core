<?php
declare(strict_types=1);

class Faq extends BaseModel
{
    protected string $table = 'faqs';
    protected array $fillable = ['question','answer','sort_order','is_active','post_id','service_id','page_id'];
}
