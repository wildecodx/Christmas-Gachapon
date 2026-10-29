import { createApp } from 'vue';
import GachaponManagement from './components/GachaponManagement.vue';

const element = document.getElementById('gachapon-management');

if (element) {
    createApp(GachaponManagement, {
        token: element.dataset.token,
    }).mount(element);
}