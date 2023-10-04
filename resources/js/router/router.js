import { createRouter, createWebHistory } from "vue-router";

const Home = () => import("../pages/Home.vue");
const TrList = () => import("../pages/Transaction/List.vue");
const NotFound = () => import("../pages/Error/NotFound.vue");

const routes = [
    { path: "/", name: "Home", component: Home },
    { path: "/transaction", name: "Transaction", component: TrList },
    { path: "/:catchAll(.*)", component: NotFound },
];

const router = createRouter({
    // 4. Provide the history implementation to use. We are using the hash history for simplicity here.
    history: createWebHistory(import.meta.env.BASE_URL),
    routes, // short for `routes: routes`
});

export default router;
