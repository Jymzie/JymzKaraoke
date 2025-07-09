import Vue from 'vue'
import VueRouter from 'vue-router'
import store from './store'

Vue.use(VueRouter)


let router = new VueRouter({
    mode: 'history',
    base: '/Karaoke/', 
    routes: [
     
     
        {
            path: '/Play',
            name: 'Play',
            component: () => import('./components/Play'),
        },
        {
            path: '/Queue',
            name: 'Queue',
            component: () => import('./components/Queue'),
        },
      
        {
            path: '/error/:status_code?',
            name: 'error',
            component: () => import('./template/Error'),

            //NOTE Error page wont appear through direct link
            beforeEnter:(to,from,next) => {
                store.state.status != null ? next() : router.go(-1);
            }
        },
    ],
});

//ANCHOR router trappings
router.beforeEach((to, from, next) =>{
    if(to.name == null){
        next({name: 'Queue'})
    }
    else{
      next()  
    }
    // if(!store.state.session && to.name !== 'login'){
    //     next({name: 'login'})
    // }
    // else if(store.state.session && (to.name == 'login' || to.name == null)){
    //     next({name: 'home'})
    // }
    // else{
    //   next()  
    // }
})


export default router