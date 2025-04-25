import axios from 'axios';

const API_URL = 'http://localhost:8000/api';

const api = axios.create({
  baseURL: API_URL,
  timeout: 10000,
});

// Intercepta requisições para incluir o token JWT
api.interceptors.request.use((config) => {
  const usuario = JSON.parse(localStorage.getItem('usuarioLogado'));
  if (usuario && usuario.token) {
    config.headers.Authorization = `Bearer ${usuario.token}`;
  }
  return config;
});

export async function listarTarefas() {
  const response = await api.get('/tarefas');
  return response.data;
}

export async function criarTarefa(titulo, descricao) {
  const response = await api.post('/tarefas', { titulo, descricao });
  return response.data;
}

export async function atualizarTarefa(id, titulo, descricao, status) {
  const response = await api.put(`/tarefas/${id}`, { titulo, descricao, status });
  return response.data;
}

export async function excluirTarefa(id) {
  await api.delete(`/tarefas/${id}`);
}

export default {
  listarTarefas,
  criarTarefa,
  atualizarTarefa,
  excluirTarefa,
};