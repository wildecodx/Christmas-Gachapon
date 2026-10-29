import { createApp, h } from 'vue';
import QrcodeVue from 'qrcode.vue';

function mount_prize_qr_codes() {
  const nodes = document.querySelectorAll('[data-prize-qr]');

  nodes.forEach((element) => {
    const value = element.getAttribute('data-prize-qr');

    if (!value) {
      return;
    }

    createApp({
      render() {
        return h(QrcodeVue, {
          value: value,
          size: 180,
          level: 'M',
        });
      },
    }).mount(element);
  });
}

mount_prize_qr_codes();
