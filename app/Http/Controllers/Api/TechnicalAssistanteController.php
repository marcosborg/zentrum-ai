<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class TechnicalAssistanteController extends Controller
{
    public function responder(Request $request)
    {
        $request->validate([
            'mensagem' => 'required|string|min:3',
        ]);

        $mensagemUser = $request->input('mensagem');

        try {
            Log::info('[Assistente Zentrum] Mensagem recebida do utilizador: ' . $mensagemUser);

            $resposta = OpenAI::chat()->create([
                'model' => 'gpt-4',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Atua como o Zé da Zentrum, um assistente técnico pós-venda simpático e direto, que ajuda clientes a instalar ou compreender peças enviadas pela Techniczentrum.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $mensagemUser
                    ],
                ],
                'temperature' => 0.7,
                'max_tokens' => 500,
            ]);

            $respostaTexto = $resposta->choices[0]->message->content;

            Log::info('[Assistente Zentrum] Resposta enviada: ' . $respostaTexto);

            return response()->json([
                'resposta' => $respostaTexto
            ]);

        } catch (\Exception $e) {
            Log::error('[Assistente Zentrum] Erro: ' . $e->getMessage());

            return response()->json([
                'resposta' => 'Desculpa, ocorreu um erro ao tentar responder. Por favor tenta novamente mais tarde.'
            ], 500);
        }
    }
}
