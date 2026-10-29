
<template>
    <main class="management-page">
        <section class="management-container">
            <header class="management-header">
                <span class="management-icon">🎄</span>
                <p class="management-eyebrow">CHRISTMAS GACHAPON</p>
                <h1>Your Gachapon Machine</h1>
                <p>Manage your Christmas surprises and gift inventory.</p>
            </header>

            <div v-if="loading" class="management-message">
                Loading your machine...
            </div>

            <div v-else-if="errorMessage && !machine" class="management-error">
                <h2>Unable to access your machine</h2>
                <p>{{ errorMessage }}</p>
                <p>Check your private management link and try again.</p>
            </div>

            <template v-else-if="machine">
                <section class="management-panel">
                    <div class="machine-heading">
                        <div>
                            <span class="management-label">MACHINE DETAILS</span>
                            <h2>{{ machine.name || 'Christmas Gachapon' }}</h2>
                            <p>
                                {{ machine.godparent_role === 'godmother'
                                    ? '👩‍🦰 Godmother (Ninang)'
                                    : '🤵 Godfather (Ninong)' }}
                            </p>
                        </div>

                        <span class="machine-status">
                            {{ machine.status }}
                        </span>
                    </div>

                    <div class="machine-details-grid">
                        <div class="machine-detail">
                            <small>Godchildren</small>
                            <strong>{{ machine.number_of_godchildren }}</strong>
                        </div>

                        <div class="machine-detail">
                            <small>Openings per child</small>
                            <strong>{{ machine.openings_per_godchild }}</strong>
                        </div>

                        <div class="machine-detail">
                            <small>Planned openings</small>
                            <strong>{{ totalOpenings }}</strong>
                        </div>
                    </div>

                    <p v-if="machine.welcome_message" class="welcome-message">
                        {{ machine.welcome_message }}
                    </p>

                    <div v-if="machine.opens_at || machine.closes_at" class="machine-dates">
                        <p v-if="machine.opens_at">
                            <strong>Opens:</strong> {{ formatDate(machine.opens_at) }}
                        </p>
                        <p v-if="machine.closes_at">
                            <strong>Closes:</strong> {{ formatDate(machine.closes_at) }}
                        </p>
                    </div>
                </section>

                <section class="inventory-summary">
                    <article class="inventory-stat">
                        <span>🎁</span>
                        <small>Gift Items</small>
                        <strong>{{ gifts.length }}</strong>
                    </article>

                    <article class="inventory-stat">
                        <span>📦</span>
                        <small>Total Stock</small>
                        <strong>{{ totalStock }}</strong>
                    </article>

                    <article class="inventory-stat">
                        <span>✨</span>
                        <small>Remaining Gifts</small>
                        <strong>{{ remainingStock }}</strong>
                    </article>
                </section>

                <section class="management-panel">
                    <div class="inventory-heading">
                        <div>
                            <span class="management-label">YOUR INVENTORY</span>
                            <h2>Gift Collection</h2>
                            <p>Review your gifts and add more stock when needed.</p>
                        </div>

                        <button
                            type="button"
                            class="refresh-button"
                            @click="loadMachine"
                            :disabled="loading"
                        >
                            ↻ Refresh
                        </button>
                    </div>

                    <p v-if="errorMessage" class="management-error-text">
                        {{ errorMessage }}
                    </p>

                    <p v-if="successMessage" class="management-success">
                        {{ successMessage }}
                    </p>

                    <div v-if="!gifts.length" class="empty-inventory">
                        🎁 No gift items were found for this machine.
                    </div>

                    <div v-else class="management-gift-list">
                        <article
                            v-for="gift in gifts"
                            :key="gift.id"
                            class="management-gift"
                        >
                            <div class="gift-heading">
                                <span class="gift-type-icon">
                                    {{ gift.gift_type === 'money' ? '💵' : '🎁' }}
                                </span>

                                <div class="gift-title">
                                    <h3>
                                        {{ gift.gift_type === 'money'
                                            ? 'Cash Gift'
                                            : gift.name }}
                                    </h3>
                                    <p v-if="gift.description">
                                        {{ gift.description }}
                                    </p>
                                </div>

                                <span
                                    class="gift-status"
                                    :class="{ inactive: !gift.is_active }"
                                >
                                    {{ gift.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                            <div v-if="gift.gift_type === 'money'" class="gift-amount">
                                ₱{{ Number(gift.amount || 0).toLocaleString('en-PH', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                }) }}
                                <small>per gift</small>
                            </div>

                            <div class="gift-stock-grid">
                                <div>
                                    <small>Total quantity</small>
                                    <strong>{{ gift.quantity }}</strong>
                                </div>

                                <div>
                                    <small>Remaining</small>
                                    <strong>{{ gift.remaining_quantity }}</strong>
                                </div>

                                <div>
                                    <small>Used</small>
                                    <strong>
                                        {{ Number(gift.quantity) -
                                            Number(gift.remaining_quantity) }}
                                    </strong>
                                </div>
                            </div>

                            <div class="gift-stock-track">
                                <div
                                    class="gift-stock-progress"
                                    :style="{
                                        width: `${gift.quantity > 0
                                            ? Math.max(0, Math.min(100,
                                                gift.remaining_quantity / gift.quantity * 100))
                                            : 0}%`
                                    }"
                                ></div>
                            </div>

                            <button
                                type="button"
                                class="add-stock-button"
                                @click="addStock(gift)"
                                :disabled="updatingGiftId === gift.id"
                            >
                                {{
                                    updatingGiftId === gift.id
                                        ? 'Adding stock...'
                                        : '＋ Add Stock'
                                }}
                            </button>
                        </article>
                    </div>
                </section>

                <section class="management-panel">
                    <div class="inventory-heading">
                        <div>
                            <span class="management-label">PRIZE HISTORY</span>
                            <h2>Who received each prize</h2>
                            <p>
                                Each code belongs to one godchild. Scan a code
                                to open that child's official prize record.
                            </p>
                        </div>
                    </div>

                    <p v-if="!history.length" class="empty-inventory">
                        No godchildren have opened the letter yet.
                    </p>

                    <article
                        v-for="child in history"
                        :key="child.id"
                        class="history-child"
                    >
                        <h3 class="history-child-title">
                            {{ child.name }}
                            <span v-if="child.code">· {{ child.code }}</span>
                        </h3>
                        <p v-if="!child.code" class="history-missing-code">
                            This code was issued before codes were saved.
                        </p>

                        <div class="history-table-wrap">
                            <table class="history-table">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>When</th>
                                        <th>Prize</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="!child.prizes.length">
                                        <td colspan="4">No prizes yet.</td>
                                    </tr>
                                    <tr
                                        v-for="prize in child.prizes"
                                        :key="`${child.id}-${prize.opening_number}`"
                                    >
                                        <td>{{ child.code || '—' }}</td>
                                        <td>{{ prize.opened_label }}</td>
                                        <td>{{ prize.name }}</td>
                                        <td>
                                            <template v-if="prize.amount !== null && prize.amount !== undefined">
                                                ₱{{ Number(prize.amount).toLocaleString('en-PH', {
                                                    minimumFractionDigits: 2,
                                                    maximumFractionDigits: 2
                                                }) }}
                                            </template>
                                            <template v-else>—</template>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div v-if="child.prize_url" class="history-qr">
                            <QrcodeVue
                                :value="child.prize_url"
                                :size="160"
                                level="M"
                            />
                            <p>Scan to view this godchild's official prizes.</p>
                            <a :href="child.prize_url">{{ child.prize_url }}</a>
                        </div>
                    </article>
                </section>

                <p class="management-security-note">
                    🔒 This is your private management page. Do not share its URL
                    with your godchildren.
                </p>
            </template>
        </section>
    </main>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import QrcodeVue from 'qrcode.vue';

const props = defineProps({
    token: {
        type: String,
        required: true,
    },
});

const machine = ref(null);
const gifts = ref([]);
const history = ref([]);
const loading = ref(false);
const updatingGiftId = ref(null);
const errorMessage = ref('');
const successMessage = ref('');

const totalOpenings = computed(() =>
    Math.max(0, Number(machine.value?.number_of_godchildren) || 0) *
    Math.max(0, Number(machine.value?.openings_per_godchild) || 0)
);

const totalStock = computed(() =>
    gifts.value.reduce((sum, gift) => sum + (Number(gift.quantity) || 0), 0)
);

const remainingStock = computed(() =>
    gifts.value.reduce(
        (sum, gift) => sum + (Number(gift.remaining_quantity) || 0),
        0
    )
);

async function loadMachine() {
    loading.value = true;
    errorMessage.value = '';
    successMessage.value = '';

    try {
        const response = await fetch(
            `/manage/${encodeURIComponent(props.token)}/data`,
            {
                headers: { Accept: 'application/json' },
                cache: 'no-store',
            }
        );

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Unable to load machine details.');
        }

        machine.value = data.machine;
        gifts.value = data.gifts;
        history.value = Array.isArray(data.history) ? data.history : [];
    } catch (error) {
        errorMessage.value = error.message || 'Could not connect to the server.';
    } finally {
        loading.value = false;
    }
}

async function addStock(gift) {
    const answer = window.prompt(
        `How many additional units of "${gift.name || 'Cash Gift'}" do you want to add?`,
        '1'
    );

    if (answer === null) return;

    const quantity = Number(answer);

    if (!Number.isInteger(quantity) || quantity < 1 || quantity > 10000) {
        errorMessage.value = 'Enter a whole number between 1 and 10,000.';
        successMessage.value = '';
        return;
    }

    updatingGiftId.value = gift.id;
    errorMessage.value = '';
    successMessage.value = '';

    try {
        const response = await fetch(
            `/manage/${encodeURIComponent(props.token)}/gifts/${gift.id}/stock`,
            {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN':
                        document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                },
                body: JSON.stringify({ add_quantity: quantity }),
            }
        );

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Unable to update gift stock.');
        }

        gift.quantity = data.gift.quantity;
        gift.remaining_quantity = data.gift.remaining_quantity;
        successMessage.value = data.message;
    } catch (error) {
        errorMessage.value = error.message || 'Could not update gift stock.';
    } finally {
        updatingGiftId.value = null;
    }
}

function formatDate(value) {
    if (!value) return '';

    const date = new Date(value);

    return Number.isNaN(date.getTime())
        ? value
        : date.toLocaleString();
}

onMounted(loadMachine);
</script>


<style scoped>
/* ========================================
   MANAGEMENT PAGE
======================================== */

.management-page {
    position: relative;
    min-height: 100vh;
    overflow: hidden;
    padding: 52px 20px 72px;
    color: #24372d;
    font-family: 'DM Sans', sans-serif;
    background:
        radial-gradient(
            ellipse at 10% 5%,
            rgba(53, 115, 75, 0.42),
            transparent 34%
        ),
        radial-gradient(
            ellipse at 95% 40%,
            rgba(185, 50, 54, 0.12),
            transparent 30%
        ),
        linear-gradient(145deg, #102d26, #0a211b 65%, #102d26);
}

.management-page::before,
.management-page::after {
    position: absolute;
    z-index: 0;
    color: rgba(248, 217, 140, 0.13);
    font-size: clamp(100px, 18vw, 220px);
    line-height: 1;
    pointer-events: none;
}

.management-page::before {
    content: '✳';
    top: 20px;
    left: -45px;
    transform: rotate(-15deg);
}

.management-page::after {
    content: '✦';
    top: 390px;
    right: -30px;
    transform: rotate(15deg);
}

.management-container {
    position: relative;
    z-index: 1;
    width: min(100%, 1000px);
    margin: 0 auto;
}

/* ========================================
   HEADER
======================================== */

.management-header {
    margin-bottom: 30px;
    padding: 38px 28px;
    text-align: center;
    border: 1px solid rgba(232, 188, 98, 0.55);
    border-radius: 26px;
    background: #fffaf0;
    box-shadow:
        0 30px 90px rgba(0, 0, 0, 0.22),
        0 0 0 7px rgba(255, 255, 255, 0.025);
}

.management-icon {
    display: grid;
    width: 76px;
    height: 76px;
    place-items: center;
    margin: 0 auto 20px;
    border: 1px solid #efd99e;
    border-radius: 24px;
    background: #fff1ce;
    box-shadow: 0 8px 22px rgba(153, 113, 37, 0.1);
    font-size: 38px;
    transform: rotate(-4deg);
}

.management-eyebrow,
.management-label {
    display: block;
    color: #9c7530;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.2em;
}

.management-eyebrow {
    margin: 0;
}

.management-header h1 {
    margin: 14px 0 12px;
    color: #174b38;
    font-family: 'Playfair Display', serif;
    font-size: clamp(2rem, 4vw, 2.8rem);
    font-weight: 700;
    line-height: 1.2;
    letter-spacing: -0.045em;
}

.management-header p:last-child {
    max-width: 470px;
    margin: 0 auto;
    color: #778278;
    font-size: 0.96rem;
    line-height: 1.8;
}

/* ========================================
   PANELS
======================================== */

.management-panel {
    margin-bottom: 24px;
    padding: 26px;
    border: 1px solid rgba(232, 188, 98, 0.4);
    border-radius: 20px;
    background: #fffaf0;
    box-shadow: 0 14px 40px rgba(0, 0, 0, 0.16);
}

.machine-heading,
.inventory-heading {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
}

.machine-heading h2,
.inventory-heading h2 {
    margin: 10px 0 8px;
    color: #174b38;
    font-family: 'Playfair Display', serif;
    font-size: 1.55rem;
    line-height: 1.35;
}

.machine-heading p,
.inventory-heading p {
    margin: 0;
    color: #778278;
    font-size: 0.9rem;
    line-height: 1.7;
}

.machine-status,
.gift-status {
    display: inline-flex;
    align-items: center;
    flex-shrink: 0;
    padding: 7px 11px;
    border-radius: 999px;
    background: #eaf3e8;
    color: #174b38;
    font-size: 0.76rem;
    font-weight: 700;
    text-transform: capitalize;
}

.gift-status.inactive {
    background: #f8e8e8;
    color: #8e252b;
}

/* ========================================
   MACHINE DETAILS
======================================== */

.machine-details-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
    margin-top: 25px;
}

.machine-detail {
    min-width: 0;
    padding: 18px;
    border: 1px solid #e8e7dc;
    border-radius: 14px;
    background: #fffefa;
}

.machine-detail small,
.inventory-stat small,
.gift-stock-grid small {
    display: block;
    margin-bottom: 8px;
    color: #778278;
    font-size: 0.8rem;
    line-height: 1.5;
}

.machine-detail strong {
    color: #174b38;
    font-size: 1.5rem;
    font-weight: 800;
    font-variant-numeric: tabular-nums;
    overflow-wrap: anywhere;
}

.welcome-message {
    margin: 22px 0 0;
    padding: 18px 20px;
    border: 1px solid #ead8aa;
    border-radius: 13px;
    background: #fff6df;
    color: #7e7158;
    font-size: 0.9rem;
    line-height: 1.8;
    white-space: pre-wrap;
    overflow-wrap: anywhere;
}

.machine-dates {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
    margin-top: 15px;
}

.machine-dates p {
    margin: 0;
    padding: 13px 15px;
    border-radius: 11px;
    background: #f7f5eb;
    color: #778278;
    font-size: 0.83rem;
    line-height: 1.7;
    overflow-wrap: anywhere;
}

.machine-dates strong {
    color: #31473a;
}

/* ========================================
   INVENTORY SUMMARY
======================================== */

.inventory-summary {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 14px;
    margin: 0 0 24px;
}

.inventory-stat {
    min-width: 0;
    padding: 20px;
    border: 1px solid rgba(232, 188, 98, 0.4);
    border-radius: 17px;
    background: #fffaf0;
    box-shadow: 0 10px 28px rgba(0, 0, 0, 0.12);
}

.inventory-stat > span {
    display: grid;
    width: 45px;
    height: 45px;
    place-items: center;
    margin-bottom: 14px;
    border-radius: 13px;
    background: #fff1ce;
    font-size: 23px;
}

.inventory-stat small {
    margin-bottom: 5px;
}

.inventory-stat strong {
    display: block;
    color: #174b38;
    font-size: clamp(1.5rem, 3vw, 2rem);
    font-weight: 800;
    font-variant-numeric: tabular-nums;
    overflow-wrap: anywhere;
}

/* ========================================
   BUTTONS AND MESSAGES
======================================== */

.refresh-button,
.add-stock-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 44px;
    padding: 11px 17px;
    border: 0;
    border-radius: 11px;
    font-family: 'DM Sans', sans-serif;
    font-size: 0.88rem;
    font-weight: 700;
    cursor: pointer;
    transition:
        background 160ms ease,
        transform 160ms ease;
}

.refresh-button {
    flex-shrink: 0;
    border: 1px solid #d7e3d4;
    background: #eaf3e8;
    color: #174b38;
}

.refresh-button:hover:not(:disabled) {
    background: #dcebd9;
    transform: translateY(-1px);
}

.add-stock-button {
    width: 100%;
    background: #b93236;
    color: #fff;
    box-shadow: 0 4px 0 #8e252b;
}

.add-stock-button:hover:not(:disabled) {
    background: #8e252b;
    transform: translateY(-1px);
}

.add-stock-button:active:not(:disabled) {
    transform: translateY(2px);
    box-shadow: 0 2px 0 #8e252b;
}

.add-stock-button:disabled,
.refresh-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.management-message,
.management-error,
.empty-inventory {
    margin-bottom: 24px;
    padding: 28px 22px;
    border: 1px solid #e8e7dc;
    border-radius: 16px;
    background: #fffaf0;
    color: #778278;
    text-align: center;
    line-height: 1.8;
}

.management-error {
    border-color: #efc4c1;
    background: #fff0ee;
    color: #b4232b;
}

.management-error h2 {
    margin-top: 0;
    color: #8e252b;
    font-family: 'Playfair Display', serif;
}

.management-error p {
    line-height: 1.7;
}

.management-error-text {
    margin: 20px 0 0;
    padding: 13px 15px;
    border: 1px solid #efc4c1;
    border-radius: 11px;
    background: #fff0ee;
    color: #b4232b;
    font-size: 0.86rem;
    line-height: 1.65;
    overflow-wrap: anywhere;
}

.management-success {
    margin: 20px 0 0;
    padding: 14px 16px;
    border: 1px solid #c9dfc7;
    border-radius: 12px;
    background: #edf6e9;
    color: #246b45;
    font-size: 0.88rem;
    font-weight: 600;
    line-height: 1.65;
}

/* ========================================
   GIFT COLLECTION
======================================== */

.management-gift-list {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
    margin-top: 24px;
}

.management-gift {
    min-width: 0;
    padding: 20px;
    border: 1px solid #e8e7dc;
    border-radius: 16px;
    background: #fffefa;
    box-shadow: 0 3px 12px rgba(39, 55, 42, 0.035);
    transition:
        border-color 180ms ease,
        box-shadow 180ms ease,
        transform 180ms ease;
}

.management-gift:hover {
    border-color: #cbd9c9;
    box-shadow: 0 8px 24px rgba(39, 55, 42, 0.07);
    transform: translateY(-2px);
}

.gift-heading {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    min-width: 0;
}

.gift-type-icon {
    display: grid;
    width: 46px;
    height: 46px;
    flex: 0 0 46px;
    place-items: center;
    border: 1px solid #efd99e;
    border-radius: 13px;
    background: #fff1ce;
    font-size: 23px;
}

.gift-title {
    flex: 1;
    min-width: 0;
}

.gift-title h3 {
    margin: 4px 0 6px;
    color: #174b38;
    font-family: 'Playfair Display', serif;
    font-size: 1.12rem;
    line-height: 1.4;
    overflow-wrap: anywhere;
}

.gift-title p {
    margin: 0;
    color: #778278;
    font-size: 0.82rem;
    line-height: 1.65;
    overflow-wrap: anywhere;
    white-space: pre-wrap;
}

.gift-amount {
    margin: 20px 0 10px;
    color: #287443;
    font-size: 1.65rem;
    font-weight: 800;
    font-variant-numeric: tabular-nums;
    overflow-wrap: anywhere;
}

.gift-amount small {
    display: block;
    margin-top: 4px;
    color: #778278;
    font-size: 0.78rem;
    font-weight: 400;
}

.gift-stock-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 9px;
    margin-top: 23px;
}

.gift-stock-grid > div {
    min-width: 0;
    padding: 12px 10px;
    border: 1px solid #e8e7dc;
    border-radius: 11px;
    background: #f7f5eb;
}

.gift-stock-grid small {
    margin-bottom: 7px;
    font-size: 0.73rem;
}

.gift-stock-grid strong {
    color: #174b38;
    font-size: 1.2rem;
    font-weight: 800;
    font-variant-numeric: tabular-nums;
    overflow-wrap: anywhere;
}

.gift-stock-track {
    height: 8px;
    margin: 19px 0;
    overflow: hidden;
    border-radius: 999px;
    background: #e8e7dc;
}

.gift-stock-progress {
    height: 100%;
    border-radius: inherit;
    background: linear-gradient(90deg, #174b38, #5d9c68);
    transition: width 300ms ease;
}

/* ========================================
   SECURITY NOTE
======================================== */

.management-security-note {
    max-width: 650px;
    margin: 26px auto 0;
    padding: 16px 20px;
    border: 1px solid rgba(232, 188, 98, 0.35);
    border-radius: 13px;
    background: rgba(255, 250, 240, 0.08);
    color: #e1e9df;
    font-size: 0.83rem;
    line-height: 1.8;
    text-align: center;
}

/* ========================================
   RESPONSIVE
======================================== */

@media (max-width: 700px) {
    .management-page {
        padding: 24px 12px 38px;
    }

    .management-header {
        padding: 28px 18px;
        border-radius: 20px;
    }

    .management-panel {
        padding: 20px 16px;
        border-radius: 17px;
    }

    .machine-details-grid,
    .inventory-summary {
        gap: 9px;
    }

    .machine-detail,
    .inventory-stat {
        padding: 13px 11px;
    }

    .machine-detail strong {
        font-size: 1.25rem;
    }

    .management-gift-list {
        grid-template-columns: 1fr;
    }

    .management-gift {
        padding: 17px;
    }

    .machine-dates {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .management-header h1 {
        font-size: 2rem;
    }

    .machine-heading,
    .inventory-heading {
        align-items: stretch;
        flex-direction: column;
    }

    .machine-status {
        align-self: flex-start;
    }

    .refresh-button {
        align-self: flex-start;
    }

    .machine-details-grid,
    .inventory-summary {
        grid-template-columns: 1fr;
    }

    .inventory-stat {
        display: grid;
        grid-template-columns: 42px minmax(0, 1fr);
        align-items: center;
        column-gap: 12px;
    }

    .inventory-stat > span {
        grid-row: span 2;
        margin: 0;
    }

    .inventory-stat small {
        margin: 0;
    }

    .inventory-stat strong {
        font-size: 1.45rem;
    }

    .gift-stock-grid {
        gap: 6px;
    }

    .gift-stock-grid > div {
        padding: 10px 7px;
    }

    .gift-stock-grid small {
        font-size: 0.68rem;
    }

    .gift-stock-grid strong {
        font-size: 1.05rem;
    }
}

.history-child {
    margin-top: 22px;
    padding-top: 18px;
    border-top: 1px solid rgba(232, 188, 98, 0.45);
}

.history-child-title {
    margin: 0 0 8px;
    color: #174b38;
    font-family: 'Playfair Display', Georgia, serif;
}

.history-missing-code {
    margin: 0 0 12px;
    color: #7b6852;
    font-size: 0.92rem;
}

.history-table-wrap {
    overflow-x: auto;
    border: 1px solid rgba(232, 188, 98, 0.55);
    border-radius: 14px;
    background: #fff;
}

.history-table {
    width: 100%;
    border-collapse: collapse;
}

.history-table th,
.history-table td {
    padding: 12px 14px;
    border-bottom: 1px solid #efe8d6;
    text-align: left;
    vertical-align: top;
}

.history-table th {
    color: #174b38;
    font-size: 0.75rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.history-table tr:last-child td {
    border-bottom: 0;
}

.history-qr {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    margin-top: 16px;
    text-align: center;
}

.history-qr p,
.history-qr a {
    margin: 0;
    color: #637267;
    overflow-wrap: anywhere;
}

@media (prefers-reduced-motion: reduce) {
    .management-page *,
    .management-page *::before,
    .management-page *::after {
        animation: none !important;
        transition: none !important;
        scroll-behavior: auto !important;
    }
}
</style>

