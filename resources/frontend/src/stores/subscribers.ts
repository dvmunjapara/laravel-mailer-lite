import { defineStore } from "pinia";
import axios from 'axios'
import type {Subscriber} from "@/interfaces/subscriber";
import {useUserStore} from "@/stores/users";
import type {TableMeta} from "@/interfaces/TableMeta";

const base_url = import.meta.env.VITE_API_URL;

export const useSubscriberStore = defineStore({
  id: 'subscribers',
  state: () => ({
    subscribers: [] as Subscriber[],
    meta: {
      current_page: 1,
      total: 0,
      per_page: 10
    } as TableMeta,
    loading: true,
    error: '' as String,
    message: '' as String,
  }),

  actions: {
    loadSubscribers(data?:any) {

      this.loading = true;

      const userStore = useUserStore();
      const tokenEmail = userStore.user.email;

      return new Promise((resolve, reject) => {
        axios
          .get(`${base_url}/subscribers`, {
            params: data,
            headers: {
              email: tokenEmail
            }
          })
          .then((response) => {
            this.subscribers = response.data.data
            this.meta = response.data.meta
            this.loading = false
            resolve(response)
          })
          .catch((err) => {
            this.loading = false
            reject(err)
          })
      })
    },
    updateSubscriber(data: Subscriber) {

      this.loading = true;

      const userStore = useUserStore();
      const tokenEmail = userStore.user.email;

      return new Promise((resolve, reject) => {
        axios
          .post(`${base_url}/subscribers`, data, {
            headers: {
              email: tokenEmail
            }
          })
          .then((response) => {
            resolve(response)
            this.loading = false
            this.message = response.data.message
            this.error = ''
          })
          .catch((err) => {
            this.error = err.response.data.message
            this.loading = false
            reject(err)
          })
      })
    },
    reset() {

      this.error = ''
      this.message = ''
    }
  }
});
