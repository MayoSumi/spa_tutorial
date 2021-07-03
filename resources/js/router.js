import Vue from '/Applications/MAMP/htdocs/spa_tutorial/node_modules/vue/dist/vue.esm.browser';
import VueRouter from '/Applications/MAMP/htdocs/spa_tutorial/node_modules/vue-router/dist/vue-router.esm.browser';

// ページコンポーネントをインポートする
import PhotoList from './pages/PhotoList.vue'
import Login from './pages/Login.vue'

// VueRouterプラグインを使用する
// これによって<RouterView />コンポーネントなどを使うことができる
Vue.use(VueRouter)

// パスとコンポーネントのマッピング
const routes = [
    {
        path: '/',
        component: PhotoList
    },
    {
        path: '/login',
        component: Login
    }
]

// VueRouterインスタンスを作成する
const router = new VueRouter({
    routes
})

// VueRouterインスタンスをエクスポートする
// app.jsでインポートするため
export default router
