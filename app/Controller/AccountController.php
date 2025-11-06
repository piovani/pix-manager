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

        $res = $this->service->withdraw($data);

        if (isset($res['error'])) {
            return $this->response->json(['message' => $res['error']])->withStatus(403);
        }
        return $this->response->json(['message' => 'Withdraw successful'])->withStatus(200);
    }
}
