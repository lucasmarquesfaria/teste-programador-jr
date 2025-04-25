# Sistema de Gerenciamento de Tarefas



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