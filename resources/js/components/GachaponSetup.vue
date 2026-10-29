<template>
    <main class="setup-page">
        <ChristmasEffects />
        <section class="setup-card">
            <div v-if="step === 'form'" class="setup-header">
                <span class="setup-icon">🎁</span>
                <p class="eyebrow">CHRISTMAS GACHAPON</p>
                <h1>Let's set up your machine!</h1>
                <p class="subtitle">
                    Prepare a little Christmas surprise for your godchildren
                    (inaanak).
                </p>
            </div>

            <form v-if="step === 'form'" @submit.prevent="submitForm">
                <div
                    id="settings-section"
                    :class="{
                        'section-highlight': highlightedSection === 'settings',
                    }"
                >
                    <div class="form-section">
                        <label>Are you a godfather or godmother?</label>

                        <div class="choice-grid">
                            <button
                                type="button"
                                :class="{
                                    selected:
                                        form.godparent_role === 'godfather',
                                }"
                                @click="form.godparent_role = 'godfather'"
                            >
                                🤵 Godfather (Ninong)
                            </button>

                            <button
                                type="button"
                                :class="{
                                    selected:
                                        form.godparent_role === 'godmother',
                                }"
                                @click="form.godparent_role = 'godmother'"
                            >
                                👩‍🦰 Godmother (Ninang)
                            </button>
                        </div>
                    </div>

                    <div class="form-section">
                        <label for="number_of_gifts"
                            >How many gift items?</label
                        >
                        <input
                            id="number_of_gifts"
                            v-model.number="form.number_of_gifts"
                            type="number"
                            min="0"
                            max="30"
                            required
                        />
                    </div>

                    <div class="form-section">
                        <label for="number_of_godchildren">
                            How many godchildren (inaanak) will play?
                        </label>
                        <input
                            id="number_of_godchildren"
                            v-model.number="form.number_of_godchildren"
                            type="number"
                            min="1"
                            max="100"
                            required
                        />
                    </div>

                    <div class="form-section">
                        <label for="openings_per_godchild">
                            How many times can each godchild open a gift?
                        </label>
                        <input
                            id="openings_per_godchild"
                            v-model.number="form.openings_per_godchild"
                            type="number"
                            min="1"
                            max="30"
                            required
                        />
                    </div>
                </div>

                <div
                    id="gifts-section"
                    class="gift-section"
                    :class="{
                        'section-highlight': highlightedSection === 'gifts',
                    }"
                >
                    <h2>🎄 Configure your gifts</h2>

                    <p class="hint">
                        Customize each gift item individually. Mix money and
                        physical gifts in the same gift pool.
                    </p>

                    <TransitionGroup
                        name="gift-list"
                        tag="div"
                        class="gift-list"
                    >
                        <article
                            v-for="(gift, index) in form.gifts"
                            :key="gift._key"
                            class="gift-item"
                        >
                            <div class="gift-item-header">
                                <h3>Gift item {{ index + 1 }}</h3>

                                <button
                                    type="button"
                                    class="delete-gift-button"
                                    @click="removeGift(index)"
                                    title="Remove this gift"
                                    aria-label="Remove this gift"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="18"
                                        height="18"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <polyline
                                            points="3 6 5 6 21 6"
                                        ></polyline>
                                        <path d="M19 6l-1 14H6L5 6"></path>
                                        <path d="M10 11v6"></path>
                                        <path d="M14 11v6"></path>
                                        <path d="M9 6V4h6v2"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="">
                                <label :for="`gift-type-${index}`"
                                    >Gift type:</label
                                >
                                <select
                                    :id="`gift-type-${index}`"
                                    v-model="gift.gift_type"
                                    required
                                >
                                    <option value="" disabled>
                                        Choose gift type
                                    </option>
                                    <option value="money">💵 Money</option>
                                    <option value="custom">
                                        🎁 Custom gift
                                    </option>
                                </select>
                            </div>

                            <template v-if="gift.gift_type === 'money'">
                                <div class="">
                                    <label :for="`amount-${index}`"
                                        >Money amount (₱)</label
                                    >
                                    <input
                                        :id="`amount-${index}`"
                                        v-model="gift.amount"
                                        type="number"
                                        min="1"
                                        step="1"
                                        placeholder="e.g. 100"
                                        required
                                    />
                                </div>
                                <label :for="`description-${index}`">
                                    Description (optional)
                                </label>
                                <textarea
                                    :id="`description-${index}`"
                                    v-model="gift.description"
                                    placeholder="Add a little detail..."
                                ></textarea>

                                <label :for="`quantity-${index}`"
                                    >Available quantity</label
                                >
                                <input
                                    :id="`quantity-${index}`"
                                    v-model.number="gift.quantity"
                                    type="number"
                                    min="1"
                                    max="10000"
                                    required
                                />
                            </template>

                            <template v-else-if="gift.gift_type === 'custom'">
                                <div class="">
                                    <label :for="`name-${index}`"
                                        >*Gift name:</label
                                    >
                                    <input
                                        :id="`name-${index}`"
                                        v-model="gift.name"
                                        type="text"
                                        maxlength="191"
                                        placeholder="e.g. Toy, chocolates"
                                        required
                                    />
                                </div>

                                <label :for="`description-${index}`">
                                    Description (optional)
                                </label>
                                <textarea
                                    :id="`description-${index}`"
                                    v-model="gift.description"
                                    placeholder="Add a little detail..."
                                ></textarea>

                                <label :for="`quantity-${index}`"
                                    >Available quantity</label
                                >
                                <input
                                    :id="`quantity-${index}`"
                                    v-model.number="gift.quantity"
                                    type="number"
                                    min="1"
                                    max="10000"
                                    required
                                />
                            </template>
                        </article>
                    </TransitionGroup>

                    <div v-if="form.gifts.length === 0" class="empty-gifts">
                        <span class="empty-gifts-icon">🎁</span>
                        <p>No gift items yet.</p>
                        <span
                            >Increase the gift item count above to add your
                            first gift.</span
                        >
                    </div>
                </div>

                <div class="stock-summary">
                    <p>
                        Total gift openings:
                        <strong>{{ totalOpenings }}</strong>
                    </p>
                    <p>
                        Total gifts in stock:
                        <strong>{{ totalStock }}</strong>
                    </p>
                    <p v-if="totalStock < totalOpenings" class="error-text">
                        You need at least {{ totalOpenings }} gifts in stock.
                    </p>
                </div>

                <div class="privacy-note">
                    <strong>🔒 Your privacy matters</strong>
                    <p>
                        No personal information about your godchildren is
                        required. The game uses randomly generated access codes
                        to track gift openings.
                    </p>
                </div>

                <p v-if="errorMessage" class="error-text">
                    {{ errorMessage }}
                </p>

                <div
                    v-if="Object.keys(validationErrors).length"
                    class="error-text"
                >
                    <p
                        v-for="(messages, field) in validationErrors"
                        :key="field"
                    >
                        {{ messages[0] }}
                    </p>
                </div>

                <button
                    type="submit"
                    class="submit-button"
                    :disabled="loading || totalStock < totalOpenings"
                >
                    {{
                        loading
                            ? "Creating your machine..."
                            : "🎁 Create My Gachapon"
                    }}
                </button>
            </form>

            <section v-if="step === 'review'" class="review-section">
                <div class="review-header">
                    <span class="review-icon">🎄</span>
                    <p class="review-eyebrow">CHRISTMAS GACHAPON</p>
                    <h2>Review Your Gachapon</h2>
                    <p>
                        Check your game settings and gifts before creating your
                        machine.
                    </p>
                </div>

                <!-- Overview -->
                <div class="review-stats">
                    <div class="review-stat">
                        <span>👥</span>
                        <div>
                            <small>Godchildren</small>
                            <strong>{{ form.number_of_godchildren }}</strong>
                        </div>
                    </div>

                    <div class="review-stat">
                        <span>🎲</span>
                        <div>
                            <small>Total Openings</small>
                            <strong>{{ totalOpenings }}</strong>
                        </div>
                    </div>

                    <div class="review-stat">
                        <span>🎁</span>
                        <div>
                            <small>Gift Items</small>
                            <strong>{{ form.gifts.length }}</strong>
                        </div>
                    </div>

                    <div class="review-stat">
                        <span>📦</span>
                        <div>
                            <small>Total Stock</small>
                            <strong>{{ totalStock }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Game settings -->
                <div class="review-panel">
                    <div class="review-panel-heading">
                        <div>
                            <span class="review-kicker">YOUR MACHINE</span>
                            <h3>Game Settings</h3>
                        </div>

                        <button
                            type="button"
                            class="review-edit-button"
                            @click="editSection('settings')"
                            :disabled="loading"
                        >
                            ✏️ Edit
                        </button>
                    </div>

                    <div class="review-settings-grid">
                        <div class="review-setting">
                            <span class="review-setting-icon">
                                {{
                                    form.godparent_role === "godfather"
                                        ? "🤵"
                                        : "👩‍🦰"
                                }}
                            </span>
                            <div>
                                <small>Godparent Role</small>
                                <strong>
                                    {{
                                        form.godparent_role === "godfather"
                                            ? "Godfather (Ninong)"
                                            : "Godmother (Ninang)"
                                    }}
                                </strong>
                            </div>
                        </div>

                        <div class="review-setting">
                            <span class="review-setting-icon">👨‍👩‍👧</span>
                            <div>
                                <small>Number of Players</small>
                                <strong
                                    >{{
                                        form.number_of_godchildren
                                    }}
                                    godchildren</strong
                                >
                            </div>
                        </div>

                        <div class="review-setting">
                            <span class="review-setting-icon">🔄</span>
                            <div>
                                <small>Openings Per Godchild</small>
                                <strong
                                    >{{
                                        form.openings_per_godchild
                                    }}
                                    times</strong
                                >
                            </div>
                        </div>

                        <div class="review-setting">
                            <span class="review-setting-icon">🎯</span>
                            <div>
                                <small>Required Gift Stock</small>
                                <strong>{{ totalOpenings }} gifts</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gift collection -->
                <div class="review-panel">
                    <div class="review-panel-heading">
                        <div>
                            <span class="review-kicker">THE SURPRISES</span>
                            <h3>Your Gift Collection</h3>
                            <p class="review-description">
                                Review each gift and its available quantity.
                            </p>
                        </div>

                        <button
                            type="button"
                            class="review-edit-button"
                            @click="editSection('gifts')"
                            :disabled="loading"
                        >
                            ✏️ Edit Gifts
                        </button>
                    </div>

                    <div v-if="form.gifts.length" class="review-gift-grid">
                        <article
                            v-for="(gift, index) in form.gifts"
                            :key="gift._key"
                            class="review-gift-card"
                        >
                            <div class="review-gift-card-top">
                                <span
                                    class="review-gift-emoji"
                                    :class="
                                        gift.gift_type === 'money'
                                            ? 'money'
                                            : 'custom'
                                    "
                                >
                                    {{
                                        gift.gift_type === "money" ? "💵" : "🎁"
                                    }}
                                </span>

                                <span class="review-gift-number">
                                    GIFT
                                    {{ String(index + 1).padStart(2, "0") }}
                                </span>
                            </div>

                            <span
                                class="review-gift-badge"
                                :class="
                                    gift.gift_type === 'money'
                                        ? 'money'
                                        : 'custom'
                                "
                            >
                                {{
                                    gift.gift_type === "money"
                                        ? "Money Gift"
                                        : "Custom Gift"
                                }}
                            </span>

                            <h4>
                                {{
                                    gift.gift_type === "money"
                                        ? "Cash Gift"
                                        : gift.name || "Untitled Gift"
                                }}
                            </h4>

                            <p class="review-gift-description">
                                {{
                                    gift.description ||
                                    "No description provided."
                                }}
                            </p>

                            <div class="review-gift-divider"></div>

                            <div class="review-gift-bottom">
                                <div>
                                    <small>
                                        {{
                                            gift.gift_type === "money"
                                                ? "Amount per gift"
                                                : "Available quantity"
                                        }}
                                    </small>

                                    <strong
                                        v-if="gift.gift_type === 'money'"
                                        class="review-money-amount"
                                    >
                                        ₱{{
                                            Number(
                                                gift.amount || 0,
                                            ).toLocaleString("en-PH", {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2,
                                            })
                                        }}
                                    </strong>

                                    <strong v-else class="review-quantity">
                                        {{ gift.quantity }} pcs
                                    </strong>
                                </div>

                                <span class="review-stock-pill">
                                    {{ gift.quantity }} in stock
                                </span>
                            </div>
                        </article>
                    </div>

                    <div v-else class="review-empty">
                        <span>🎁</span>
                        <strong>No gifts added yet</strong>
                        <p>Return to the form to configure your gifts.</p>
                    </div>
                </div>

                <!-- Stock validation -->
                <div
                    class="review-stock-notice"
                    :class="{ insufficient: totalStock < totalOpenings }"
                >
                    <span class="review-stock-notice-icon">
                        {{ totalStock >= totalOpenings ? "✓" : "!" }}
                    </span>

                    <div>
                        <strong>
                            {{
                                totalStock >= totalOpenings
                                    ? "Your gift stock is sufficient!"
                                    : "Additional gifts are needed"
                            }}
                        </strong>

                        <p v-if="totalStock >= totalOpenings">
                            You have {{ totalStock }} gifts for
                            {{ totalOpenings }} planned openings.
                        </p>

                        <p v-else>
                            Add {{ totalOpenings - totalStock }} more gifts to
                            cover all planned openings.
                        </p>
                    </div>
                </div>

                <!-- Errors -->
                <div v-if="errorMessage" class="error-text review-errors">
                    {{ errorMessage }}
                </div>

                <div
                    v-if="Object.keys(validationErrors).length"
                    class="error-text review-errors"
                >
                    <p
                        v-for="(messages, field) in validationErrors"
                        :key="field"
                    >
                        {{ messages[0] }}
                    </p>
                </div>

                <!-- Actions -->
                <div class="review-actions">
                    <button
                        type="button"
                        class="review-back-button"
                        @click="step = 'form'"
                        :disabled="loading"
                    >
                        ← Edit Setup
                    </button>

                    <button
                        type="button"
                        class="review-confirm-button"
                        @click="confirmCreate"
                        :disabled="
                            loading ||
                            totalStock < totalOpenings ||
                            form.gifts.length === 0
                        "
                    >
                        {{
                            loading
                                ? "⏳ Creating..."
                                : "🎁 Confirm & Create Machine"
                        }}
                    </button>
                </div>

                <p class="review-security-note">
                    🔒 Your machine will only be saved after you confirm its
                    creation.
                </p>
            </section>

            <section v-if="step === 'done' && result" class="result-section">
                <h2>🎉 Your machine is ready!</h2>
                <p>Your Gachapon has been created successfully.</p>

                <div class="review-group">
                    <h3>🔒 Private Management Link</h3>
                    <p>
                        Keep this link private. Use it to manage your machine.
                    </p>

                    <div class="result-link">
                        <input :value="result.management_url" readonly />
                        <button
                            type="button"
                            @click="copyLink(result.management_url)"
                        >
                            {{
                                copiedLink === result.management_url
                                    ? "✓ Copied!"
                                    : "Copy"
                            }}
                        </button>
                    </div>
                </div>

                <div class="review-group">
                    <h3>🎁 Godchildren's Play Link</h3>
                    <p>
                        Share this link or let your godchildren scan the QR
                        code.
                    </p>

                    <div class="qr-container">
                        <QrcodeVue
                            :value="result.public_url"
                            :size="220"
                            level="H"
                        />
                    </div>

                    <div class="result-link">
                        <input :value="result.public_url" readonly />
                        <button
                            type="button"
                            @click="copyLink(result.public_url)"
                        >
                            {{
                                copiedLink === result.public_url
                                    ? "✓ Copied!"
                                    : "Copy"
                            }}
                        </button>
                    </div>
                </div>

                <Transition name="copy-toast">
                <div v-if="copiedLink" class="copy-toast">
                    ✅ Link copied successfully!
                </div>
            </Transition>
            </section>
        </section>
    </main>
</template>

<script setup>
import { computed, reactive, ref, watch } from "vue";
import QrcodeVue from "qrcode.vue";
import ChristmasEffects from "../components/ChristmasEffects.vue";

const step = ref("form");
const loading = ref(false);
const errorMessage = ref("");
const validationErrors = ref({});
const result = ref(null);
const highlightedSection = ref("");

function editSection(section) {
    step.value = "form";
    highlightedSection.value = section;

    setTimeout(() => {
        const targetId =
            section === "gifts" ? "gifts-section" : "settings-section";

        const target = document.getElementById(targetId);

        if (target) {
            target.scrollIntoView({
                behavior: "smooth",
                block: "center",
            });
        }
    }, 100);

    setTimeout(() => {
        if (highlightedSection.value === section) {
            highlightedSection.value = "";
        }
    }, 3000);
}

const form = reactive({
    godparent_role: "godfather",
    number_of_gifts: 0,
    number_of_godchildren: 0,
    openings_per_godchild: 0,
    gifts: [],
});

let nextGiftKey = 0;

function createGift() {
    return {
        _key: nextGiftKey++,
        gift_type: "",
        name: "",
        description: "",
        amount: "",
        quantity: 0,
    };
}

function removeGift(index) {
    form.gifts.splice(index, 1);

    form.number_of_gifts = form.gifts.length;
}

function syncGiftItems(count) {
    const safeCount = Math.max(0, Math.min(30, Number(count) || 0));

    while (form.gifts.length < safeCount) {
        form.gifts.push(createGift());
    }

    while (form.gifts.length > safeCount) {
        form.gifts.pop();
    }
}

syncGiftItems(form.number_of_gifts);

watch(() => form.number_of_gifts, syncGiftItems);

const totalOpenings = computed(() => {
    return (
        Math.max(0, Number(form.number_of_godchildren) || 0) *
        Math.max(0, Number(form.openings_per_godchild) || 0)
    );
});

const totalStock = computed(() => {
    return form.gifts.reduce(
        (total, gift) => total + (Number(gift.quantity) || 0),
        0,
    );
});

function submitForm() {
    errorMessage.value = "";
    validationErrors.value = {};

    if (form.gifts.length === 0) {
        errorMessage.value = "Please add at least one gift item.";
        return;
    }

    if (totalStock.value < totalOpenings.value) {
        errorMessage.value = "Your gift stock must cover all planned openings.";
        return;
    }

    step.value = "review";
}

async function confirmCreate() {
    loading.value = true;
    errorMessage.value = "";
    validationErrors.value = {};

    try {
        const response = await fetch("/setup", {
            method: "POST",
            credentials: "same-origin",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-CSRF-TOKEN":
                    document.querySelector('meta[name="csrf-token"]')
                        ?.content ?? "",
            },
            body: JSON.stringify(form),
        });

        const data = await response.json();

        if (!response.ok) {
            if (response.status === 422 && data.errors) {
                validationErrors.value = data.errors;
                errorMessage.value =
                    "Please correct the errors before creating your machine.";
                step.value = "form";
            } else {
                errorMessage.value =
                    data.message || "Unable to create your machine.";
            }

            return;
        }

        result.value = data;
        step.value = "done";
    } catch (error) {
        errorMessage.value =
            "Could not connect to the server. Please try again.";
    } finally {
        loading.value = false;
    }
}

const copiedLink = ref("");

async function copyLink(url) {
    try {
        await navigator.clipboard.writeText(url);
        copiedLink.value = url;

        setTimeout(() => {
            if (copiedLink.value === url) {
                copiedLink.value = "";
            }
        }, 2000);
    } catch {
        errorMessage.value =
            "Copy failed. Please select and copy the link manually.";
    }
}
</script>
