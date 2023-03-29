import { createRouter, createWebHistory } from "vue-router";
import User from "../views/User.vue";
import Login from "../views/Login.vue";
import {useUserStore} from "@/stores/users";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/set-token",
      name: "setToken",
      component: Login,
      meta: {
        require_token: false,
      }
    },
    {
      path: "/",
      name: "home",
      component: User,
      meta: {
        require_token: true,
      }
    },
  ],
});

router.beforeEach((to, from, next) => {

  const userStore = useUserStore();

  if (to.meta.require_token && !userStore.hasToken) next({ name: 'setToken' })
  else if (!to.meta.require_token && userStore.hasToken) next({ name: 'home' })
  else next()
})

export default router;
