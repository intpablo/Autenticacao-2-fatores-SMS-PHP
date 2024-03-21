<?php

session_start(); // Iniciar a sessão

ob_start(); // Limpar o buffer de saída

// Incluir o arquivo com as configurações
include_once './config.php';

// Definir um fuso horario padrao
date_default_timezone_set('America/Sao_Paulo');

// Incluir o arquivo com a conexão com banco de dados
include_once './conexao.php';

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Celke - Login</title>
</head>

<body>

    <h2>Login</h2>

    <?php
    // Exemplo criptografar a senha
    //echo password_hash(123456, PASSWORD_DEFAULT);

    // Receber os dados do formulário
    $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

// Acessar o IF quando o usuário clicar no botão acessar do formulário
if(!empty($dados['SendLogin'])) {
    //var_dump($dados);

    // Recuperar os dados do usuário no banco de dados
    $query_usuario = "SELECT id, nome, usuario, senha_usuario, celular
                    FROM usuarios
                    WHERE usuario=:usuario
                    LIMIT 1";

    // Preparar a QUERY
    $result_usuario = $conn->prepare($query_usuario);

    // Substituir o link da query pelo valor que vem do formulário
    $result_usuario->bindParam(':usuario', $dados['usuario']);

    // Executar a QUERY
    $result_usuario->execute();

    // Acessar o IF quando encontrar usuário no banco de dados
    if(($result_usuario) and ($result_usuario->rowCount() != 0)) {

        // Ler os registros retorando do banco de dados
        $row_usuario = $result_usuario->fetch(PDO::FETCH_ASSOC);
        //var_dump($row_usuario);

        // Acessar o IF quando a senha é válida
        if(password_verify($dados['senha_usuario'], $row_usuario['senha_usuario'])) {

            // Recuperar a data atual
            $data = date('Y-m-d H:i:s');

            // Gerar número randômico entre 100000 e 999999
            $codigo_autenticacao = mt_rand(100000, 999999);

            // QUERY para salvar no banco de dados o código e a data gerada
            $query_up_usuario = "UPDATE usuarios SET
                    codigo_autenticacao=:codigo_autenticacao,
                    data_codigo_autenticacao=:data_codigo_autenticacao
                    WHERE id=:id
                    LIMIT 1";

            // Preparar a QUERY
            $result_up_usuario = $conn->prepare($query_up_usuario);

            // Substituir o link da QUERY pelo valores
            $result_up_usuario->bindParam(':codigo_autenticacao', $codigo_autenticacao);
            $result_up_usuario->bindParam(':data_codigo_autenticacao', $data);
            $result_up_usuario->bindParam(':id', $row_usuario['id']);

            // Executar a QUERY
            $result_up_usuario->execute();

            // Enviar o SMS com o código
            // Criar a mensagem
            $mensagem = urlencode("[Ruan] Codigo de verificacao: $codigo_autenticacao");

            // URL com os dados
            $url_api = "https://api.iagentesms.com.br/webservices/http.php?metodo=envio&usuario=ruanruandnz@gmail.com&senha=12345&celular=5199999999&mensagem=$mensagem&codigoms=300". USUARIOIAGENTE."&senha=".SENHAIAGENTE."&celular=".$row_usuario['celular'] ."&mensagem=$mensagem&codigosms=300";

            // Realizar a requisição HTTP
            $resposta_api = file_get_contents($url_api);

            if($resposta_api == "OK") {
                // Salvar os dados do usuário na sessão
                $_SESSION['id'] = $row_usuario['id'];
                $_SESSION['usuario'] = $row_usuario['usuario'];

                // Redirecionar o usuário
                header('Location: validar_codigo.php');

                // Pausar o processamento
                exit();

            } else {
                $_SESSION['msg'] = "<p style='color: #f00;'>Erro: Tente mais tarde!</p>";
            }
        } else {
            $_SESSION['msg'] = "<p style='color: #f00;'>Erro: Usuário ou senha inválida!</p>";
        }

    } else {
        $_SESSION['msg'] = "<p style='color: #f00;'>Erro: Usuário ou senha inválida!</p>";
    }
}

// Imprimir a mensagem da sessão
if(isset($_SESSION['msg'])) {
    echo $_SESSION['msg'];
    unset($_SESSION['msg']);
}

?>

    <!-- Inicio do formulário de login -->
    <form method="POST" action="">
        <label>Usuário: </label>
        <input type="text" name="usuario" placeholder="Digite o usuário"><br><br>

        <label>Senha: </label>
        <input type="password" name="senha_usuario" placeholder="Digite a senha"><br><br>

        <input type="submit" name="SendLogin" value="Acessar"><br><br>

    </form>
    <!-- Fim do formulário de login -->

    Usuário: ruanruandnz@gmail.com<br>
    Senha: 123456

</body>

</html>