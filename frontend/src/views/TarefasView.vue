<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import TarefaList from '../components/tarefas/TarefaList.vue'
import * as tarefaService from '../services/tarefaService'
import * as authService from '../services/authService'

const router = useRouter()
const usuario = ref(authService.getUsuarioAtual())
const tarefas = ref([])
const carregando = ref(true)
const mensagem = ref('')
const tipoMensagem = ref('sucesso')

onMounted(async () => {
  await authService.verificarToken()
  carregarTarefas()
})

async function carregarTarefas() {
  carregando.value = true;
  mensagem.value = '';

  try {
    const response = await tarefaService.listarTarefas();
    tarefas.value = response;
  } catch (error) {
    exibirMensagem('Não foi possível carregar as tarefas. Tente novamente mais tarde.', 'erro');
  } finally {
    carregando.value = false;
  }
}

async function adicionarTarefa(tarefa) {
  try {
    const novaTarefa = await tarefaService.criarTarefa(tarefa.titulo, tarefa.descricao);
    tarefas.value.push(novaTarefa);
    exibirMensagem('Tarefa adicionada com sucesso!');
  } catch (error) {
    exibirMensagem('Erro ao adicionar tarefa. Tente novamente.', 'erro');
  }
}

async function editarTarefa(tarefa) {
  try {
    const tarefaAtualizada = await tarefaService.atualizarTarefa(tarefa.id, tarefa.titulo, tarefa.descricao, tarefa.status);
    const index = tarefas.value.findIndex(t => t.id === tarefa.id);
    if (index !== -1) tarefas.value[index] = tarefaAtualizada;
    exibirMensagem('Tarefa atualizada com sucesso!');
  } catch (error) {
    exibirMensagem('Erro ao atualizar tarefa. Tente novamente.', 'erro');
  }
}

async function excluirTarefa(id) {
  try {
    await tarefaService.excluirTarefa(id);
    tarefas.value = tarefas.value.filter(t => t.id !== id);
    exibirMensagem('Tarefa excluída com sucesso!');
  } catch (error) {
    exibirMensagem('Erro ao excluir tarefa. Tente novamente.', 'erro');
  }
}

async function excluirTarefasSelecionadas(ids) {
  try {
    await tarefaService.excluirTarefas(ids)
    await carregarTarefas()
    exibirMensagem(`${ids.length} tarefa(s) excluída(s) com sucesso!`)
  } catch (error) {
    console.error('Erro ao excluir tarefas:', error)
    exibirMensagem('Não foi possível excluir as tarefas selecionadas. Tente novamente.', 'erro')
  }
}

function exibirMensagem(texto, tipo = 'sucesso') {
  mensagem.value = texto
  tipoMensagem.value = tipo
  setTimeout(() => { mensagem.value = '' }, 3000)
}

async function fazerLogout() {
  await authService.logout()
  router.push('/login')
}
</script>

<template>
  <div class="tarefas-view">
    <div class="header-container">
      <div class="user-info">
        <span v-if="usuario">Olá, {{ usuario.nome }}</span>
        <button class="logout-button" @click="fazerLogout">
          <i class="fas fa-sign-out-alt"></i> Sair
        </button>
      </div>
    </div>
    
    <!-- Notificação de mensagem -->
    <div v-if="mensagem" 
         class="mensagem-notificacao"
         :class="{'mensagem-sucesso': tipoMensagem === 'sucesso', 'mensagem-erro': tipoMensagem === 'erro'}">
      {{ mensagem }}
    </div>
    
    <!-- Indicador de carregamento -->
    <div v-if="carregando" class="loading-container">
      <div class="loading-spinner"></div>
      <p>Carregando tarefas...</p>
    </div>
    
    <!-- Lista de tarefas -->
    <TarefaList 
      v-else
      :tarefas="tarefas"
      @adicionar="adicionarTarefa"
      @editar="editarTarefa"
      @excluir="excluirTarefa"
      @excluirSelecionadas="excluirTarefasSelecionadas"
    />
  </div>
</template>

<style scoped>
.tarefas-view {
  max-width: 1400px;
  margin: 0 auto;
  padding: 20px;
  position: relative;
}

.header-container {
  display: flex;
  justify-content: flex-end;
  margin-bottom: 20px;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 15px;
  background-color: white;
  padding: 10px 15px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.user-info span {
  color: #333;
  font-weight: 500;
}

.logout-button {
  background-color: #dc3545;
  color: white;
  padding: 8px 12px;
  font-size: 13px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 5px;
  transition: background-color 0.2s;
}

.logout-button:hover {
  background-color: #bd2130;
}

/* Estilo para mensagem de notificação */
.mensagem-notificacao {
  position: fixed;
  top: 20px;
  right: 20px;
  padding: 12px 20px;
  border-radius: 4px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  z-index: 1000;
  animation: fadeIn 0.3s, fadeOut 0.3s 2.7s;
  max-width: 400px;
}

.mensagem-sucesso {
  background-color: #28a745;
  color: white;
}

.mensagem-erro {
  background-color: #dc3545;
  color: white;
}

/* Estilo para o spinner de carregamento */
.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 0;
  color: #6c757d;
}

.loading-spinner {
  border: 4px solid rgba(0, 0, 0, 0.1);
  border-radius: 50%;
  border-top: 4px solid #3d7ab8;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
  margin-bottom: 10px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeOut {
  from { opacity: 1; transform: translateY(0); }
  to { opacity: 0; transform: translateY(-20px); }
}
</style>