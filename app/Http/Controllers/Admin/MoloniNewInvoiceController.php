<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\Moloni;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\MoloniInvoice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class MoloniNewInvoiceController extends Controller
{
    use Moloni;

    public function index()
    {
        abort_if(Gate::denies('moloni_new_invoice_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.moloniNewInvoices.index');
    }

    public function processOcr(MoloniInvoice $moloniInvoice)
    {
        $filePath = $moloniInvoice->file->getPath();

        if (!file_exists($filePath)) {
            return response()->json([
                'success' => false,
                'ocr' => '[OCR] Ficheiro não encontrado no caminho: ' . $filePath
            ]);
        }

        $response = Http::attach(
            'file',
            file_get_contents($filePath),
            basename($filePath)
        )->post('https://api.ocr.space/parse/image', [
            'apikey' => env('OCR_API', 'CHAVE_INVALIDA'),
            'language' => 'por',
            'isOverlayRequired' => false,
            'OCREngine' => 2,
        ]);

        $data = $response->json();

        // Inicializa string vazia
        $ocrText = '';

        // Verifica se o resultado está presente e é válido
        if (!empty($data['ParsedResults']) && is_array($data['ParsedResults'])) {
            foreach ($data['ParsedResults'] as $parsed) {
                // Adiciona apenas o texto extraído, se existir
                if (isset($parsed['ParsedText'])) {
                    $ocrText .= $parsed['ParsedText'] . "\n";
                }
            }
        } else {
            $ocrText = '[OCR] Erro: ' . ($data['ErrorMessage'] ?? 'Resposta inválida da API');
        }

        // Salva texto como string
        $moloniInvoice->ocr = $ocrText;
        $moloniInvoice->save();

        return response()->json([
            'success' => true,
            'ocr' => $ocrText,
        ]);
    }
}
