<template>
    <DeleteModal v-if="deleteModal" :isOpen="deleteModal.isOpen" :onClose="() => deleteModal.isOpen = false"
        :onAccept="deleteData" />
    <AddNewModal />

    <v-container class="max-width">
        <v-table>
            <thead>
                <tr>
                    <th class="text-left">
                        Nama barang
                    </th>
                    <th class="text-left">
                        Stok
                    </th>
                    <th class="text-left">
                        Jumlah terjual
                    </th>
                    <th class="text-left">
                        Tanggal transaksi
                    </th>
                    <th class="text-left">
                        Jenis barang
                    </th>
                    <th class="text-left">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in data" :key="item.name">
                    <td>{{ item.name }}</td>
                    <td>{{ item.stock }}</td>
                    <td>{{ item.qty }}</td>
                    <td>{{ dateFormatter(item.created_at) }}</td>
                    <td>{{ item.category_name }}</td>
                    <td>
                        <v-btn variant="text" icon="mdi-pencil-outline"></v-btn>
                        <v-btn variant="text" @click="() => deleteModal = { id: item.id, isOpen: true }"
                            icon="mdi-delete"></v-btn>
                    </td>
                </tr>
            </tbody>
        </v-table>

        <v-pagination c v-model="queries.page" class="my-4" :length="meta.last_page" @input="next"></v-pagination>
    </v-container>
</template>

<script setup>
import {
    onMounted,
    ref,
    watch,
} from 'vue';

import AddNewModal from '../../components/Transaction/AddNewModal.vue';
import DeleteModal from '../../components/Transaction/DeleteModal.vue';
import { dateFormatter } from '../../utils/date.js';
import {
    deleteTransaction,
    getAllTransactions,
} from '../../utils/https/transaction.js';

const data = ref([]);
const meta = ref({

});

const deleteModal = ref({
    isOpen: false,
    id: null
});

const queries = ref({
    searchByName: "",
    per_page: 3,
    page: 1
})



const fetchData = async () => {
    try {
        const result = await getAllTransactions(queries.value);
        data.value = result.data.data.data;
        meta.value = result.data.data;
        delete meta.value.data;
    } catch (error) {
        console.error(error);
    }
}

const deleteData = async (id) => {
    try {
        const result = await deleteTransaction(deleteModal.value.id);
    } catch (error) {
        console.error(error);
    }
}

const next = (page) => {
    queries.value.page = page;
    fetchData();
}

onMounted(() => fetchData());


</script>
