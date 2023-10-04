<template>
    <div class="text-center">
        <v-dialog :persistent="isLoading" v-model="isOpen" width="auto">
            <v-card>
                <v-card-text>
                    Are you sure to delete this item?
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue-darken-1" variant="text" @click="onClose" :disabled="isLoading">
                        Close
                    </v-btn>
                    <v-btn color="blue-darken-1" variant="text" @click="onAccept" :loading="isLoading">
                        Yes, sure
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
export default {
    data() {
        return {
            dialog: false,
        }
    },
    computed: {
        isOpenModal() {
            return this.isOpen
        }
    },
    props: {
        onClose: Function,
        onAccept: Function,
        isOpen: Boolean,
        isLoading: Boolean
    }
}
</script>
