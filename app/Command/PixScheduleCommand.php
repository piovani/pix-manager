<?php

declare(strict_types=1);

namespace App\Command;

use App\Model\Account;
use App\Model\AccountWithdraw;
use App\Service\Withdraw;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerInterface;
use Hyperf\DbConnection\Db;

#[Command]
class PixScheduleCommand extends HyperfCommand
{
    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('pix-schedule');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Check there pix schedueled and process them');
    }

    public function handle()
    {
        $this->line('Cheking PIXs!', 'info');

        AccountWithdraw::where('method', 'PIX')
            ->where('scheduled', true)
            ->where('done', false)
            ->get()
            ->each(function (AccountWithdraw $withdraw) {
                $service = new Withdraw();

                DB::beginTransaction();

                $account = Account::query()->findOrFail($withdraw->account_id);

                if (!$account->hasValue((float)$withdraw->amount)) {
                    $withdraw->done = true;
                    $withdraw->error = true;
                    $withdraw->error_reason = 'Saldo insuficiente para saque agendado.';
                    $withdraw->save();

                    $this->line("Insufficient funds for scheduled PIX withdraw {$withdraw->id} for account {$account->id}", 'error');

                    DB::commit();
                    return;
                }

                $service->accountWithdraw($withdraw, $account, (float)$withdraw->amount);
                $withdraw->done = true;
                $withdraw->save();

                DB::commit();

                $this->line("Processed PIX withdraw {$withdraw->id} for account {$account->id}", 'info');
            });
    }
}
