<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

class Account extends Model
{
    protected ?string $table = 'Account';
    
    public bool $incrementing = false;

    protected string $keyType = 'string';

    public const CREATED_AT = null;

    public const UPDATED_AT = null;

    protected array $fillable = [
        'id',
        'name',
        'balance',
    ];

    protected array $casts = [
        'id' => 'string', 
        'name' => 'string', 
        'balance' => 'float'
    ];
}
