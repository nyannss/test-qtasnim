import "./bootstrap";
import "@mdi/font/css/materialdesignicons.css";
import "vuetify/styles";
import "vue-toastification/dist/index.css";
import "@vuepic/vue-datepicker/dist/main.css";

import Toast, { POSITION } from "vue-toastification";
import { createApp } from "vue/dist/vue.esm-bundler.js";
import { createVuetify } from "vuetify";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";

import VueDatePicker from "@vuepic/vue-datepicker";

import App from "./App.vue";
import router from "./router/router";

const app = createApp(App);

const vuetify = createVuetify({
    components: {
        ...components,
    },
    directives,
});

const options = {
    position: POSITION.BOTTOM_CENTER,
    timeout: 3000,
};

app.use(Toast, options);
app.component("VueDatePicker", VueDatePicker);

app.use(router);
app.use(vuetify);
app.mount("#app");
