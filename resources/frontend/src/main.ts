import { createApp } from "vue";
import { createPinia } from "pinia";

import App from "./App.vue";
import router from "./router";
import piniaPluginPersistedState from "pinia-plugin-persistedstate"
import Vue3Toastify, { type ToastContainerOptions } from 'vue3-toastify';

import "./assets/main.css";
import 'vue3-toastify/dist/index.css';

const app = createApp(App);

const pinia = createPinia();


app.use(Vue3Toastify, {
  autoClose: 3000,
} as ToastContainerOptions);

pinia.use(piniaPluginPersistedState)

app.use(pinia);

app.use(router);

app.mount("#app");
