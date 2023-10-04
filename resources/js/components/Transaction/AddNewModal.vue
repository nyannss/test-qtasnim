<script setup>
import {
    onMounted,
    ref,
} from 'vue';

import { useToast } from 'vue-toastification';

import { getAllProducts } from '../../utils/https/product.js';
import { insertNewTransaction } from '../../utils/https/transaction.js';

const toast = useToast();
const input = ref({
    product_id: "",
    qty: 1
});

const products = ref([]);

const modal = ref(false);
const status = ref({
    isProductLoading: true,
    isSubmitLoading: false,
});

const rules = {
    productId: [
        value => {
            if (value !== "") return true
            return 'Product is required.'
        }
    ],
    'qty': [
        value => {
            if (value > 0) return true
            return 'Product quantity must more than 0.'
        }
    ]
};

const fetchProducts = async () => {
    try {
        const result = await getAllProducts({});
        products.value = result.data.data.data;
    } catch (error) {
        console.error(error);
        return toast.error("Terjadi kesalahan ketika mengambil data produk dari server");
    } finally {
        status.value.isProductLoading = false;
    }
}

const submit = async () => {
    try {
        status.value.isSubmitLoading = true;
        await insertNewTransaction({ productID: input.value.product_id, qty: input.value.qty });
        toast.success("Berhasil menambahkan histori");

        modal.value = false;
    } catch (error) {
        if (error.response?.data?.message) {
            return toast.error(error.response.data.message);
        }
        return toast.error("Terjadi kesalahan");

    } finally {
        status.value.isSubmitLoading = false;
    }
}

onMounted(() => fetchProducts());
</script>

<template>
    <VLayoutItem model-value position="bottom" class="text-end" size="88">
        <div class="ma-4">
            <v-btn @click="() => modal = true" icon="mdi-plus" size="large" elevation="8" />
        </div>
    </VLayoutItem>

    <v-row justify="center">
        <v-dialog v-model="modal" persistent width="1024">
            <v-card>
                <v-card-title>
                    <span class="text-h5">Add new transaction history</span>
                </v-card-title>
                <v-card-text>
                    <v-container>
                        <v-row>
                            <v-col cols="12">
                                <v-select v-model="input.product_id" item-value="id" :items="products" label="Product*"
                                    :loading="status.isProductLoading" item-title="name" :rules="rules.productId" required>
                                    <template v-slot:item="{ props, item }">
                                        <v-list-item v-bind="props" :title="`${item.raw.name} - Stok: ${item.raw.stock}`"
                                            :subtitle="item.raw.category_name"></v-list-item>
                                    </template>
                                </v-select>
                            </v-col>
                            <v-col cols="12">
                                <v-text-field label="Jumlah barang*" v-model="input.qty" :rules="rules.qty">
                                    <template v-slot:append>
                                        <v-btn icon="mdi-plus" @click="() => input.qty++" />
                                    </template>
                                    <template v-slot:prepend>
                                        <v-btn icon="mdi-minus" @click="() => input.qty > 1 && input.qty--" />
                                    </template>
                                </v-text-field>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue-darken-1" variant="text" @click="modal = false" :disabled="status.isSubmitLoading">
                        Close
                    </v-btn>
                    <v-btn color="blue-darken-1" variant="text" @click="submit" :loading="status.isSubmitLoading">
                        Save
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
