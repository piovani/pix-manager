<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 */
class AccountWithdrawPix extends Model
{
    protected ?string $table = 'AccountWithdrawPix';

    public bool $incrementing = false;

    protected string $keyType = 'string';

    public const CREATED_AT = null;

    public const UPDATED_AT = null;

    protected array $fillable = [
        'id',
        'accounts_withdraw_id',
        'key',
    ];

    protected array $casts = [
        'id' => 'string',
        'accounts_withdraw_id' => 'string',
        'key' => 'string',
    ];
}
