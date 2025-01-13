<?php
use App\Jobs\ProcessInvoiceJob;
use App\Models\Invoice;
use App\Services\EmailService;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

it('deve processar boletos', function () {
    Storage::fake('local');

    // Mock do InvoiceService
    $invoiceService = Mockery::mock(InvoiceService::class);
    $invoiceService->shouldReceive('check')->andReturnUsing(function ($data) {
        return new Invoice($data + ['processed' => false]);
    });
    $invoiceService->shouldReceive('generate')->andReturn('http://fake.invoice/email/debt_id');

    // Mock do EmailService
    $emailService = Mockery::mock(EmailService::class);
    $emailService->shouldReceive('send')->once();

    // Simula o conteúdo de um arquivo CSV
    $fileContent = "name,governmentId,email,debtAmount,debtDueDate,debtId\nJohn Doe,11111111111,johndoe@kanastra.com.br,1000000.00,2022-10-12,1adb6ccf-ff16-467f-bea7-5f05d494280f";
    $filePath = 'uploads/mock_invoice.csv';
    Storage::put($filePath, $fileContent);

    // Verifica se o log será chamado pelo job
    Log::shouldReceive('info')
        ->with('Boletos processados com sucesso!')
        ->once();

    // Executa o job
    $job = new ProcessInvoiceJob(Storage::path($filePath));
    $job->handle($invoiceService, $emailService);

    // Verifica se o arquivo foi deletado
    Storage::assertMissing($filePath);
});
