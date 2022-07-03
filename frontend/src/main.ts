import { createApp } from 'vue';
import App from './App.vue';
import directives from "@/directives";
import router from "@/router/router";
import { createPinia } from 'pinia'

import 'bootstrap/scss/bootstrap.scss';
import './assets/scss/app.scss';

/* import the fontawesome core */
import { library } from '@fortawesome/fontawesome-svg-core';

/* import specific icons */
import {
    faTractor, faHouse, faUserGroup, faList, faAngleLeft,
    faXmark, faBars, faAnglesLeft, faAnglesRight, faAngleRight,
    faPen
} from '@fortawesome/free-solid-svg-icons';

/* import font awesome icon component */
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

/* add icons to the library */
library.add(
    faTractor, faHouse, faUserGroup, faList, faAngleLeft,
    faAngleRight, faXmark, faBars, faAnglesLeft, faAnglesRight,
    faPen
);

const app = createApp(App);

directives.forEach(directive => {
    app.directive(directive.name, directive);
});

app
    .component('font-awesome-icon', FontAwesomeIcon)
    .use(router)
    .use(createPinia())
    .mount('#app');
