<script setup>
// O componente principal que renderiza as rotas
</script>

<template>
  <div class="app-wrapper">
    <router-view></router-view>
  </div>
</template>

<style>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
  background-color: #f5f7fa;
  line-height: 1.5;
}

.app-wrapper {
  background-color: #f5f7fa;
  background-image: linear-gradient(135deg, #f5f7fa 0%, #e4efe9 100%);
  width: 100%;
  min-height: 100vh;
  padding: 30px;
}

button {
  cursor: pointer;
  border: none;
  border-radius: 4px;
  padding: 10px 15px;
}
</style>
