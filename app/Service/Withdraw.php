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

        $scheduleDate  = null;

        if (isset($data['schedule'])) {
            try {
                $scheduleDate = new \DateTimeImmutable($data['schedule']);
            } catch (\Exception $e) {
                return ['error' => 'Data de agendamento inválida.'];
            }  
        }


        DB::beginTransaction();

        $idWithdra = Uuid::uuid4()->toString();
        $withdraw = AccountWithdraw::create([
            'id' => $idWithdra,
            'account_id' => $data['id'],
            'method' => $data['method'],
            'amount' => (float)$data['amount'],
            'scheduled' => is_null($scheduleDate) ? false : true,
            'done' => is_null($scheduleDate) ? false : true,
        ]);
        $withdraw->save();

        if ($data['method'] === 'PIX') {
            AccountWithdrawPix::create([
                'id' => Uuid::uuid4()->toString(),
                'accounts_withdraw_id' => $idWithdra,
                'key' => $data['pix']['key'],
            ])->save();
        }

        if (is_null($scheduleDate)) {
            $this->accountWithdraw($withdraw, $account, (float)$data['amount']);
            $this->notification($withdraw);
        }

        DB::commit();

        return [];
    }

    public function accountWithdraw(AccountWithdraw $withdraw, Account $account, float $amount): void
    {
        $account->balance -= $amount;
        $account->save();
        $this->notification($withdraw);
    }

    private function notification(AccountWithdraw $withdraw): void
    {
        $account = Account::query()->find($withdraw->account_id);

        if (!$account || empty($account->email)) {
            return;
        }

        $to = $account->email;
        $subject = 'Notificação de Saque';
        $message = sprintf(
            "Olá %s,\n\nSeu saque de R$ %s foi registrado.\nID: %s\nMétodo: %s\nAgendado: %s\n\nObrigado.",
            $account->name ?? 'Cliente',
            number_format((float)$withdraw->amount, 2, ',', '.'),
            $withdraw->id,
            $withdraw->method,
            $withdraw->scheduled ? 'Sim' : 'Não'
        );

        $headers = "From: no-reply@pix-manager.local\r\n".
               "Content-Type: text/plain; charset=UTF-8\r\n";

        @mail($to, $subject, $message, $headers);
    }

}