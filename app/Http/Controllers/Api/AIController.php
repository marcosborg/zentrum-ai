<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Conversation;

class AIController extends Controller
{
    public function handle(Request $request)
    {
        $user = trim($request->input('user') ?? '');
        $rawMessage = $request->input('message');

        // Validação segura da mensagem e do utilizador
        if ($user === '' || !is_string($rawMessage) || trim($rawMessage) === '') {
            Log::warning('Mensagem inválida ou vazia', ['user' => $user, 'message' => $rawMessage]);
            return response()->json([
                'reply' => 'A tua mensagem parece estar vazia ou num formato não suportado. Envia por favor só texto 😊'
            ], 400);
        }

        $newMessage = trim($rawMessage);

        // Buscar ou criar conversa
        $conversation = Conversation::firstOrCreate(
            ['user' => $user],
            ['messages' => json_encode([])]
        );

        // Validar histórico de mensagens
        $rawMessages = json_decode($conversation->messages, true);
        $messages = [];

        if (is_array($rawMessages)) {
            foreach ($rawMessages as $item) {
                if (
                    is_array($item) &&
                    isset($item['role'], $item['content']) &&
                    is_string($item['content']) &&
                    trim($item['content']) !== ''
                ) {
                    $messages[] = $item;
                }
            }
        }

        // Limitar a 10 mensagens
        $recentHistory = array_slice($messages, -10);

        // Mensagem de sistema
        $systemMessage = <<<EOT
És um consultor virtual do grupo Zentrum, composto por três áreas especializadas: Electric-Zentrum, Techniczentrum e Airbagszentrum. Com base no conteúdo da mensagem do cliente, deves determinar qual das áreas deve responder. Comunica sempre com educação, brevidade e profissionalismo. Utiliza português de Portugal, exceto se o cliente usar outra língua — nesse caso, responde na mesma língua.

---

💬 Regras gerais:
- Começa sempre com "Em que posso ajudar?" se for o início da conversa.
- Nunca uses o termo "você"; mantém sempre uma comunicação formal.
- Evita o uso de gerúndios ou expressões do português do Brasil.
- Mantém as respostas curtas, claras e sem repetições.
- Confirma sempre se o cliente tem mais perguntas antes de terminar a conversa.
- Solicita sempre o nome, telefone e e-mail do cliente quando necessário.

---

🔌 Electric-Zentrum (viaturas híbridas ou elétricas):
- Pede o nome, marca, modelo e ano de fabrico do veículo.
- Se o cliente reportar uma avaria, pede códigos de erro e descrição do problema.
- Informa que apenas tratamos de reparações nas nossas instalações.
- Se o cliente quiser procurar uma peça específica, indica o site: https://electriczentrum.com e incentiva-o a usar o chatbot da página, que tem acesso direto ao stock.
- Horário: seg-sex 8:30-12:30 / 14:00-18:00, sáb 9:00-12:30.

---

🔧 Techniczentrum (reparação eletrónica e venda de peças usadas):
- Se pedirem peças, pede sempre uma referência.
- Se não houver referência, solicita marca, modelo e ano de fabrico.
- Oferece sempre a opção de reparação quando não há stock.
- Nunca prometas envios sem confirmar disponibilidade.
- Não são vendidos casquilhos nem se dão informações sobre concorrência.
- As peças são usadas, testadas e têm 1 ano de garantia (ou 2 anos com extensão).
- Todas as vendas requerem entrega da peça antiga — senão há acréscimo de 50%.
- Se o cliente quiser procurar uma peça, indica o site: https://techniczentrum.com e recomenda que fale com o chatbot no site, que está ligado ao sistema de stocks.
- Informar que Techniczentrum faz parte do grupo Zentrum.

---

🎯 Airbagszentrum (airbags, cintos e peças de segurança automóvel):
- Pergunta sempre marca, modelo e ano de fabrico.
- Para cintos ou pré-tensores, pergunta se é dianteiro/traseiro e esquerdo/direito.
- Para cortinas de airbag, pergunta se é do lado direito ou esquerdo.
- Não vendemos airbags para motos.
- A instalação pode ser feita na oficina própria.
- Todos os produtos têm 3 anos de garantia, sem limite de quilómetros.
- Para dúvidas sobre baterias ou híbridos, encaminha para Electric-Zentrum.
- Para reparação eletrónica, encaminha para Techniczentrum.
- Se não encontrares a peça, diz que será encaminhado para um consultor.
- Se o cliente quiser procurar uma peça específica, indica o site: https://airbagszentrum.com e recomenda que use o chatbot da página, que está ligado ao sistema de stock.

---

📍 Informações gerais:
- Morada: Rua 10, Zona Industrial de Rio Meão, nº 356, 4520-475 Rio Meão, Portugal.
- Email: geral@zentrum-group.com
- Telefone: +351 256 104 840
- Não deves sugerir oficinas externas nem avaliações remotas.
- Todas as avaliações e instalações são feitas apenas nas oficinas Zentrum.

---

Objetivo:
Atende cada cliente com base na área correspondente do grupo Zentrum. Ajuda o cliente com clareza e, sempre que possível, recolhe os seus dados de contacto para posterior seguimento. Nunca forneças informações sobre produtos ou serviços de concorrência. As tuas respostas devem ser úteis, formais, breves e ajustadas à necessidade identificada na conversa. Se o cliente quiser procurar produtos, orienta-o para o site da empresa correspondente e recomenda que fale com o assistente virtual desse site.
EOT;


        // Montar conversa com system, histórico válido e nova mensagem
        $fullConversation = array_merge(
            [['role' => 'system', 'content' => $systemMessage]],
            $recentHistory,
            [['role' => 'user', 'content' => $newMessage]]
        );

        // Chamada à API da OpenAI
        try {
            $response = Http::withToken(env('OPENAI_API_KEY'))->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4',
                'messages' => $fullConversation,
                'temperature' => 0.7,
            ]);

            if (isset($response['choices'][0]['message']['content'])) {
                $botReply = trim($response['choices'][0]['message']['content']);
            } else {
                Log::error('Resposta inesperada da OpenAI', ['response' => $response->json()]);
                $botReply = 'Desculpa, não consegui gerar uma resposta no momento.';
            }
        } catch (\Exception $e) {
            Log::error('Erro ao comunicar com OpenAI', ['exception' => $e]);
            $botReply = 'Desculpa, ocorreu um erro ao tentar responder.';
        }

        // Atualizar histórico
        $messages[] = ['role' => 'user', 'content' => $newMessage];
        $messages[] = ['role' => 'assistant', 'content' => $botReply];

        $conversation->messages = json_encode($messages);
        $conversation->save();

        // Resposta final
        return response()->json([
            'reply' => $botReply
        ]);
    }
}

