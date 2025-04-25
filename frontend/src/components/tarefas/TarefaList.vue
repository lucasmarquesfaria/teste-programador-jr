<script setup>
import { ref } from 'vue'
import TarefaModal from './TarefaModal.vue'

/**
 * INTEGRAÇÃO COM BACKEND LARAVEL
 * ------------------------------
 * Para adaptar este componente ao backend Laravel:
 * 
 * 1. Verifique se os nomes dos campos das tarefas correspondem ao formato retornado pela API Laravel
 *    - Pode ser necessário adaptar: data_criacao → created_at, data_atualizacao → updated_at
 *    - O formato de status pode precisar ser adaptado: pendente/concluída → pending/completed 
 * 2. Se a API Laravel retornar dados paginados, você pode precisar implementar a navegação entre páginas
 * 3. Ao exibir ou manipular datas, lembre-se que o Laravel retorna datas no formato ISO
 */

const props = defineProps({
  tarefas: {
    type: Array,
    required: true
  }
})

const emit = defineEmits(['adicionar', 'editar', 'excluir', 'excluirSelecionadas'])

const tarefasSelecionadas = ref([])
const mostrarModal = ref(false)
const tarefaAtual = ref(null)
const modoEdicao = ref(false)

/**
 * Formata uma data para exibição
 * 
 * INTEGRAÇÃO LARAVEL:
 * As datas no Laravel geralmente vêm no formato ISO (ex: "2023-04-15T14:30:45.000000Z")
 * Você pode precisar adaptar esta função para lidar com o formato de data do Laravel
 */
const formatarData = (dataString) => {
  if (!dataString) return '-';
  
  try {
    // Criar um objeto Date a partir da string ISO do Laravel
    const data = new Date(dataString);
    
    if (isNaN(data.getTime())) return '-';
    
    // Formatar data e hora no padrão brasileiro
    const opcoesData = { 
      day: '2-digit', 
      month: '2-digit', 
      year: 'numeric',
    };
    
    const opcoesHora = {
      hour: '2-digit',
      minute: '2-digit',
      hour12: false
    };
    
    // Formatar como DD/MM/YYYY às HH:MM
    return `${data.toLocaleDateString('pt-BR', opcoesData)} às ${data.toLocaleTimeString('pt-BR', opcoesHora)}`;
  } catch (e) {
    console.error('Erro ao formatar data:', e);
    return '-';
  }
}

const toggleSelecionarTarefa = (id) => {
  const index = tarefasSelecionadas.value.indexOf(id)
  if (index === -1) {
    tarefasSelecionadas.value.push(id)
  } else {
    tarefasSelecionadas.value.splice(index, 1)
  }
}

const temTarefaSelecionada = () => {
  return tarefasSelecionadas.value.length > 0
}

// Prepara os dados para criar uma nova tarefa
const abrirModalAdicionar = () => {
  tarefaAtual.value = {
    id: null,
    titulo: '',
    descricao: '',
    status: 'pendente'
    // INTEGRAÇÃO LARAVEL: Você pode precisar ajustar o valor padrão 
    // para o formato que o Laravel espera (ex: 'pending')
  }
  modoEdicao.value = false
  mostrarModal.value = true
}

// Prepara os dados para editar uma tarefa existente
const abrirModalEditar = (tarefa) => {
  // INTEGRAÇÃO LARAVEL: Você pode precisar mapear os campos do Laravel
  // para o formato esperado pelo seu frontend
  tarefaAtual.value = { ...tarefa }
  modoEdicao.value = true
  mostrarModal.value = true
}

const salvarTarefa = (tarefa) => {
  if (modoEdicao.value) {
    emit('editar', tarefa)
  } else {
    emit('adicionar', tarefa)
  }
  mostrarModal.value = false
}

const excluirTarefa = (id) => {
  if (confirm('Tem certeza que deseja excluir esta tarefa?')) {
    emit('excluir', id)
    const index = tarefasSelecionadas.value.indexOf(id)
    if (index !== -1) {
      tarefasSelecionadas.value.splice(index, 1)
    }
  }
}

const excluirTarefasSelecionadas = () => {
  if (tarefasSelecionadas.value.length === 0) return
  
  if (confirm(`Tem certeza que deseja excluir ${tarefasSelecionadas.value.length} tarefa(s)?`)) {
    emit('excluirSelecionadas', tarefasSelecionadas.value)
    tarefasSelecionadas.value = []
  }
}

const fecharModal = () => {
  mostrarModal.value = false
}

const toggleSelecionarTodas = (event) => {
  if (event.target.checked) {
    tarefasSelecionadas.value = props.tarefas.map(t => t.id)
  } else {
    tarefasSelecionadas.value = []
  }
}
</script>

<template>
  <div class="tarefa-lista">
    <header>
      <h1>Gerenciador de Tarefas</h1>
    </header>
    
    <div class="controls">
      <div class="left-controls">
        <button class="add-button" @click="abrirModalAdicionar">
          <i class="fa-solid fa-plus"></i> Nova Tarefa
        </button>
        <button 
          class="delete-button" 
          :disabled="!temTarefaSelecionada()" 
          @click="excluirTarefasSelecionadas">
          <i class="fa-solid fa-trash-alt"></i> Excluir Selecionadas
        </button>
      </div>
      <div class="selection-count" v-if="tarefasSelecionadas.length > 0">
        {{ tarefasSelecionadas.length }} {{ tarefasSelecionadas.length === 1 ? 'tarefa selecionada' : 'tarefas selecionadas' }}
      </div>
    </div>

    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th class="check-column">
              <input 
                type="checkbox" 
                @change="toggleSelecionarTodas">
            </th>
            <th class="title-column">Título</th>
            <th class="desc-column">Descrição</th>
            <th class="status-column">Status</th>
            <th class="date-column">Criado</th>
            <th class="date-column">Atualizado</th>
            <th class="actions-column">Ações</th>
          </tr>
        </thead>
        <tbody>
          <!-- 
            INTEGRAÇÃO LARAVEL:
            1. Verifique se os campos correspondem ao modelo retornado pelo Laravel
            2. Você pode precisar adaptar as propriedades:
               - titulo → title
               - descricao → description
               - status → status (pode precisar de conversão de valor)
               - data_criacao → created_at
               - data_atualizacao → updated_at
          -->
          <tr v-for="tarefa in tarefas" :key="tarefa.id" :class="{ 'completed': tarefa.status === 'concluída' }">
            <td class="check-column">
              <input 
                type="checkbox" 
                :checked="tarefasSelecionadas.includes(tarefa.id)"
                @change="toggleSelecionarTarefa(tarefa.id)">
            </td>
            <td class="title-column">{{ tarefa.titulo }}</td>
            <td class="desc-column">{{ tarefa.descricao }}</td>
            <td class="status-column">
              <span :class="['status-indicator', tarefa.status === 'concluída' ? 'status-done' : 'status-pending']">
                {{ tarefa.status }}
              </span>
            </td>
            <td class="date-column">{{ formatarData(tarefa.created_at) }}</td>
            <td class="date-column">{{ formatarData(tarefa.updated_at) }}</td>
            <td class="actions-column">
              <div class="action-buttons">
                <button class="edit-button" @click="abrirModalEditar(tarefa)" title="Editar">
                  <i class="fa-solid fa-edit"></i>
                </button>
                <button class="delete-button" @click="excluirTarefa(tarefa.id)" title="Excluir">
                  <!-- Ícone de lixeira com fallback para X -->
                  <span class="delete-icon">
                    <i class="fa-solid fa-trash-alt"></i>
                    <span class="delete-x">✕</span>
                  </span>
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="tarefas.length === 0">
            <td colspan="7" class="no-data">Nenhuma tarefa encontrada</td>
          </tr>
        </tbody>
      </table>
    </div>

    <TarefaModal 
      v-if="mostrarModal"
      :tarefa="tarefaAtual"
      :modo-edicao="modoEdicao"
      @salvar="salvarTarefa"
      @fechar="fecharModal"
    />
  </div>
</template>

<style scoped>
.tarefa-lista {
  width: 100%;
}

header {
  margin-bottom: 20px;
  text-align: center;
}

h1 {
  color: #333;
  font-size: 28px;
  font-weight: 500;
}

.controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.left-controls {
  display: flex;
  gap: 8px;
}

.selection-count {
  background-color: #4a6fa5;
  color: white;
  padding: 8px 12px;
  border-radius: 4px;
  font-weight: 500;
  font-size: 14px;
}

.table-wrapper {
  background-color: white;
  border-radius: 6px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  overflow: hidden;
}

table {
  width: 100%;
  border-collapse: collapse;
  color: #333;
}

th {
  background-color: #f8f9fa;
  border-bottom: 2px solid #e9ecef;
  color: #495057;
  text-align: left;
  padding: 12px;
  font-weight: 500;
  font-size: 14px;
}

td {
  padding: 12px;
  border-bottom: 1px solid #e9ecef;
  vertical-align: middle;
  color: #333;
  font-size: 14px;
}

.check-column {
  width: 40px;
  text-align: center;
}

.title-column {
  width: 20%;
  font-weight: 500;
}

.desc-column {
  width: 30%;
  color: #6c757d;
}

.status-column {
  width: 10%;
}

.date-column {
  width: 15%;
  color: #6c757d;
  font-size: 13px;
}

.actions-column {
  width: 10%;
  text-align: center;
}

tr.completed {
  background-color: #f8f9fa;
}

tr.completed td {
  color: #6c757d;
}

tr:hover {
  background-color: #f8f9fa;
}

.status-indicator {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 4px;
  font-weight: 500;
  font-size: 12px;
}

.status-done {
  background-color: #e9f7ef;
  color: #28a745;
  border: 1px solid #d4edda;
}

.status-pending {
  background-color: #fff8e6;
  color: #ffc107;
  border: 1px solid #ffeeba;
}

.action-buttons {
  display: flex;
  justify-content: center;
  gap: 6px;
}

.no-data {
  text-align: center;
  padding: 24px 0;
  font-size: 15px;
  color: #6c757d;
}

button {
  cursor: pointer;
  border: none;
  border-radius: 4px;
  padding: 8px 12px;
  font-size: 14px;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: all 0.2s;
}

button i {
  font-size: 14px;
}

.add-button {
  background-color: #3d7ab8;
  color: white;
}

.add-button:hover {
  background-color: #2c5987;
}

.delete-button {
  background-color: #dc3545;
  color: white;
}

.delete-button:hover {
  background-color: #bd2130;
}

.delete-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.edit-button {
  background-color: #3d7ab8;
  color: white;
  padding: 6px 8px;
}

.edit-button:hover {
  background-color: #2c5987;
}

/* Estilo para o fallback do ícone de lixeira */
.delete-icon {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.delete-x {
  display: none;
  font-size: 14px;
  font-weight: bold;
}

.fas.fa-trash-alt:not(:before) + .delete-x {
  display: inline;
}

@media (max-width: 992px) {
  table {
    min-width: 900px;
  }
  
  .table-wrapper {
    overflow-x: auto;
  }
}
</style>