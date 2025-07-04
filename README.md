# 🍕 Della Vita

**Della Vita** é uma aplicação web para gerenciamento de pedidos de uma pizzaria, oferecendo recursos como cardápio online, seleção de produtos, acompanhamento de pedidos e área administrativa para gerenciamento de produtos e pedidos.

## 📌 Funcionalidades

- Cadastro e login de usuários
- Visualização e filtragem de cardápio
- Adição de produtos ao pedido
- Acompanhamento de pedidos em tempo real
- Área administrativa com:
  - Cadastro de produtos (com imagem e ingredientes)
  - Controle de status na cozinha
- Personalização de perfil do usuário

## 🚀 Tecnologias Utilizadas

- **Frontend**: HTML5, CSS3, JavaScript (puro)
- **Backend**: PHP (sem frameworks)
- **Banco de Dados**: MySQL
- **Outros**:
  - JSON para armazenamento de descrições de produtos
  - Sistema de login com sessões PHP
  - Upload de imagens
  - Integração com ícones SVG e imagens personalizadas

## 🗂️ Estrutura de Diretórios

```
BD/             # Script SQL para estrutura do banco de dados
CSS/            # Estilos das páginas
IMG/            # Imagens do site (logos, produtos, ícones)
IMG/Produtos/   # Imagens das pizzas
JS/             # Scripts JavaScript para funcionalidades dinâmicas
Json/           # Arquivos JSON com descrição dos produtos
System/         # Scripts PHP de sistema (login, cadastro, sessões, banco)
Pages/          # Páginas principais do site (index, login, cardápio, etc.)
```

## 🧪 Como Usar

1. Clone o repositório:
   ```bash
   git clone https://github.com/HederGabriel/Della-Vita.git
   cd Della-Vita
   ```

2. Configure o ambiente local:
   - Coloque os arquivos em um servidor local como **XAMPP** ou **WampServer**
   - Importe o banco de dados `BD/Della Vita.sql` no phpMyAdmin
   - Ajuste a conexão no arquivo `System/db.php` com os dados do seu MySQL

3. Acesse o sistema:
   - Frontend: `http://localhost/Della-Vita/index.php`
   - Admin: Após login, acesse funcionalidades como `/ADM.php`, `/cozinha.php`, etc.

## 🧠 Banco de Dados

Incluído em: `BD/Della Vita.sql`  
Contém as tabelas principais para usuários, pedidos e produtos.

## 📷 Imagens e Produtos

- As imagens dos produtos estão armazenadas em `IMG/Produtos`
- Cada produto tem um arquivo `.json` correspondente em `Json/` com sua descrição completa e ingredientes.

## 📄 Licença

Distribuído sob a licença MIT. Veja `LICENSE` para mais informações.

---

> Projeto acadêmico com fins de aprendizado e portfólio.
