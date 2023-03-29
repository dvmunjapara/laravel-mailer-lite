import { defineStore } from "pinia";
import axios from 'axios'
import type {User} from "@/interfaces/user";
import type {Token} from "@/interfaces/token";

const base_url = import.meta.env.VITE_API_URL;

export const useUserStore = defineStore({
  id: 'users',
  state: () => ({
    user: {} as User,
    error: undefined as String|undefined,
    hasToken: false,
    loading: false
  }),

  actions: {
    getToken(email: string) {

      this.loading = true;

      return new Promise((resolve, reject) => {
        axios
          .get(`${base_url}/token`, {
            params: {
                email
            },
          })
          .then((response) => {
            this.user = response.data.user
            this.hasToken = response.data.user?.email?.length > 0
            this.error = undefined
            this.loading = false
            resolve(response)
          })
          .catch((err) => {
            this.error = err.response?.data?.message
            this.loading = false
            reject(err)
          })
      })
    },
    setToken(data: Token) {

      this.loading = true;

      return new Promise((resolve, reject) => {
        axios
          .post(`${base_url}/token`, data)
          .then((response) => {
            this.user = response.data.user
            this.hasToken = this.user?.email?.length > 0
            this.error = undefined
            this.loading = false;
            resolve(response)
          })
          .catch((err) => {
            this.error = err.response?.data?.message
            this.loading = false
            reject(err)
          })
      })
    }
  },
  persist: true,
});
