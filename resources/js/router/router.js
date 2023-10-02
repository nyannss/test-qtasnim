import {
  createRouter,
  createWebHistory,
} from 'vue-router';

const Home = { template: "<div>Home</div>" };

const routes = [{ path: "/", name: "Home", component: Home }];

const router = createRouter({
    // 4. Provide the history implementation to use. We are using the hash history for simplicity here.
    history: createWebHistory(import.meta.env.BASE_URL),
    routes, // short for `routes: routes`
});

export default router;
