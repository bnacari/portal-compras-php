



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* Estilos CSS adicionais para descrições */
        .descricao1 {
            color: #132835;
            /* Azul */
            font-weight: bold;
            /* font-style: italic; */
        }

        .descricao2 {
            color: #193446;
            /* Azul mais claro */
            font-weight: bold;
            font-style: italic;
        }

        .descricao3 {
            color: #B7B7B7;
            /* Azul mais claro ainda */
            font-style: italic;
        }

                /* Estilo personalizado para o modal */
                .modal-header {
            background-color: #0868A5;
            color: white;
            border-bottom: none;
        }

        .modal-title {
            font-weight: bold;
        }

        .modal-body {
            padding: 30px;
        }

       
    </style>
</head>

<body>

    <!-- Button to trigger modal -->
    <a href="#contatoModal" data-toggle="modal">Abrir Modal</a>


    <!-- Modal -->
    <div class="modal fade" id="contatoModal" tabindex="-1" role="dialog" aria-labelledby="contatoModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contatoModalLabel">Contatos</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="descricao1">Informações, dúvidas e esclarecimentos sobre licitação Cesan</p>
                    <p class="descricao2">Comissão Permanente de Licitações (CPL)</p>
                    <p class="descricao3">Rua Nelcy Lopes Vieira, S/N, Jardim Limoeiro, Serra, ES, CEP: 29164-018</p>
                    <ul class="list-group">
                        <li class="list-group-item"><i class="bi bi-telephone"></i> (27) 2127-5119</li>
                        <li class="list-group-item"><i class="bi bi-chat-right-text"></i> licitacoes@cesan.com.br</li>
                    </ul>
                </div>
                <div class="modal-body">
                    <p class="descricao1">Informações, dúvidas e esclarecimentos sobre pregões, dispensas eletrônicas e cadastro de fornecedores</p>
                    <p class="descricao2">Divisão de Compras e Suprimentos (A-DCS)</p>
                    <p class="descricao3">Rua Nelcy Lopes Vieira, S/N, Jardim Limoeiro, Serra, ES. CEP: 29.164-018</p>
                    <ul class="list-group">
                        <p class="descricao2">Pregoeiros</p>
                        <li class="list-group-item"><i class="bi bi-chat-right-text"></i> pregao@cesan.com.br</li>
                        <li class="list-group-item"><i class="bi bi-telephone"></i> Luciana Spinassé - (27) 2127-5299</li>
                        <li class="list-group-item"><i class="bi bi-telephone"></i> Fernando Cordeiro - (27) 2127-5418</li>
                        <li class="list-group-item"><i class="bi bi-telephone"></i> Mirelle Ino - (27) 2127-5429</li>
                    </ul>
                    <br>
                    <ul class="list-group">
                        <p class="descricao2">Cadastro de Fornecedores</p>
                        <li class="list-group-item"><i class="bi bi-chat-right-text"></i> cadastrofornecedor@cesan.com.br</li>

                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery e Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>