
<template>
        <ChristmasEffects />

    <main class="app">
        <section class="welcome-card">
            <p class="eyebrow">✨ A LITTLE CHRISTMAS MAGIC ✨</p>

            <h1>Christmas Gachapon</h1>

            <p class="subtitle">
                Every capsule holds a little surprise.
            </p>

            <button class="start-button" @click="start">
                🎁 Let's Get Started
            </button>

            <p v-if="message" class="message">{{ message }}</p>
        </section>
    </main>
</template>

<script setup>
import ChristmasEffects from '../components/ChristmasEffects.vue';

function start() {
    window.location.href = '/setup';
}


</script>
