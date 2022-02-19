import Vue from "vue";
import App from "./App";
import ModalTrigger from "./ModalTrigger";

new Vue({
    render: h => h(App)
}).$mount('#app-modal');

new ModalTrigger('#js-modal');