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

      .chat-bubble {
        background-color: #e9f5ff;
        border-radius: 20px;
        padding: 20px;
        max-width: 100%;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        font-size: 1.1rem;
      }

      .chat-message {
        background-color: #fff;
        border-radius: 15px;
        padding: 15px;
        margin-bottom: 10px;
        box-shadow: 0 1px 5px rgba(0,0,0,0.05);
      }

      .chat-message.bot {
        background-color: #dbeeff;
        align-self: flex-start;
      }

      .chat-message.user {
        background-color: #dcf8c6;
        align-self: flex-end;
        text-align: right;
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
        <!-- Coluna esquerda: Campo de Nº da Fatura -->
        <div class="col-lg-4 col-md-5 mb-4">
          <label for="fatura" class="form-label">Nº da Fatura</label>
          <input type="text" class="form-control form-control-lg mb-3" id="fatura" placeholder="Introduz o número da fatura">
          <button class="btn btn-primary w-100" onclick="validarFatura()">Submeter</button>
        </div>

        <!-- Coluna direita: Zé da Zentrum e balão -->
        <div class="col-lg-8 col-md-7 d-flex align-items-start gap-3">
          <img src="{{ asset('images/zedazentrum2.svg') }}" alt="Zé da Zentrum" class="person">
          <div class="chat-bubble">
            <strong>Olá!</strong><br>
            Sou o Zé da Zentrum.<br>
            Estou aqui para te ajudar a utilizar a peça que recebeste da <strong>Techniczentrum</strong>.
          </div>
        </div>
      </div>

      <!-- Caixa de chat (oculta até validação) -->
      <div class="chat-box mt-5" id="chatBox">
        <div class="chat-message bot">
          👋 Olá! Diz-me qual é a dificuldade com a peça e vou tentar ajudar.
        </div>
        <div class="chat-message user">
          A peça não encaixa corretamente no conector.
        </div>
        <div class="chat-message bot">
          Obrigado pela informação! Podes enviar uma foto? Ou dizer-me a referência do conector?
        </div>
      </div>
    </div>

    <script>
      function validarFatura() {
        const fatura = document.getElementById('fatura').value.trim();
        if (fatura.length > 3) {
          document.getElementById('chatBox').style.display = 'flex';
        } else {
          alert('Por favor, introduz um número de fatura válido.');
        }
      }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
