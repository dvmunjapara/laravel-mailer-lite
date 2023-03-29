<script>
import {useUserStore} from "@/stores/users";
import {ref} from "vue";
import {useRouter} from "vue-router/dist/vue-router";

export default {
  setup() {
    const userStore = useUserStore();
    const showTokenField = ref(false)
    const router = useRouter();

    const email = ref('');
    const token = ref('');

    const setEmail = async function () {

      await userStore.getToken(this.email)

      if (userStore.hasToken) {
        await router.push('home')
      } else {
        showTokenField.value = true
      }

    }
    const setToken = async function () {

      await userStore.setToken({
        email: email.value,
        token: token.value
      })

      await router.push('home')
    }

    return {
      userStore,
      email,
      token,
      showTokenField,
      setEmail,
      setToken
    }
  },
  data: () => ({
    apiResponse: null
  }),
  methods: {}
}
</script>

<template>
  <div class="container mx-auto">
    <div class="flex justify-center">

      <div
        class="
          absolute
          inset-0
          flex
          items-center
          justify-center
          bg-gray-700 bg-opacity-50
        "
      >
        <div class="max-w-2xl w-1/3 p-6 mx-4 bg-white rounded-md shadow-xl">
          <div class="flex items-center justify-between">
            <h3 class="text-2xl"></h3>
          </div>
          <div class="mt-4">

            <div class="mb-6">
              <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email
                address</label>
              <input type="email" v-model="email" id="email"
                     class="border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                     placeholder="john.doe@company.com" required>
            </div>

            <div class="mb-6" v-if="showTokenField">
              <label for="token" class="block mb-2 text-sm font-medium text-gray-900">Token</label>
              <input type="tel" v-model="token" id="token"
                     class="border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" required>
            </div>

            <div v-if="userStore.error"
                 class="bg-red-100 border border-red-400 text-red-700 mb-2 px-4 py-3 rounded relative"
                 role="alert">
              <span class="block sm:inline">{{ userStore.error }}</span>
            </div>

            <button :disabled="userStore.loading" v-if="showTokenField"
                    class="px-6 py-2 ml-2 text-white bg-green-400 rounded" @click="setToken">
              <svg v-if="userStore.loading" aria-hidden="true" role="status"
                   class="inline mr-3 w-4 h-4 text-white animate-spin" viewBox="0 0 100 101" fill="none"
                   xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                  fill="#E5E7EB"></path>
                <path
                  d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                  fill="currentColor"></path>
              </svg>
              Save Token
            </button>

            <button v-else :disabled="userStore.loading" class="px-6 py-2 ml-2 text-white bg-green-400 rounded"
                    @click="setEmail">
              <svg v-if="userStore.loading" aria-hidden="true" role="status"
                   class="inline mr-3 w-4 h-4 text-white animate-spin" viewBox="0 0 100 101" fill="none"
                   xmlns="http://www.w3.org/2000/svg">
                <path
                  d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                  fill="#E5E7EB"></path>
                <path
                  d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                  fill="currentColor"></path>
              </svg>
              Save
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
