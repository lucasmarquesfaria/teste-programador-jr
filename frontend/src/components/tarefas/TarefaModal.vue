<script setup>
import { ref, reactive, computed, watch } from 'vue'

const props = defineProps({
  tarefa: {
    type: Object,
    required: true
  },
  modoEdicao: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['salvar', 'fechar'])

const formTarefa = reactive({
  id: props.tarefa.id,
  titulo: props.tarefa.titulo,
  descricao: props.tarefa.descricao,
  status: props.tarefa.status,
  data_criacao: props.tarefa.data_criacao
})

watch(() => props.tarefa, (novaTarefa) => {
  if (novaTarefa) {
    Object.assign(formTarefa, {
      id: novaTarefa.id,
      titulo: novaTarefa.titulo,
      descricao: novaTarefa.descricao,
      status: novaTarefa.status,
      data_criacao: novaTarefa.data_criacao
    })
  }
}, { immediate: true })

const formTocado = reactive({
  titulo: false,
  descricao: false
})

const validacoes = {
  titulo: (valor) => {
    if (!valor) return 'O título é obrigatório'
    if (valor.length < 3) return 'O título deve ter pelo menos 3 caracteres'
    return ''
  },
  descricao: (valor) => {
    if (!valor) return 'A descrição é obrigatória'
    return ''
  }
}

const marcarTocado = (campo) => {
  formTocado[campo] = true
}

const validarCampo = (campo) => {
  if (!formTocado[campo]) return ''
  return validacoes[campo](formTarefa[campo])
}

const formularioValido = computed(() => {
  return !validarCampo('titulo') && !validarCampo('descricao')
})

const salvarTarefa = () => {
  Object.keys(formTocado).forEach(campo => formTocado[campo] = true)
  
  if (!formularioValido.value) return
  
  emit('salvar', { ...formTarefa })
}

const fecharModal = () => {
  emit('fechar')
}
</script>

<template>
  <div>
    <div class="modal-backdrop" @click="fecharModal"></div>
    
    <div class="modal">
      <div class="modal-panel">
        <div class="modal-header">
          <h2>
            <i class="fa-solid fa-save" :class="modoEdicao"></i>
            {{ modoEdicao ? 'Editar Tarefa' : 'Nova Tarefa' }}
          </h2>
          <button class="close-button" @click="fecharModal">×</button>
        </div>
        
        <div class="modal-body">
          <form @submit.prevent="salvarTarefa">
            <div class="form-group-row">
              <div class="form-group title-field">
                <label for="titulo">Título</label>
                <input 
                  type="text" 
                  id="titulo" 
                  v-model="formTarefa.titulo"
                  @blur="marcarTocado('titulo')"
                  :class="{ 'erro': validarCampo('titulo') }"
                  placeholder="Digite o título da tarefa"
                  required
                >
                <span class="erro-mensagem" v-if="validarCampo('titulo')">
                  {{ validarCampo('titulo') }}
                </span>
              </div>
              
              <div class="form-group status-field">
                <label for="status">Status</label>
                <select id="status" v-model="formTarefa.status">
                  <option value="pendente">Pendente</option>
                  <option value="concluída">Concluída</option>
                </select>
              </div>
            </div>
            
            <div class="form-group">
              <label for="descricao">Descrição</label>
              <textarea 
                id="descricao" 
                v-model="formTarefa.descricao"
                @blur="marcarTocado('descricao')"
                :class="{ 'erro': validarCampo('descricao') }"
                placeholder="Digite uma descrição detalhada para a tarefa"
                rows="4"
                required
              ></textarea>
              <span class="erro-mensagem" v-if="validarCampo('descricao')">
                {{ validarCampo('descricao') }}
              </span>
            </div>
            
            <div class="modal-footer">
              <button type="submit" class="save-button">
               Salvar
              </button>
              <button type="button" class="cancel-button" @click="fecharModal">
               Cancelar
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 1000;
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1001;
}

.modal-panel {
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.15);
  width: 90%;
  max-width: 550px;
  max-height: 90vh;
  overflow-y: auto;
  animation: fadeIn 0.2s ease-out;
}

.modal-header {
  padding: 16px 20px;
  border-bottom: 1px solid #e9ecef;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h2 {
  margin: 0;
  font-size: 20px;
  font-weight: 500;
  color: #333;
  display: flex;
  align-items: center;
  gap: 10px;
}

.close-button {
  background: none;
  border: none;
  font-size: 28px;
  line-height: 1;
  color: #999;
  cursor: pointer;
  padding: 0;
  margin: 0;
}

.close-button:hover {
  color: #333;
}

.modal-body {
  padding: 20px;
}

.form-group {
  margin-bottom: 16px;
}

.form-group-row {
  display: flex;
  gap: 16px;
  margin-bottom: 16px;
}

.title-field {
  flex: 3;
}

.status-field {
  flex: 1;
}

label {
  display: block;
  margin-bottom: 6px;
  font-weight: 500;
  color: #333;
  font-size: 14px;
}

input[type="text"],
select,
textarea {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #ced4da;
  border-radius: 4px;
  font-size: 14px;
  color: #333;
  background-color: #fff;
  transition: border-color 0.2s;
}

input[type="text"]:focus,
select:focus,
textarea:focus {
  border-color: #4a6fa5;
  outline: none;
}

input.erro,
textarea.erro {
  border-color: #dc3545;
}

.erro-mensagem {
  display: block;
  color: #dc3545;
  font-size: 12px;
  margin-top: 4px;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 24px;
}

.cancel-button {
  padding: 10px 16px;
  background-color: #f8f9fa;
  border: 1px solid #ced4da;
  border-radius: 4px;
  color: #574c49;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.cancel-button:hover {
  background-color: #e9ecef;
}

.save-button {
  padding: 10px 16px;
  background-color: #3d7ab8;
  border: none;
  border-radius: 4px;
  color: white;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
}

.save-button:hover {
  background-color: #2c5987;
}

.save-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@media (max-width: 600px) {
  .form-group-row {
    flex-direction: column;
    gap: 16px;
  }
  
  .title-field, 
  .status-field {
    flex: none;
  }
}
</style>