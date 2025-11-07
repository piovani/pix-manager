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
        $account = $this->getAccountById($data['id']);
        if ($account === null) {
            return ['error' => 'Conta não encontrada.'];
        }

        if (!$account->hasValue((float)$data['amount'])) {
            return ['error'=> 'Saldo insuficiente para saque.'];
        }

        if (isset($data['schedule'])) {
            try {
                $scheduleDate = new \DateTimeImmutable($data['schedule']);
            } catch (\Exception $e) {
                return ['error' => 'Data de agendamento inválida.'];
            }  
        }


        DB::beginTransaction();

        $withdraw = $this->createAccountWithdraw(
            $account,
            $data['amount'],
            $data['method'],
            $data['schedule'] ?? null
        );
    
        if ($data['method'] === 'PIX') {
            $withdrawPix = $this->createAccountWithdrawPix(
                $withdraw,
                $data['pix']['key']
            );
        }

        if (is_null($scheduleDate)) {
            $this->accountWithdraw($withdraw, $account, (float)$data['amount']);
            $this->notification($withdraw);
        }

        DB::commit();

        return [];
    }

    public function WithdrawFromAccount(Account $account, Withdraw $withdraw): void
    {
        $account->balance -= $withdraw->amount;
        $withdraw->done = true;
        if ($account->save()) {
            $this->notification($withdraw);
        } else {
            $this->withdraw->error = true;
            $this->withdraw->error_reason = 'Erro ao processar saque.';
        }
        $withdraw->save();
    }

    public function getAccountById(string $id): ?Account
    {
        return Account::query()->find($id);
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

    private function createAccountWithdraw(Account $account, string $amount, string $method, ?string $scheduled): ?AccountWithdraw
    {
        $withdraw = AccountWithdraw::create([
            'id' => Uuid::uuid4()->toString(),
            'account_id' => $account->id,
            'method' => $method,
            'amount' => (float)$amount,
            'scheduled' => is_null($scheduled) ? false : true,
            'done' => is_null($scheduled) ? false : true,
        ]);
        $withdraw->save();
        return $withdraw;
    }

    private function createAccountWithdrawPix(AccountWithdraw $withdraw, string $key): ?AccountWithdrawPix
    {
        $withdrawPix = AccountWithdrawPix::create([
            'id' => Uuid::uuid4()->toString(),
            'accounts_withdraw_id' => $withdraw->id,
            'key' => $key,
        ]);
        $withdrawPix->save();
        return $withdrawPix;
    }
}