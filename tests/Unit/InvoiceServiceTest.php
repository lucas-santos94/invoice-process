<?php
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Support\Str;

it('deve criar um registro de boleto novo', function () {
    $service = new InvoiceService();

    // Dados do boleto
    $data = [
        'name' => 'John Doe',
        'governmentId' => '12345678901',
        'email' => 'john@example.com',
        'debtAmount' => 1500.50,
        'debtDueDate' => '2025-01-15',
        'debtId' => Str::uuid(),
    ];

    // Executa o método check
    $invoice = $service->check($data);

    // Verifica se o registro foi criado no banco
    expect($invoice)->toBeInstanceOf(Invoice::class);
    expect($invoice->name)->toBe('John Doe');
});

it('deve atualizar um registro de boleto existente', function () {
    $service = new InvoiceService();

    $uuid = Str::uuid();
    // Cria um boleto existente
    $existingInvoice = Invoice::create([
        'name' => 'Jane Doe',
        'government_id' => '98765432100',
        'email' => 'jane@example.com',
        'debt_amount' => 1000,
        'debt_due_date' => '2025-01-10',
        'debt_id' => $uuid,
        'processed' => false,
    ]);

    // Dados atualizados do boleto
    $data = [
        'name' => 'John Doe',
        'governmentId' => '98765432100',
        'email' => 'john@example.com',
        'debtAmount' => 1500.50,
        'debtDueDate' => '2025-01-15',
        'debtId' => $uuid,
    ];

    // Executa o método check
    $invoice = $service->check($data);

    // Verifica se o registro foi atualizado
    expect($invoice->name)->toBe('John Doe');
    expect($invoice->email)->toBe('john@example.com');
    expect($invoice->debt_amount)->toBe(1500.50);
});
