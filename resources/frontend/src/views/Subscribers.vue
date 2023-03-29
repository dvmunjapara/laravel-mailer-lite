<script>
import {useRoute} from 'vue-router';
import {useSubscriberStore} from "@/stores/subscribers";
import {reactive, ref, watch} from "vue";
import Vue3TableLite from "vue3-table-lite";
import debounce from 'lodash.debounce'
import moment from 'moment'
import { toast } from 'vue3-toastify';


export default {

  components: {TableLite: Vue3TableLite},

  setup() {
    const subscriberStore = useSubscriberStore();
    const route = useRoute();
    let query = ref('');
    let showEmailModal = ref(false );

    const filter = reactive({
      page: 1,
      per_page: 10,
      search: ''
    });

    let subscriber = ref({
      id: undefined,
      name: '',
      email: '',
      country: ''
    })

    watch(filter, () => {
      loadData()
    })

    const table = ref({
      columns: [
        {
          label: "Name",
          field: "name",
        },
        {
          label: "Email",
          field: "email",
        },
        {
          label: "Country",
          field: "country",
        },
        {
          label: "Subscribed date",
          field: "date",
        },
        {
          label: "Subscribed time",
          field: "time",
        },
        {
          label: "Edit",
          field: "edit",
        },
      ]
    })

    const formatDate = function (date) {

      return moment(date).format('D/M/YYYY')
    }

    const formatTime = function (date) {

      return moment(date).format('hh:mm')
    }

    const doPagination = function (offset, limit) {
      filter.page = parseInt((offset / limit)) + 1
      filter.per_page = limit
    }


    const doSearch = debounce(() => {
      filter.search = query.value
    }, 500)


    const loadData = async function () {

      await subscriberStore.loadSubscribers(filter);
    }

    const editSubscriber = async function(sub) {

      showEmailModal.value = true
      subscriber.value = {...sub.value}
    }

    const updateSubscriber = async function() {

      await subscriberStore.updateSubscriber(subscriber.value)

      if (!subscriberStore.error) {
        showEmailModal.value = false;
        toast.success(subscriberStore.message);
        loadData()
      }
    }

    const cancelUpdateSubscriber = async function() {

      subscriber.value = {
        id: undefined,
        name: '',
        email: '',
        country: '',
        mode: 'create'
      }

      subscriberStore.reset();
      showEmailModal.value = false
    }

    return {
      subscriberStore,
      route,
      table,
      filter,
      doSearch,
      query,
      subscriber,
      showEmailModal,
      loadData,
      doPagination,
      formatDate,
      formatTime,
      updateSubscriber,
      editSubscriber,
      cancelUpdateSubscriber
    }
  },
  async mounted() {
    await this.loadData()
  }
}
</script>


<template>
  <div class="flex flex-col">
    <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
      <div class="flex">
        <div class="flex-1">
          <label for="search" class="block mb-2 text-sm font-medium text-gray-900">Search</label>
          <input type="text" v-model="query" @input="doSearch" id="search"
                 class="border border-gray-300 text-gray-900 text-sm rounded-lg block w-1/4 mb-2 p-2.5" required>
        </div>
        <div>
          <button class="px-6 py-2 ml-2 text-white bg-green-400 rounded" @click="showEmailModal = true">
            Add subscriber
          </button>
        </div>
      </div>
      <table-lite
        :is-slot-mode="true"
        :is-loading="subscriberStore.loading"
        :columns="table.columns"
        :rows="subscriberStore.subscribers"
        :total="subscriberStore.meta.total"
        @do-search="doPagination"
        @is-finished="table.isLoading = false"
      >
        <template v-slot:date="data">
          {{ formatDate(data.value.subscribed_at) }}
        </template>
        <template v-slot:time="data">
          {{ formatTime(data.value.subscribed_at) }}
        </template>
        <template v-slot:edit="data">
          <button class="px-3 py-3  text-white bg-green-400 rounded" @click="editSubscriber(data)">
            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
              <path
                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
            </svg>
          </button>
        </template>
      </table-lite>
    </div>

    <!--    Add/Edit modal -->

    <div
      v-show="showEmailModal"
      class="
          absolute
          inset-0
          flex
          items-center
          justify-center
          bg-gray-700 bg-opacity-50
          z-10
        "
    >
      <div class="max-w-2xl w-1/3 p-6 mx-4 bg-white rounded-md shadow-xl">
        <div class="flex items-center justify-between">
          <h3 class="text-2xl">{{subscriber.id ? "Edit" : "Add"}} Subscriber</h3>
        </div>
        <div class="mt-4">


          <div class="mb-6">
            <label for="email"  class="block mb-2 text-sm font-medium text-gray-900">Email
              address</label>
            <input type="email" :disabled="subscriber.id" v-model="subscriber.email" id="email"
                   class="border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                   placeholder="john.doe@company.com" required>
          </div>

          <div class="mb-6">
            <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Name</label>
            <input type="text" v-model="subscriber.name" id="name"
                   class="border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                   placeholder="John Doe" required>
          </div>

          <div class="mb-6">
            <label for="country" class="block mb-2 text-sm font-medium text-gray-900">Country</label>
            <input type="text" v-model="subscriber.country" id="country"
                   class="border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"
                   placeholder="USA" required>
          </div>


          <div v-if="subscriberStore.error" class="bg-red-100 border border-red-400 text-red-700 mb-2 px-4 py-3 rounded relative"
               role="alert">
            <span class="block sm:inline">{{subscriberStore.error}}</span>
          </div>

          <button :disabled="subscriberStore.loading" class="px-6 py-2 ml-2 text-white bg-white text-black border border-green-400 rounded" @click="cancelUpdateSubscriber">
            Cancel
          </button>

          <button :disabled="subscriberStore.loading" class="px-6 py-2 ml-2 text-white bg-green-400 rounded" @click="updateSubscriber">
            <svg v-if="subscriberStore.loading" aria-hidden="true" role="status" class="inline mr-3 w-4 h-4 text-white animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB"></path>
              <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor"></path>
            </svg>
            Save
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
::v-deep(.vtl-table .vtl-thead .vtl-thead-th) {
  color: #fff;
  background-color: #42b983;
  border-color: #42b983;
}
::v-deep(.vtl-table) {
  display: table;
}
::v-deep(.vtl-paging-count-dropdown) {
  margin: 0 1rem 0 0.3rem;
}
::v-deep(.vtl-paging-page-dropdown) {
  margin: 0 1rem 0 0.3rem;
}
</style>
