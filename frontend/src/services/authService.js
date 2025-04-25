import { ref } from 'vue'
import axios from 'axios'

const API_URL = 'http://localhost:8000/api'

const api = axios.create({
  baseURL: API_URL,
  timeout: 10000,
  withCredentials: false
});

// Intercepta requisições para incluir o token JWT
api.interceptors.request.use((config) => {
  const usuario = carregarUsuario();
  if (usuario && usuario.token) {
    config.headers.Authorization = `Bearer ${usuario.token}`;
  }
  return config;
});

const usuarioAtual = ref(carregarUsuario())

function carregarUsuario() {
  const dadosUsuario = localStorage.getItem('usuarioLogado')
  return dadosUsuario ? JSON.parse(dadosUsuario) : null
}

export function estaAutenticado() {
  return !!usuarioAtual.value
}

export async function login(email, senha) {
  try {
    const response = await api.post('/login', { email, password: senha });
    console.log('Resposta do backend:', response.data);

    const user = response.data.user || {};
    const usuario = {
      id: user.id,
      nome: user.name || user.nome || '',
      email: user.email || '',
      token: response.data.token || ''
    };

    if (!usuario.token) {
      throw new Error('Token não recebido do backend.');
    }

    localStorage.setItem('usuarioLogado', JSON.stringify(usuario));
    usuarioAtual.value = usuario;

    return usuario;
  } catch (error) {
    if (error.response && error.response.data && error.response.data.error) {
      throw new Error(error.response.data.error);
    }
    throw error;
  }
}

export async function logout() {
  try {
    await api.post('/logout');
  } finally {
    localStorage.removeItem('usuarioLogado')
    usuarioAtual.value = null
  }
}

export function getUsuarioAtual() {
  return usuarioAtual.value
}

export async function verificarToken() {
  if (!usuarioAtual.value || !usuarioAtual.value.token) return null
  
  try {
    const response = await api.get('/auth/verificar', {
      headers: {
        Authorization: `Bearer ${usuarioAtual.value.token}`
      }
    })
    
    // Se o token for válido, retorna o usuário
    return usuarioAtual.value
  } catch (error) {
    // Se o token for inválido, faz logout
    if (error.response && error.response.status === 401) {
      await logout()
    }
    return null
  }
}

export default {
  estaAutenticado,
  login,
  logout,
  getUsuarioAtual,
  verificarToken
}