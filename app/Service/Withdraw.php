<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Account;
use App\Model\AccountWithdraw;
use App\Model\AccountWithdrawPix;
use Ramsey\Uuid\Uuid;
use Hyperf\DbConnection\Db;

class Withdraw
{
    public function withdraw(array $data)
    {
        if (!isset($data['id'])) {
            return ['error' => 'Account id ausente.'];
        }

        $account = Account::query()->findOrFail($data['id']);

        if (!$account->hasValue((float)$data['amount'])) {
            return ['error'=> 'Saldo insuficiente para saque.'];
        }

        DB::beginTransaction();

        $idWithdra = Uuid::uuid4()->toString();

        AccountWithdraw::create([
            'id' => $idWithdra,
            'account_id' => $data['id'],
            'method' => $data['method'],
            'amount' => (float)$data['amount'],
            'scheduled' => isset($data['schedule']),
        ])->save();

        if ($data['method'] === 'PIX') {
            AccountWithdrawPix::create([
                'id' => Uuid::uuid4()->toString(),
                'accounts_withdraw_id' => $idWithdra,
                'key' => $data['pix']['key'],
            ])->save();
        }

        if (!isset($data['schedule'])) {
            $this->accountWithdraw($account, (float)$data['amount']);
        }

        DB::commit();

        return [];
    }

    public function accountWithdraw(Account $account, float $amount): void
    {
        $account->balance -= $amount;
        $account->save();
    }

}