<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import * as authService from '../../services/authService'

const router = useRouter()

const formLogin = reactive({
  email: '',
  senha: '',
})

const erro = ref('')
const carregando = ref(false)

const formTocado = reactive({
  email: false,
  senha: false
})

const marcarTocado = (campo) => {
  formTocado[campo] = true
}

const validacoes = {
  email: (valor) => {
    if (!valor) return 'O email é obrigatório'
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valor)) return 'Formato de email inválido'
    return ''
  },
  senha: (valor) => {
    if (!valor) return 'A senha é obrigatória'
    if (valor.length < 6) return 'A senha deve ter pelo menos 6 caracteres'
    return ''
  }
}

const validarCampo = (campo) => {
  if (!formTocado[campo]) return ''
  return validacoes[campo](formLogin[campo])
}

const formularioValido = () => {
  return !validarCampo('email') && !validarCampo('senha')
}

const fazerLogin = async () => {
  Object.keys(formTocado).forEach(campo => formTocado[campo] = true)
  
  if (!formularioValido()) return
  
  carregando.value = true
  erro.value = ''
  
  try {
    await authService.login(formLogin.email, formLogin.senha)
    router.push('/tarefas')
  } catch (error) {
    console.error('Erro ao fazer login:', error)
    
    if (error.response) {
      // Laravel geralmente retorna mensagens de erro em error.response.data.message ou error.response.data.error
      if (error.response.data.message) {
        erro.value = error.response.data.message
      } else if (error.response.data.error) {
        erro.value = error.response.data.error
      } else if (error.response.data.errors) {
        // Pode haver múltiplos erros de validação
        const primeiroErro = Object.values(error.response.data.errors)[0]
        erro.value = Array.isArray(primeiroErro) ? primeiroErro[0] : primeiroErro
      } else {
        erro.value = 'Erro ao fazer login'
      }
    } else {
      erro.value = error.message || 'Erro ao fazer login'
    }
  } finally {
    carregando.value = false
  }
}

const irParaRegistro = () => {
  router.push('/register')
}
</script>

<template>
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <h1>Login</h1>
        <p>Faça login para acessar o sistema de tarefas</p>
      </div>
      
      <form @submit.prevent="fazerLogin" class="login-form">
        <div class="form-group">
          <label for="email">Email</label>
          <input 
            type="email" 
            id="email" 
            v-model="formLogin.email"
            @blur="marcarTocado('email')"
            :class="{ 'erro': validarCampo('email') }"
            autocomplete="email"
          >
          <span class="erro-mensagem" v-if="validarCampo('email')">
            {{ validarCampo('email') }}
          </span>
        </div>
        
        <div class="form-group">
          <label for="senha">Senha</label>
          <input 
            type="password" 
            id="senha" 
            v-model="formLogin.senha"
            @blur="marcarTocado('senha')"
            :class="{ 'erro': validarCampo('senha') }"
            autocomplete="current-password"
          >
          <span class="erro-mensagem" v-if="validarCampo('senha')">
            {{ validarCampo('senha') }}
          </span>
        </div>
        
        <div class="erro-geral" v-if="erro">
          {{ erro }}
        </div>
        
        <button 
          type="submit" 
          class="login-button" 
          :disabled="carregando"
        >
          <span v-if="!carregando">Entrar</span>
          <span v-else>Entrando...</span>
        </button>
      </form>
      
      <div class="register-link">
        Não tem uma conta? <a href="#" @click.prevent="irParaRegistro">Registre-se</a>
      </div>
      

    </div>
  </div>
</template>

<style>
.login-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  background-color: #f5f7fa;
  background-image: linear-gradient(135deg, #f5f7fa 0%, #e4efe9 100%);
}

.login-card {
  background-color: white;
  border-radius: 8px;
  width: 100%;
  max-width: 400px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.login-header {
  background-color: #3d7ab8;
  color: white;
  padding: 20px;
  text-align: center;
}

.login-header h1 {
  margin: 0 0 10px;
  font-size: 24px;
}

.login-header p {
  margin: 0;
  font-size: 14px;
  opacity: 0.8;
}

.login-form {
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

.login-button {
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

.login-button:hover {
  background-color: #2c5987;
}

.login-button:disabled {
  background-color: #6c757d;
  cursor: not-allowed;
}

.login-info {
  background-color: #f8f9fa;
  padding: 15px;
  border-top: 1px solid #e9ecef;
  font-size: 13px;
  color: #6c757d;
}

.login-info p {
  margin: 5px 0;
}

.register-link {
  text-align: center;
  margin: 0 20px 15px;
  padding-top: 15px;
  border-top: 1px solid #e9ecef;
  color: #6c757d;
  font-size: 14px;
}

.register-link a {
  color: #3d7ab8;
  text-decoration: none;
  font-weight: 500;
}

.register-link a:hover {
  text-decoration: underline;
}
</style>