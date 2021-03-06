import Vue from "vue"
import VueRouter from "vue-router"

// ページコンポーネントをインポートする
import PhotoList from './pages/PhotoList.vue'
import Login from './pages/Login.vue'
import SystemError from './pages/errors/System.vue'
import PhotoDetail from './pages/PhotoDetail.vue'

import store from "./store";

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
        component: Login,
        beforeEnter (to, from, next) {
            if (store.getters['auth/check']) {
                next('/')
            } else {
                // 未ログイン時は引き続きログイン画面を表示させる
                next()
            }
        }
    },
    {
        path: '/500',
        component: SystemError
    },
    {
        path: '/photos/:id',
        component: PhotoDetail,
        props: true
    },
]

// VueRouterインスタンスを作成する
const router = new VueRouter({
    mode: "history",
    routes
})

// VueRouterインスタンスをエクスポートする
// app.jsでインポートするため
export default router
