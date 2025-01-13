<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\Invoice;

class InvoiceService
{
    public function generate(array $invoiceData)
    {
        // Simula a criacao do boleto com um log
        Log::info("Boleto {$invoiceData['debtId']} gerado para: {$invoiceData['name']}");
        
        // Retorna uma URL fake de boleto
        return "http://fake.invoice/{$invoiceData['email']}/{$invoiceData['debtId']}";
    }

    public function check(array $invoiceData)
    {
        // Verifica se o boleto ja foi registrado anteriormente
        $invoice = Invoice::where('debt_id', $invoiceData['debtId'])->first();

        if ($invoice) {
            // Caso ja tenha sido registrado e processado, ignora o boleto
            if ($invoice->processed) {
                Log::info("Boleto {$invoiceData['debtId']} já processado");
                return null;
            }
            // Se o boleto tiver registrado mas não processado, atualizo o registro dele
            $invoice->update([
                'name' => $invoiceData['name'],
                'government_id' => $invoiceData['governmentId'],
                'email' => $invoiceData['email'],
                'debt_amount' => $invoiceData['debtAmount'],
                'debt_due_date' => $invoiceData['debtDueDate'],
            ]);
        } else {
            // Cria o registro do boleto caso ainda não exista
            $invoice = Invoice::create([
                'name' => $invoiceData['name'],
                'government_id' => $invoiceData['governmentId'],
                'email' => $invoiceData['email'],
                'debt_amount' => $invoiceData['debtAmount'],
                'debt_due_date' => $invoiceData['debtDueDate'],
                'debt_id' => $invoiceData['debtId'],
            ]);
        }

        return $invoice;
    }
}
