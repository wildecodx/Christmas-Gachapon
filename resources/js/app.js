import { createApp } from 'vue';
import '../css/app.css';

import GachaponApp from './components/GachaponApp.vue';
import GachaponSetup from './components/GachaponSetup.vue';

const root = document.getElementById('app');

if (root) {
    if (root.dataset.page === 'setup') {
        createApp(GachaponSetup).mount(root);
    } else {
        createApp(GachaponApp).mount(root);
    }
}