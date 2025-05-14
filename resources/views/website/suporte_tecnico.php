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
      box-shadow: 0 1px 5px rgba(0, 0, 0, 0.05);
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
      justify-content: space-between;
      height: 500px;
      /* altura fixa do chat */
      margin-top: 30px;
      padding: 20px;
      border-radius: 10px;
      background-color: #ffffff;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
    }

    .mensagens-container {
      flex-grow: 1;
      overflow-y: auto;
      margin-bottom: 15px;
    }
  </style>
</head>

<body>
  <div class="container-fluid">
    <div class="row bg-primary text-white py-3 mb-4">
      <div class="col text-center">
        <h1>Suporte Técnico Pós-Venda</h1>
        <p>Estamos aqui para ajudar!</p>
      </div>
    </div>
  </div>

  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-lg-6 col-md-8">
        <label for="fatura" class="form-label">Nº da Fatura</label>
        <input type="text" class="form-control form-control-lg mb-3" id="fatura" placeholder="Introduz o número da fatura">
        <button class="btn btn-primary w-100" onclick="verificarFatura()">Submeter</button>
      </div>
    </div>

    <!-- Área do chat -->
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="chat-box" id="chatBox">
          <!-- Mensagens -->
          <div id="mensagens" class="d-flex flex-column mensagens-container"></div>

          <!-- Input + botões -->
          <div>
            <input type="text" id="mensagemUsuario" class="form-control mb-2" placeholder="Escreve a tua dúvida...">
            <button id="enviarBtn" class="btn btn-success w-100 mb-2" onclick="enviarParaOpenAI()">Enviar</button>
            <button class="btn btn-secondary w-100" onclick="reiniciarConversa()">Reiniciar Conversa</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    let faturaValidada = false;
    let faturaData = null; // guardar o contexto da fatura

    function verificarFatura() {
      const input = document.getElementById('fatura');
      const numeroFatura = input.value.trim();

      // Limpa mensagens anteriores e reseta estado
      document.getElementById('mensagens').innerHTML = '';
      document.getElementById('chatBox').style.display = 'none';
      faturaValidada = false;
      faturaData = null;

      if (!numeroFatura) {
        document.getElementById('chatBox').style.display = 'flex'; // Abre o chat
        mostrarMensagemNoChat("Por favor, introduza o número da fatura.", 'bot');
        return;
      }


      const numeroCodificado = encodeURIComponent(numeroFatura);

      fetch(`https://zcmanager.com/api/suporte-tecnico/zcmrequest?invoice_number=${numeroCodificado}`)
        .then(response => response.json())
        .then(data => {
          if (data.error) {
            mostrarMensagemNoChat("A fatura não foi encontrada ou já foi emitida há mais de um mês.", 'bot');
            return;
          }

          const mensagemInicial = gerarMensagemDeInstrucao(data);
          mostrarMensagemNoChat(mensagemInicial, 'bot');
          document.getElementById('chatBox').style.display = 'flex';
          faturaValidada = true;
          faturaData = data; // guardar o contexto para enviar depois
        })
        .catch(() => {
          mostrarMensagemNoChat("Ocorreu um erro ao contactar o servidor. Tenta novamente.", 'bot');
        });
    }

    function gerarMensagemDeInstrucao(data) {
      return `Recebemos a fatura nº ${data.invoice_number}, de ${data.invoice_date}.
Produto: ${data.product}.
Veículo: ${data.car}.
Se precisar de ajuda, escreva aqui e eu vou ajudar!`;
    }

    function mostrarMensagemNoChat(texto, tipo) {
      const mensagens = document.getElementById('mensagens');
      const msg = document.createElement('div');
      msg.classList.add('chat-message', tipo);
      msg.innerText = texto;
      mensagens.appendChild(msg);

      // Scroll automático para a última mensagem
      mensagens.scrollTop = mensagens.scrollHeight;
    }

    function enviarParaOpenAI() {
      if (!faturaValidada) {
        alert('Por favor, introduza primeiro um número de fatura válido.');
        return;
      }

      const inputMsg = document.getElementById('mensagemUsuario');
      const enviarBtn = document.getElementById('enviarBtn');
      const mensagem = inputMsg.value.trim();
      if (!mensagem) return;

      mostrarMensagemNoChat(mensagem, 'user');
      inputMsg.value = '';

      // Desativa o botão e mostra loading
      enviarBtn.disabled = true;
      enviarBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> A responder...';

      fetch('api/chat', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            mensagem,
            contexto: faturaData // envia também o contexto da fatura
          })
        })
        .then(response => response.json())
        .then(data => {
          mostrarMensagemNoChat(data.resposta, 'bot');
        })
        .catch(() => {
          mostrarMensagemNoChat("Erro ao contactar o assistente. Tenta novamente.", 'bot');
        })
        .finally(() => {
          // Reativa o botão e repõe o texto original
          enviarBtn.disabled = false;
          enviarBtn.innerText = 'Enviar';
        });
    }


    function reiniciarConversa() {
      if (!confirm("Tem a certeza que quer reiniciar a conversa?")) return;

      fetch('/api/chat/reset', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          }
        })
        .then(() => {
          document.getElementById('mensagens').innerHTML = '';
          document.getElementById('chatBox').style.display = 'none';
          faturaValidada = false;
          faturaData = null;
          mostrarMensagemNoChat("A conversa foi reiniciada. Pode introduzir uma nova fatura.", 'bot');
        })
        .catch(() => {
          alert('Erro ao reiniciar a conversa. Tente novamente.');
        });
    }

    // Permitir envio com a tecla Enter
    document.getElementById('mensagemUsuario').addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault(); // impede quebra de linha
        enviarParaOpenAI();
      }
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>