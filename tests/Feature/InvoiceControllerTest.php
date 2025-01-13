<?php
use App\Jobs\ProcessInvoiceJob;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

it('Recebe o arquivo CSV e despach um job para processar e gerar os boletos', function () {
    // Mock do storage
    Storage::fake('local');
    // Mock da fila
    Queue::fake();

    // Mock CSV
    $file = UploadedFile::fake()->createWithContent(
        'mock_invoice.csv',
        'name,governmentId,email,debtAmount,debtDueDate,debtId\nJohn Doe,11111111111,johndoe@kanastra.com.br,1000000.00,2022-10-12,1adb6ccf-ff16-467f-bea7-5f05d494280f'
    );

    // Envia o request
    $response = $this->postJson('/api/invoice/process', ['file' => $file]);

    // Verifica se o arquivo foi salvo no storage mockado
    Storage::assertExists('uploads/' . $file->hashName());

    // Verifica se o job foi despachado
    Queue::assertPushed(ProcessInvoiceJob::class, function ($job) use ($file) {
        // Verifica se o arquivo do job despachado esta no mesmo path do arquivo salvo no storage mockado
        return $job->filePath === storage_path('app/uploads/' . $file->hashName());
    });

    // Verifica a resposta
    $response->assertStatus(200)
        ->assertJson(['message' => 'Arquivo recebido e processamento iniciado']);
});

it('Deve retornar erro 422 ao enviar arquivo inválido', function () {
    // envia o request sem enviar um arquivo
    $response = $this->postJson('/api/invoice/process', ['file' => '']);
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['file']);
});
