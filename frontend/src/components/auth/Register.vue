<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const API_URL = 'http://localhost:8000/api'
const router = useRouter()

const formRegistro = reactive({
  nome: '',
  email: '',
  password: '',
  confirmPassword: ''
})

const erro = ref('')
const carregando = ref(false)

const formTocado = reactive({
  nome: false,
  email: false,
  password: false,
  confirmPassword: false
})

const marcarTocado = (campo) => {
  formTocado[campo] = true
}

const validacoes = {
  nome: (valor) => {
    if (!valor) return 'O nome é obrigatório'
    if (valor.length < 3) return 'O nome deve ter pelo menos 3 caracteres'
    return ''
  },
  email: (valor) => {
    if (!valor) return 'O email é obrigatório'
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valor)) return 'Formato de email inválido'
    return ''
  },
  password: (valor) => {
    if (!valor) return 'A senha é obrigatória'
    if (valor.length < 6) return 'A senha deve ter pelo menos 6 caracteres'
    return ''
  },
  confirmPassword: (valor) => {
    if (!valor) return 'A confirmação de senha é obrigatória'
    if (valor !== formRegistro.password) return 'As senhas não conferem'
    return ''
  }
}

const validarCampo = (campo) => {
  if (!formTocado[campo]) return ''
  return validacoes[campo](formRegistro[campo])
}

const formularioValido = () => {
  return !validarCampo('nome') && 
         !validarCampo('email') && 
         !validarCampo('password') && 
         !validarCampo('confirmPassword')
}

const fazerRegistro = async () => {
  Object.keys(formTocado).forEach(campo => formTocado[campo] = true)
  
  if (!formularioValido()) return
  
  carregando.value = true
  erro.value = ''
  
  try {
    const response = await axios.post(`${API_URL}/register`, {
      nome: formRegistro.nome,
      email: formRegistro.email,
      password: formRegistro.password
    })
    
    // Armazena os dados do usuário e token após registro bem-sucedido
    const usuario = {
      id: response.data.user.id,
      nome: response.data.user.name,
      email: response.data.user.email,
      token: response.data.token
    }
    
    localStorage.setItem('usuarioLogado', JSON.stringify(usuario))
    
    // Redireciona para a página de tarefas
    router.push('/tarefas')
  } catch (error) {
    console.error('Erro ao fazer registro:', error)
    
    if (error.response) {
      if (error.response.data.message) {
        erro.value = error.response.data.message
      } else if (error.response.data.error) {
        erro.value = error.response.data.error
      } else if (error.response.data.errors) {
        // Pode haver múltiplos erros de validação
        const primeiroErro = Object.values(error.response.data.errors)[0]
        erro.value = Array.isArray(primeiroErro) ? primeiroErro[0] : primeiroErro
      } else {
        erro.value = 'Erro ao fazer registro'
      }
    } else {
      erro.value = error.message || 'Erro ao fazer registro'
    }
  } finally {
    carregando.value = false
  }
}

const irParaLogin = () => {
  router.push('/login')
}
</script>

<template>
  <div class="register-container">
    <div class="register-card">
      <div class="register-header">
        <h1>Criar Conta</h1>
        <p>Registre-se para acessar o sistema de tarefas</p>
      </div>
      
      <form @submit.prevent="fazerRegistro" class="register-form">
        <div class="form-group">
          <label for="nome">Nome</label>
          <input 
            type="text" 
            id="nome" 
            v-model="formRegistro.nome"
            @blur="marcarTocado('nome')"
            :class="{ 'erro': validarCampo('nome') }"
            autocomplete="name"
          >
          <span class="erro-mensagem" v-if="validarCampo('nome')">
            {{ validarCampo('nome') }}
          </span>
        </div>
        
        <div class="form-group">
          <label for="email">Email</label>
          <input 
            type="email" 
            id="email" 
            v-model="formRegistro.email"
            @blur="marcarTocado('email')"
            :class="{ 'erro': validarCampo('email') }"
            autocomplete="email"
          >
          <span class="erro-mensagem" v-if="validarCampo('email')">
            {{ validarCampo('email') }}
          </span>
        </div>
        
        <div class="form-group">
          <label for="password">Senha</label>
          <input 
            type="password" 
            id="password" 
            v-model="formRegistro.password"
            @blur="marcarTocado('password')"
            :class="{ 'erro': validarCampo('password') }"
            autocomplete="new-password"
          >
          <span class="erro-mensagem" v-if="validarCampo('password')">
            {{ validarCampo('password') }}
          </span>
        </div>
        
        <div class="form-group">
          <label for="confirmPassword">Confirmar Senha</label>
          <input 
            type="password" 
            id="confirmPassword" 
            v-model="formRegistro.confirmPassword"
            @blur="marcarTocado('confirmPassword')"
            :class="{ 'erro': validarCampo('confirmPassword') }"
            autocomplete="new-password"
          >
          <span class="erro-mensagem" v-if="validarCampo('confirmPassword')">
            {{ validarCampo('confirmPassword') }}
          </span>
        </div>
        
        <div class="erro-geral" v-if="erro">
          {{ erro }}
        </div>
        
        <button 
          type="submit" 
          class="register-button" 
          :disabled="carregando"
        >
          <span v-if="!carregando">Registrar</span>
          <span v-else>Processando...</span>
        </button>
        
        <div class="login-link">
          Já tem uma conta? <a href="#" @click.prevent="irParaLogin">Faça login</a>
        </div>
      </form>
    </div>
  </div>
</template>

<style>
.register-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #f5f7fa;
  background-image: linear-gradient(135deg, #f5f7fa 0%, #e4efe9 100%);
}

.register-card {
  background-color: white;
  border-radius: 8px;
  width: 100%;
  max-width: 500px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.register-header {
  background-color: #3d7ab8;
  color: white;
  padding: 20px;
  text-align: center;
}

.register-header h1 {
  margin: 0 0 10px;
  font-size: 24px;
}

.register-header p {
  margin: 0;
  font-size: 14px;
  opacity: 0.8;
}

.register-form {
  padding: 20px;
}

.form-group {
  margin-bottom: 20px;
}

label {
  display: block;
  margin-bottom: 8px;
  font-weight: 500;
  color: #495057;
}

input[type="text"],
input[type="email"],
input[type="password"] {
  width: 100%;
  padding: 12px;
  font-size: 14px;
  border: 1px solid #ced4da;
  border-radius: 4px;
  transition: border-color 0.2s, box-shadow 0.2s;
}

input:focus {
  outline: none;
  border-color: #80bdff;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

input.erro {
  border-color: #dc3545;
}

.erro-mensagem {
  display: block;
  color: #dc3545;
  font-size: 12px;
  margin-top: 5px;
}

.erro-geral {
  background-color: #f8d7da;
  color: #721c24;
  padding: 10px;
  border-radius: 4px;
  margin-bottom: 20px;
  text-align: center;
}

.register-button {
  display: block;
  width: 100%;
  padding: 12px;
  background-color: #3d7ab8;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 16px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
}

.register-button:hover {
  background-color: #2c5987;
}

.register-button:disabled {
  background-color: #6c757d;
  cursor: not-allowed;
}

.login-link {
  text-align: center;
  margin-top: 15px;
  color: #6c757d;
  font-size: 14px;
}

.login-link a {
  color: #3d7ab8;
  text-decoration: none;
  font-weight: 500;
}

.login-link a:hover {
  text-decoration: underline;
}
</style>