<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 */
class AccountWithdraw extends Model
{
    protected ?string $table = 'AccountWithdraw';

    public bool $incrementing = false;

    protected string $keyType = 'string';

    public const CREATED_AT = null;

    public const UPDATED_AT = null;

    protected array $fillable = [
        'id',
        'account_id',
        'method',
        'amount',
        'scheduled',
        'done',
        'error',
        'error_reason',
    ];

    protected array $casts = [
        'id' => 'string',
        'account_id' => 'string',
        'method' => 'string',
        'amount' => 'float',
        'scheduled' => 'boolean',
        'done' => 'boolean',
        'error' => 'boolean',
        'error_reason' => 'string|null',
    ];
}
