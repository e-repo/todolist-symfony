import { createApp } from 'vue'
import App from './App.vue'
import directives from "@/directives"
import router from "@/router/router"
import { createPinia } from 'pinia'

import 'bootstrap/scss/bootstrap.scss'
import './assets/scss/app.scss'

/* import the fontawesome core */
import { library } from '@fortawesome/fontawesome-svg-core'

/* import specific icons */
import {
    faTractor, faHouse, faUserGroup, faList, faAngleLeft,
    faXmark, faBars, faAnglesLeft, faAnglesRight, faAngleRight,
    faPen, faDownload, faUpload, faCheckDouble, faPlus, faTrash
} from '@fortawesome/free-solid-svg-icons'

/* import font awesome icon component */
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

/* add icons to the library */
library.add(
    faTractor, faHouse, faUserGroup, faList, faAngleLeft,
    faAngleRight, faXmark, faBars, faAnglesLeft, faAnglesRight,
    faPen, faDownload, faUpload, faCheckDouble, faPlus,
    faTrash
)

const app = createApp(App)

directives.forEach(directive => {
    app.directive(directive.name, directive)
})

app
    .component('FontAwesomeIcon', FontAwesomeIcon)
    .use(router)
    .use(createPinia())
    .mount('#app')
