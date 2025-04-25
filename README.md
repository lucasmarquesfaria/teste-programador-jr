# Sistema de Gerenciamento de Tarefas

### FrontEnd
Node.js 16.0 ou mais atual
Vue.js 3 (o framework principal que usamos)
Vue Router (para navegação entre páginas)
Axios (para comunicação com o servidor)
Bootstrap (para os estilos e componentes visuais)
Font Awesome (para os ícones bonitos)
Um navegador moderno (Chrome, Firefox ou Edge)

### Para o backend
PHP 8.1 ou mais recente
Laravel (o framework que usamos)
JWT para autenticação (login seguro)
SQLite, MySQL ou PostgreSQL para o banco de dados

### 1. Clone o repositório

```bash
git clone https://github.com/lucasmarquesfaria/sistema-tarefas
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