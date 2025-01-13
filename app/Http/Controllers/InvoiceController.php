<?php

namespace App\Http\Controllers;

use App\Services\ProcessService;
use Exception;
use Illuminate\Http\Request;
use App\Jobs\ProcessInvoiceJob;

class InvoiceController extends Controller
{
    protected $processService;

    public function process(Request $request)
    {
        try {
            // Valida se a requisicao esta recebendo um arquivo CSV ou TXT de no maximo 2mb
            $request->validate([
                'file' => 'required|mimes:csv,txt',
            ]);
            // Captura o arquivo da requisicao e salva no storage/app/uploads
            $filePath = $request->file('file')->store('uploads');
            // Passa o caminho do arquivo CSV o job processar os boletos
            ProcessInvoiceJob::dispatch(storage_path("app/{$filePath}"));
            return response()->json(['message' => 'Arquivo recebido e processamento iniciado'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
