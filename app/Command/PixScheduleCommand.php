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
        $service = new Withdraw();
        $this->line('Cheking PIXs!', 'info');

        AccountWithdraw::where('method', 'PIX')
            ->where('scheduled', true)
            ->where('done', false)
            ->get()
            ->each(function (AccountWithdraw $withdraw, Withdraw $service) {
                DB::beginTransaction();
                $account = $service->getAccountById($withdraw->account_id);
                $service->WithdrawFromAccount($account, $withdraw);
                DB::commit();

                $this->line("Processed PIX withdraw {$withdraw->id} for account {$account->id}", 'info');
            });
    }
}
