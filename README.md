# Sistema de Gerenciamento de Tarefas

Um sistema completo de gerenciamento de tarefas pessoais com autenticação de usuários, desenvolvido com Laravel (backend) e Vue.js (frontend).

## Visão Geral

O sistema permite o gerenciamento completo de tarefas pessoais, com separação por usuário, autenticação segura e interface moderna e responsiva.

### Funcionalidades

- **Autenticação de usuários**: Cadastro, login e logout
- **Gerenciamento de tarefas**:
  - Listar todas as tarefas do usuário atual
  - Adicionar novas tarefas
  - Editar tarefas existentes
  - Excluir tarefas (individual ou em lote)
  - Marcar tarefas como concluídas
- **Segurança**: Autenticação via JWT (JSON Web Tokens)
- **API RESTful**: Comunicação entre frontend e backend através de API

## Tecnologias Utilizadas

### Backend
- PHP 8.1+
- Laravel 10.x
- JWT Authentication
- SQLite (configurável para MySQL/PostgreSQL)

### Frontend
- Vue.js 3
- Vue Router
- Vite
- CSS moderno (Flexbox/Grid)
- Font Awesome

## Requisitos

- PHP 8.1 ou superior
- Composer
- Node.js 16.0 ou superior
- NPM ou Yarn

## Instalação

Siga os passos abaixo para configurar o ambiente de desenvolvimento completo:

### 1. Clone o repositório

```bash
git clone https://github.com/seu-usuario/sistema-tarefas.git
cd sistema-tarefas
```

### 2. Configuração do Backend (Laravel)

```bash
# Entre na pasta do backend
cd backend

# Instale as dependências do PHP
composer install

# Copie o arquivo de ambiente
cp .env.example .env

# Gere a chave da aplicação
php artisan key:generate

# Configure o banco de dados SQLite (ou outro de sua preferência)
touch database/database.sqlite

# Execute as migrações e seeders
php artisan migrate --seed

# Inicie o servidor de desenvolvimento
php artisan serve
```

O backend estará disponível em: http://localhost:8000

### 3. Configuração do Frontend (Vue.js)

```bash
# Em outro terminal, entre na pasta do frontend
cd frontend

# Instale as dependências do JavaScript
npm install
# ou se preferir usar Yarn
# yarn

# Configure o arquivo .env para apontar para a API
echo "VITE_API_URL=http://localhost:8000/api" > .env

# Inicie o servidor de desenvolvimento
npm run dev
# ou se preferir usar Yarn
# yarn dev
```

O frontend estará disponível em: http://localhost:5173

## Configurações Adicionais

### Configuração do JWT (JSON Web Tokens)

O sistema utiliza JWT para autenticação. A chave secreta para assinatura dos tokens está configurada no arquivo `AuthController.php`. Em um ambiente de produção, você deve alterar essa chave e configurá-la através de variáveis de ambiente.

```php
// Em backend/app/Http/Controllers/AuthController.php
private $key = 'your-secret-key'; // Altere para uma chave segura
```

### Configuração do Banco de Dados

Por padrão, o sistema está configurado para usar SQLite, mas você pode alterar para MySQL, PostgreSQL ou outro banco suportado pelo Laravel editando o arquivo `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistema_tarefas
DB_USERNAME=root
DB_PASSWORD=
```

## Uso

### Credenciais para teste

Para acessar a aplicação após a instalação, você pode usar as seguintes credenciais:

- **Email**: usuario@teste.com
- **Senha**: 123456

### Principais endpoints da API

| Método | Rota | Descrição | Autenticação |
|--------|------|-----------|--------------|
| POST | `/api/login` | Autenticar usuário | Não |
| POST | `/api/register` | Registrar novo usuário | Não |
| POST | `/api/logout` | Encerrar sessão | Sim |
| GET | `/api/me` | Obter usuário atual | Sim |
| GET | `/api/tarefas` | Listar tarefas do usuário | Sim |
| POST | `/api/tarefas` | Adicionar nova tarefa | Sim |
| PUT | `/api/tarefas/{id}` | Atualizar uma tarefa | Sim |
| DELETE | `/api/tarefas/{id}` | Excluir uma tarefa | Sim |

## Estrutura do Projeto

### Backend (Laravel)

```
backend/
  ├── app/
  │   ├── Http/
  │   │   ├── Controllers/      # Controladores da aplicação
  │   │   │   ├── AuthController.php  # Autenticação
  │   │   │   └── TarefaController.php  # Gerenciamento de tarefas
  │   │   └── Middleware/       # Middlewares (ex: JWT)
  │   └── Models/               # Modelos da aplicação
  │       ├── Tarefa.php
  │       ├── User.php
  │       └── Usuario.php
  ├── database/
  │   ├── migrations/           # Migrações do banco de dados
  │   └── seeders/              # Seeders para dados iniciais
  └── routes/
      └── api.php               # Rotas da API
```

### Frontend (Vue.js)

```
frontend/
  ├── src/
  │   ├── components/           # Componentes reutilizáveis
  │   │   ├── auth/             # Componentes de autenticação
  │   │   └── tarefas/          # Componentes de tarefas
  │   ├── services/             # Serviços de comunicação com API
  │   │   ├── authService.js    # Serviço de autenticação
  │   │   └── tarefaService.js  # Serviço de tarefas
  │   ├── views/                # Páginas da aplicação
  │   │   ├── LoginView.vue     # Página de login
  │   │   └── TarefasView.vue   # Página de gerenciamento de tarefas
  │   ├── App.vue               # Componente raiz
  │   ├── main.js               # Ponto de entrada
  │   └── router.js             # Configuração de rotas
  └── index.html                # Arquivo HTML principal
```

## Implantação em Produção

### Backend

Para implantar o backend em um servidor de produção:

1. Configure o servidor web (Apache/Nginx) para apontar para a pasta `public/`
2. Certifique-se de que as permissões de pasta estão corretas
3. Configure o `.env` para ambiente de produção (cache, otimizações)
4. Execute `php artisan optimize` para otimizar o Laravel

### Frontend

Para implantar o frontend:

1. Gere a build de produção: `npm run build` ou `yarn build`
2. Faça o upload da pasta `dist/` para seu servidor web
3. Configure o servidor para redirecionar 404 para index.html (SPA)

## Contribuição

1. Faça um fork do projeto
2. Crie sua branch de feature (`git checkout -b feature/nova-funcionalidade`)
3. Commit suas alterações (`git commit -m 'Adiciona nova funcionalidade'`)
4. Push para a branch (`git push origin feature/nova-funcionalidade`)
5. Abra um Pull Request

## Licença

Este projeto está licenciado sob a licença MIT - veja o arquivo [LICENSE](LICENSE) para detalhes.