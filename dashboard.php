<?php

session_start(); // Iniciar a sessão

ob_start(); // Limpar o buffer de saída

// Definir um fuso horario padrao
date_default_timezone_set('America/Sao_Paulo');

// Acessar o IF quando o usuário não estão logado e redireciona para página de login
if((!isset($_SESSION['id'])) and (!isset($_SESSION['usuario'])) and (!isset($_SESSION['codigo_autenticacao']))){

    // Criar a mensagem de erro
    $_SESSION['msg'] = "<p style='color: #f00;'>Erro: Necessário realizar o login para acessar a página!</p>";

    // Redirecionar o usuário
    header('Location: index.php');

    // Pausar o processamento
    exit();
}


// Imprimir os dados do usuário logado
echo "<h2>Bem-vindo {$_SESSION['nome']}</h2>";

// Link para sair do sistema administrativo
echo "<a href='sair.php'>Sair</a><br>";