
<script setup>
import { useRouter } from 'vue-router';

const router = useRouter();

const navAction = {
    transaction: () => router.push('/transaction'),
    salesResult: () => router.push('/sales-result'),
}

</script>


<template>
    <v-app> <v-layout class="rounded rounded-md">
            <v-app-bar title="App"></v-app-bar>

            <v-navigation-drawer expand-on-hover rail>

                <v-list density="compact" nav>
                    <v-list-item prepend-icon="mdi-list-box" title="Transactions" value="transaction"
                        @click="navAction.transaction">
                    </v-list-item>
                    <v-list-item prepend-icon="mdi-calendar-range" title="Hasil Penjualan" value="salesResult"
                        @click="navAction.salesResult">
                    </v-list-item>
                    <!-- <v-list-item prepend-icon="mdi-account-multiple" title="Shared with me" value="shared"></v-list-item>
                    <v-list-item prepend-icon="mdi-star" title="Starred" value="starred"></v-list-item> -->
                </v-list>
            </v-navigation-drawer>


            <v-main class="d-flex justify-center" style="min-height: 300px;">
                <router-view></router-view>
            </v-main>
        </v-layout>
    </v-app>
</template>

