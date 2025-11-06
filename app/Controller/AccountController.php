<?php

declare(strict_types=1);

namespace App\Controller;

use App\Request\WithdrawRequest;
use App\Service\Withdraw;

class AccountController extends AbstractController
{
    private $service;

    public function __construct()
    {
        $this->service = new Withdraw();
    }

    public function withdraw(WithdrawRequest $request, string $id)
    {
        $data = $request->validated();
        $data['id'] = $id;

        if ($res = $this->service->withdraw($data)) {
            return $this->response->json([
                'message' => 'Withdraw successful',
                'res' => $res
            ])->withStatus(200);
        } else {
            return $this->response->json([
                'message' => 'Withdraw failed'
            ])->withStatus(500);
        }
    }
}
