<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Account;

class Withdraw
{
    public function withdraw(array $data)
    {
    /*
        * A operação do saque deve ser registrada no banco de dados, usando as tabelas account_withdraw e account_withdraw_pix.
        * O saque sem agendamento deve realizar o saque de imediato.
        * O saque com agendamento deve ser processado somente via cron (mais detalhes abaixo).
        * O saque deve deduzir o saldo da conta na tabela account .
        * Atualmente só existe a opção de saque via PIX, podendo ser somente para chaves do tipo email. A implementação deve possibilitar uma fácil expansão de outras formas de saque no futuro.
        * Não é permitido sacar um valor maior do que o disponível no saldo da conta digital.
        * O saldo da conta não pode ficar negativo.
        * Para saque agendado, não é permitido agendar para um momento no passado.
    */



        return $data;
    }

}