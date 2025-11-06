<?php
declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class WithdrawRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|string|uuid|unique:account,id',
            'method' => 'required|in:PIX',
            'pix' => 'required_if:method,PIX|array',
            'pix.type' => 'required_if:method,PIX|in:email',
            'pix.key' => 'required_if:method,PIX|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'schedule' => 'nullable|date|after_or_equal:today',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'O campo id é obrigatório.',
            'id.string' => 'O campo id deve ser uma string.',
            'id.uuid' => 'O campo id deve ser um UUID válido.',
            'id.unique' => 'O id fornecido já existe na tabela account.',

            'method.in' => 'Os métodos aceitáveis são: PIX ou TED.',
            'method.required' => 'O campo method é obrigatório.',

            'pix.required_if' => 'Dados PIX são obrigatórios quando method=PIX.',
            'pix.array' => 'O campo pix deve ser um objeto JSON (array associativo).',
            'pix.type.required_if' => 'O campo pix.type é obrigatório quando method=PIX.',
            'pix.type.in' => 'Tipo PIX inválido. Use um de: email.',
            'pix.key.required_if' => 'A chave pix.key é obrigatória quando method=PIX.',
            'pix.key.max' => 'A chave PIX não pode ultrapassar 255 caracteres.',

            'amount.required' => 'Informe o valor (amount).',
            'amount.numeric' => 'O valor deve ser numérico.',
            'amount.min' => 'O valor mínimo para saque é 0.01.',
            
            'schedule.after_or_equal' => 'A data agendada deve ser hoje ou uma data futura.',
        ];
    }
}
