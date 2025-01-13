<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class EmailService
{
    public function send(string $email, string $invoiceUrl)
    {
        // Simula o envio do email com o boleto para o cliente
        Log::info("Enviando e-mail para {$email} com boleto: {$invoiceUrl}");
    }
}