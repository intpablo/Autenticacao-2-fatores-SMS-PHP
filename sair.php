<?php

session_start(); // Iniciar a sessão

ob_start(); // Limpar o buffer de saída

// Destruir as sessões
unset($_SESSION['id'], $_SESSION['usuario'], $_SESSION['nome'], $_SESSION['codigo_autenticacao']);

// Criar a mensagem de deslogado
$_SESSION['msg'] = "<p style='color: green;'>Deslogado com sucesso!</p>";

// Redirecionar o usuário
header('Location: index.php');

// Pausar o processamento
exit();