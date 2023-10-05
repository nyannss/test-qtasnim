<script setup>
import {
    computed,
    onMounted,
    ref,
    watch,
} from 'vue';

import { useToast } from 'vue-toastification';

import Loading from '../../components/Loading.vue';
import AddNewModal from '../../components/Transaction/AddNewModal.vue';
import DeleteModal from '../../components/Transaction/DeleteModal.vue';
import EditModal from '../../components/Transaction/EditModal.vue';
import { dateFormatter } from '../../utils/date.js';
import {
    deleteTransaction,
    getAllTransactions,
} from '../../utils/https/transaction.js';

const toast = useToast();
const data = ref([]);
const meta = ref({

});

const status = ref({
    isDeleteLoading: false,
    isDataLoading: true
});


const deleteModal = ref({
    isOpen: false,
    id: null
});

const queries = ref({
    searchByName: "",
    per_page: 10,
    page: 1,
    sort_by: "",
    sort_order: "desc",
    sortValue: ""
})

const editModal = ref({
    isOpen: false,
    data: {}
})



const fetchData = async () => {
    try {
        status.value.isDataLoading = true;
        const result = await getAllTransactions(queries.value);
        data.value = result.data.data.data;
        meta.value = result.data.data;
        delete meta.value.data;
    } catch (error) {
        if (error.response?.data?.message) {
            return toast.error(error.response.data.message);
        }
        return toast.error("Terjadi kesalahan saat mengambil data");
    } finally {
        status.value.isDataLoading = false;

    }
}

const currentPage = computed(() => queries.value.page);

const deleteData = async (id) => {
    try {
        status.value.isDeleteLoading = true;
        const result = await deleteTransaction(deleteModal.value.id);
        toast.success("Success deleting data");
        deleteModal.value.isOpen = false;
        fetchData();
    } catch (error) {
        if (error.response?.data?.message) {
            return toast.error(error.response.data.message);
        }
        return toast.error("Terjadi kesalahan saat menghapus data");
    } finally {
        status.value.isDeleteLoading = false;
    }
}

const sortOptions = [
    {
        title: "A-Z",
        value: "product_name,asc"
    },
    {
        title: "Z-A",
        value: "product_name,desc"
    },
    {
        title: "Latest",
        value: "created_at,desc"
    },
    {
        title: "Oldest",
        value: "created_at,asc"
    },
    {
        title: "Most quantity",
        value: "qty,desc"
    },
    {
        title: "Least quantity",
        value: "qty,asc"
    },

];

const filterAction = () => {
    if (queries.value.sortValue !== "") {
        const sort = queries.value.sortValue.split(",");
        queries.value.sort_by = sort[0];
        queries.value.sort_order = sort[1];
    }

    fetchData();
}
// reactivity
watch(currentPage, fetchData);
onMounted(() => fetchData());


</script>


<template>
    <DeleteModal v-if="deleteModal" :isOpen="deleteModal.isOpen" :onClose="() => deleteModal.isOpen = false"
        :onAccept="deleteData" :isLoading="status.isDeleteLoading" />
    <AddNewModal :onSuccess="() => fetchData()" />
    <EditModal :data="editModal.data" :onSave="() => { editModal.isOpen = false; fetchData(); }"
        :onClose="() => editModal.isOpen = false" v-if="editModal.isOpen" />
    <loading v-if="status.isDataLoading" />


    <v-container class="max-width" v-else>

        <v-row>
            <v-col cols="6" md="6">
                <v-text-field v-model="queries.searchByName" label="Cari" outlined />
            </v-col>
            <v-col cols="6" md="2">
                <v-select v-model="queries.sortValue" :items="sortOptions" label="Urutkan berdasarkan" outlined></v-select>
            </v-col>
            <v-col>
                <v-btn h="full" size="large" color="primary" @click="filterAction">Go</v-btn>
            </v-col>
        </v-row>


        <v-table v-if="data.length > 0">
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
                <tr v-for="item in data" :key="item.id">
                    <td>{{ item.name }}</td>
                    <td>{{ item.stock }}</td>
                    <td>{{ item.qty }}</td>
                    <td>{{ dateFormatter(item.created_at) }}</td>
                    <td>{{ item.category_name }}</td>
                    <td>
                        <v-btn variant="text" @click="() => editModal = { isOpen: true, data: item }"
                            icon="mdi-pencil-outline"></v-btn>
                        <v-btn variant="text" @click="() => deleteModal = { id: item.id, isOpen: true }"
                            icon="mdi-delete"></v-btn>
                    </td>
                </tr>
            </tbody>
        </v-table>

        <v-card w="50%" v-else>
            <v-card-title>Hasil Pencarian Kosong</v-card-title>
            <v-card-text>
                Maaf, kami tidak menemukan hasil untuk pencarian Anda.
            </v-card-text>
        </v-card>

        <v-pagination :total-visible="5" v-model="queries.page" class="my-4" :length="meta.last_page"
            @input="fetchData"></v-pagination>
    </v-container>
</template>


