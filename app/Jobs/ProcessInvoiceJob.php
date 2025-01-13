<?php

namespace App\Jobs;

use App\Services\EmailService;
use App\Services\InvoiceService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function handle(InvoiceService $invoiceService, EmailService $emailService)
    {
        try {
            // Abre o arquivo CSV
            $file = fopen($this->filePath, 'r');
            if ($file === false) {
                throw new Exception('Não foi possível abrir o arquivo.');
            }

            // Captura o cabecalho e converte em array
            $header = fgetcsv($file);
            if ($header === false) {
                throw new Exception('O arquivo CSV está vazio ou não tem um cabeçalho válido.');
            }

            // Processa o arquivo linha por linha e converte em array
            while (($line = fgetcsv($file)) !== false) {
                // Combina a linha com o cabecalho para criar um array associativo
                $invoiceData = array_combine($header, $line);
                // Verifica se a linha nao esta vazia
                if (!empty($invoiceData)) {
                    // Processa o boleto
                    // Captura os dados do boleto no banco de dados
                    $invoice = $invoiceService->check($invoiceData);
                    if ($invoice) {
                        // Gera o boleto
                        $invoiceUrl = $invoiceService->generate($invoiceData);
                        // Envia o boleto por email
                        $emailService->send($invoiceData['email'], $invoiceUrl);
                        // Atualiza o status do boleto
                        $invoice->update(['processed' => true]);
                        Log::info('Boletos processados com sucesso!');
                    }
                }
            }

            fclose($file);
        } catch (Exception $e) {
            throw $e;
        } finally {
            unlink($this->filePath);
        }
    }
}
