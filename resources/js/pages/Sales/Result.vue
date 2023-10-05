<script setup>
import {
    onMounted,
    ref,
    watch,
} from 'vue';

import { useToast } from 'vue-toastification';

import Loading from '../../components/Loading.vue';
import { dateFormatter } from '../../utils/date';
import { getAllSalesReport } from '../../utils/https/salesReport.js';

const toast = useToast();
const data = ref([]);
const date = ref();

const queries = ref({
    start_date: "",
    end_date: ""
});

const status = ref({
    isDataLoading: false
});

watch(date, () => {
    queries.value.start_date = dateFormatter(date.value[0]);
    queries.value.end_date = dateFormatter(date.value[1]);
    fetchData();
})
const meta = ref({});


const fetchData = async () => {
    try {
        status.value.isDataLoading = true;
        const result = await getAllSalesReport(queries.value);
        data.value = result.data.data;
        // meta.value = result.data.data;
    } catch (error) {
        if (error.response?.data?.message) {
            return toast.error(error.response.data.message);
        }
        console.error(error);
        return toast.error("Terjadi kesalahan saat mengambil data");
    } finally {
        status.value.isDataLoading = false;

    }
}


onMounted(() => {
    const now = new Date();

    // Calculate the date 7 days ago
    const sevenDaysAgo = new Date(now);
    sevenDaysAgo.setDate(now.getDate() - 7);

    // Create an array with the dates
    date.value = [sevenDaysAgo, now];
    queries.value = {
        start_date: dateFormatter(data.value[0]),
        end_date: dateFormatter(data.value[1]),
    }
});

</script>

<template>
    <loading v-if="status.isDataLoading" />

    <v-container class="max-width" v-if="!status.isDataLoading">

        <VueDatePicker v-model="date" range default="true" :enable-time-picker="false" />
        <v-table h="full" v-if="data.length > 0">
            <thead>
                <tr>
                    <th class="text-left">
                        Jenis barang
                    </th>
                    <th class="text-left">
                        Jumlah terjual
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in data" :key="item.id">
                    <td>{{ item.category_name }}</td>
                    <td>{{ item.total_qty }}</td>
                </tr>
            </tbody>
        </v-table>



        <v-card w="50%" class="mt-5" v-else>
            <v-card-title>Hasil Pencarian Kosong</v-card-title>
            <v-card-text>
                Maaf, kami tidak menemukan hasil untuk pencarian Anda.
            </v-card-text>
        </v-card>

        <!-- <v-pagination :total-visible="5" v-model="queries.page" class="my-4" :length="meta.last_page"
            @input="fetchData"></v-pagination> -->
    </v-container>
</template>
