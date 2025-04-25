import { createRouter, createWebHistory } from 'vue-router'
import TarefasView from './views/TarefasView.vue'
import LoginView from './views/LoginView.vue'
import RegisterView from './views/RegisterView.vue'
import { estaAutenticado } from './services/authService'

const routes = [
  { 
    path: '/login', 
    component: LoginView 
  },
  {
    path: '/register',
    component: RegisterView
  },
  { 
    path: '/tarefas', 
    component: TarefasView,
    meta: { requiresAuth: true } 
  },
  { 
    path: '/', 
    redirect: '/tarefas' 
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Proteção de rotas - redireciona para login se não estiver autenticado
router.beforeEach((to, from, next) => {
  const autenticado = estaAutenticado()
  
  // Se a rota requer autenticação e o usuário não está autenticado, redireciona para login
  if (to.meta.requiresAuth && !autenticado) {
    next('/login')
  } 
  // Se o usuário está tentando acessar o login, mas já está autenticado, redireciona para tarefas
  else if (to.path === '/login' && autenticado) {
    next('/tarefas')
  }
  // Caso contrário, prossegue normalmente
  else {
    next()
  }
})

export default router