<script setup>
import {
    defineProps,
    onMounted,
    ref,
} from 'vue';

import { useToast } from 'vue-toastification';

import { getAllProducts } from '../../utils/https/product.js';
import { updateTransaction } from '../../utils/https/transaction.js';

const toast = useToast();

const modal = true;

const products = ref([]);



const status = ref({
    isProductLoading: true,
    isSubmitLoading: false,
});

const props = defineProps({
    onClose: Function,
    onSave: Function,
    data: Object
})

const input = ref({
    product_id: 1,
    qty: "",
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

const saveAction = async () => {
    try {
        status.value.isSubmitLoading = true;
        await updateTransaction({
            transactionID: props.data.id,
            productID: input.value.product_id,
            qty: input.value.qty
        });
        toast.success("Berhasil mengubah data transaksi");

        props.onSave();
        props.onClose();
    } catch (error) {
        if (error.response?.data?.message) {
            return toast.error(error.response.data.message);
        }
        return toast.error("Terjadi kesalahan");

    } finally {
        status.value.isSubmitLoading = false;
    }
}

const fetchProducts = async () => {
    try {
        const result = await getAllProducts({});
        products.value = result.data.data.data;
    } catch (error) {
        // console.error(error);
        return toast.error("Terjadi kesalahan ketika mengambil data produk dari server");
    } finally {
        status.value.isProductLoading = false;
    }
}

onMounted(() => {
    input.value.product_id = props.data.product_id;
    input.value.qty = props.data.qty;
});
onMounted(() => fetchProducts());

</script>

<template>
    <v-row justify="center">
        <v-dialog v-model="modal" persistent width="1024">
            <v-card>
                <v-card-title>
                    <span class="text-h5">Edit transaction history</span>
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
                    <v-btn color="blue-darken-1" variant="text" @click="onClose" :disabled="status.isSubmitLoading">
                        Close
                    </v-btn>
                    <v-btn color="blue-darken-1" variant="text" @click="saveAction" :loading="status.isSubmitLoading">
                        Save
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-row>
</template>
