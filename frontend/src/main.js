import { createApp } from 'vue'
import App from './App.vue'

import 'bootstrap/scss/bootstrap.scss'
import './assets/scss/app.scss'

/* import the fontawesome core */
import { library } from '@fortawesome/fontawesome-svg-core'

/* import specific icons */
import { faTractor, faHouse, faUserGroup, faList } from '@fortawesome/free-solid-svg-icons'

/* import font awesome icon component */
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

/* add icons to the library */
library.add(faTractor, faHouse, faUserGroup, faList)

createApp(App)
    .component('font-awesome-icon', FontAwesomeIcon)
    .mount('#app')
