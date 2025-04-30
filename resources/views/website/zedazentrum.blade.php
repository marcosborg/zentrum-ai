<!doctype html>
<html lang="pt">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Suporte Técnico - Zentrum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      body {
        background-color: #f8f9fa;
      }

      .chat-message {
        background-color: #fff;
        border-radius: 15px;
        padding: 15px;
        margin-bottom: 10px;
        box-shadow: 0 1px 5px rgba(0,0,0,0.05);
        width: fit-content;
        max-width: 80%;
      }

      .chat-message.bot {
        background-color: #dbeeff;
        align-self: flex-start;
      }

      .chat-message.user {
        background-color: #dcf8c6;
        align-self: flex-end;
        text-align: right;
        margin-left: auto;
      }

      .chat-box {
        display: none;
        flex-direction: column;
        gap: 10px;
        margin-top: 30px;
        padding: 20px;
        border-radius: 10px;
        background-color: #ffffff;
        box-shadow: 0 0 15px rgba(0,0,0,0.05);
      }

      .person {
        max-width: 120px;
        height: auto;
      }
    </style>
  </head>
  <body>
    <div class="container py-5">
      <h2 class="mb-4 text-primary">Suporte Técnico Pós-Venda</h2>

      <div class="row gx-5 align-items-start">
        <!-- Coluna esquerda -->
        <div class="col-lg-4 col-md-5 mb-4">
          <label for="fatura" class="form-label">Nº da Fatura</label>
          <input type="text" class="form-control form-control-lg mb-3" id="fatura" placeholder="Introduz o número da fatura">
          <button class="btn btn-primary w-100" onclick="verificarFatura()">Submeter</button>
        </div>

        <!-- Coluna direita -->
        <div class="col-lg-8 col-md-7 d-flex align-items-start gap-3">
          <img src="{{ asset('images/zedazentrum2.svg') }}" alt="Zé da Zentrum" class="person">
          <div class="chat-message bot">
            <strong>Olá!</strong><br>
            Sou o Zé da Zentrum.<br>
            Estou aqui para te ajudar a utilizar a peça que recebeste da <strong>Techniczentrum</strong>.
          </div>
        </div>
      </div>

      <!-- Área do chat -->
      <div class="chat-box" id="chatBox">
        <div id="mensagens" class="d-flex flex-column"></div>

        <div class="mt-3">
          <input type="text" id="mensagemUsuario" class="form-control mb-2" placeholder="Escreve a tua dúvida...">
          <button class="btn btn-success w-100" onclick="enviarParaOpenAI()">Enviar</button>
        </div>
      </div>
    </div>

    <script>
      let faturaValidada = false;

      function verificarFatura() {
        const input = document.getElementById('fatura');
        const numeroFatura = input.value.trim();

        if (!numeroFatura) {
          alert('Por favor, introduz o número da fatura.');
          return;
        }

        const numeroCodificado = encodeURIComponent(numeroFatura);

        fetch(`https://zcmanager.com/api/zedazentrum/zcmrequest?invoice_number=${numeroCodificado}`)
          .then(response => response.json())
          .then(data => {
            if (data.error) {
              mostrarMensagemNoChat("A fatura não foi encontrada ou já foi emitida há mais de um mês.", 'bot');
            } else {
              const mensagemInicial = gerarMensagemDeInstrucao(data);
              mostrarMensagemNoChat(mensagemInicial, 'bot');
              document.getElementById('chatBox').style.display = 'flex';
              faturaValidada = true;
            }
          })
          .catch(() => {
            mostrarMensagemNoChat("Ocorreu um erro ao contactar o servidor. Tenta novamente.", 'bot');
          });
      }

      function gerarMensagemDeInstrucao(data) {
        return `Recebemos a fatura nº ${data.invoice_number}, de ${data.invoice_date}.
Produto: ${data.product}.
Veículo: ${data.car}.
Se precisares de ajuda, escreve aqui e eu, o Zé da Zentrum, já te respondo!`;
      }

      function mostrarMensagemNoChat(texto, tipo) {
        const mensagens = document.getElementById('mensagens');
        const msg = document.createElement('div');
        msg.classList.add('chat-message', tipo);
        msg.innerText = texto;
        mensagens.appendChild(msg);
      }

      function enviarParaOpenAI() {
        if (!faturaValidada) {
          alert('Por favor, introduz primeiro um número de fatura válido.');
          return;
        }

        const inputMsg = document.getElementById('mensagemUsuario');
        const mensagem = inputMsg.value.trim();
        if (!mensagem) return;

        mostrarMensagemNoChat(mensagem, 'user');
        inputMsg.value = '';

        fetch('/api/chat', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ mensagem })
        })
        .then(response => response.json())
        .then(data => {
          mostrarMensagemNoChat(data.resposta, 'bot');
        })
        .catch(() => {
          mostrarMensagemNoChat("Erro ao contactar o assistente. Tenta novamente.", 'bot');
        });
      }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
