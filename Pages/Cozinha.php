<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Della Vita - Cozinha</title>
    <link rel="stylesheet" href="..\CSS\cozinha.css">
</head>
<body>
    
    <section id="lista-pedido">
        <button id="sair" onclick="window.location.href='adm-cozinha.php'">Sair</button>
        <h1>Lista de Pedidos</h1>
        <button id="reload">RELOAD</button>
        <div class="pedido">
            <div class="avatar"></div>
            <p>Cliente: João Silva</p>
            <p>Itens: Pizza Margherita, Salada Caesar</p>
            <p>Status: Em Preparação</p>
            <p>Endereço: América Pereira Rocha, 825 - St. Santa Luzia, Posse - CEP: 73900000</p>
            <p>Horário: 20:12</p>
        </div>
        <div class="pedido">
            <div class="avatar"></div>
            <p>Cliente: Maria Oliveira</p>
            <p>Itens: Lasanha, Tiramisu</p>
            <p>Status: Aguardando Retirada</p>
            <p>Endereço: Retirada no Local</p>
            <p>Horário: 20:15</p>
        </div>
        <div class="pedido">
            <div class="avatar"></div>
            <p>Cliente: Carlos Pereira</p>
            <p>Itens: Risoto de Cogumelos, Vinho Tinto</p>
            <p>Status: Em Preparação</p>
            <p>Endereço: Retirada no Local</p>
            <p>Horário: 20:18</p>
        </div>
    </section>
</body>
</html>