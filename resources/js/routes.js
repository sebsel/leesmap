import Feed from './views/Feed.vue'
import NotFound from './views/NotFound.vue'

export default [
    {path: '/channels/:id', component: Feed},
    {path: '**', component: NotFound}
]
