<template>
    <div class="christmas-effects" aria-hidden="true">
        <!-- Falling snowflakes -->
        <span
            v-for="flake in snowflakes"
            :key="flake.id"
            class="snowflake"
            :style="{
                left: flake.left,
                animationDelay: flake.delay,
                animationDuration: flake.duration,
                fontSize: flake.size,
                opacity: flake.opacity
            }"
        >
            ❄
        </span>

        <!-- Christmas string lights -->
        <div class="christmas-lights">
            <div
                v-for="light in christmasLights"
                :key="light.id"
                class="christmas-light"
                :class="`light-${light.color}`"
                :style="{
                    left: light.left,
                    animationDelay: light.delay
                }"
            >
                <span class="light-bulb"></span>
            </div>
        </div>

    </div>
</template>

<script setup>
const snowflakes = Array.from({ length: 50 }, (_, index) => ({
    id: index,
    left: `${(index * 37 + 11) % 100}%`,
    delay: `${-((index * 1.7) % 18)}s`,
    duration: `${12 + (index % 9) * 2}s`,
    size: `${12 + (index % 5) * 5}px`,
    opacity: 0.3 + (index % 5) * 0.12,
}));

const christmasLights = Array.from({ length: 24 }, (_, index) => ({
    id: index,
    left: `${(index / 23) * 100}%`,
    color: ['red', 'green', 'yellow', 'blue'][index % 4],
    delay: `${-(index % 4) * 0.4}s`,
}));
</script>


<style scoped>
.christmas-effects {
    position: fixed;
    inset: 0;
    overflow: hidden;
    pointer-events: none;
    z-index: 1;
}

/* Falling snow */
.snowflake {
    position: absolute;
    top: -40px;
    color: #ffffff;
    user-select: none;
    text-shadow: 0 0 8px rgba(255, 255, 255, 0.65);
    animation-name: snowfall;
    animation-timing-function: linear;
    animation-iteration-count: infinite;
    will-change: transform;
}

/* Christmas lights hanging across the top */
.christmas-lights {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 38px;
    z-index: 2;
    pointer-events: none;
    border-top: 2px solid #263d2d;
}

/* Individual light holders */
.christmas-light {
    position: absolute;
    top: -2px;
    transform: translateX(-50%);
    width: 12px;
    height: 22px;
    border-top: 2px solid #263d2d;
    display: flex;
    justify-content: center;
}

/* Bulbs */
.light-bulb {
    position: absolute;
    top: 9px;
    width: 11px;
    height: 15px;
    border-radius: 50% 50% 45% 45%;
    animation: christmas-glow 1.8s ease-in-out infinite alternate;
}

/* Christmas colors */
.light-red .light-bulb {
    background: #ff3548;
    box-shadow: 0 0 8px #ff3548, 0 0 16px rgba(255, 53, 72, 0.55);
}

.light-green .light-bulb {
    background: #39e875;
    box-shadow: 0 0 8px #39e875, 0 0 16px rgba(57, 232, 117, 0.55);
}

.light-yellow .light-bulb {
    background: #ffdc62;
    box-shadow: 0 0 8px #ffdc62, 0 0 16px rgba(255, 220, 98, 0.55);
}

.light-blue .light-bulb {
    background: #4fc8ff;
    box-shadow: 0 0 8px #4fc8ff, 0 0 16px rgba(79, 200, 255, 0.55);
}

/* Soft glowing effect */
@keyframes christmas-glow {
    from {
        opacity: 0.65;
        filter: brightness(0.8);
        transform: scale(0.9);
    }

    to {
        opacity: 1;
        filter: brightness(1.3);
        transform: scale(1.08);
    }
}

/* Respect reduced-motion preferences */
@media (prefers-reduced-motion: reduce) {
    .snowflake,
    .christmas-light .light-bulb {
        animation: none;
    }
}

@keyframes twinkle {
    0%, 100% {
        opacity: 0.15;
        transform: scale(0.7) rotate(0deg);
    }

    50% {
        opacity: 0.9;
        transform: scale(1.15) rotate(20deg);
    }
}

@keyframes snowfall {
    0% {
        transform: translate3d(0, -5vh, 0) rotate(0deg);
    }

    50% {
        transform: translate3d(35px, 50vh, 0) rotate(180deg);
    }

    100% {
        transform: translate3d(-25px, 110vh, 0) rotate(360deg);
    }
}

/* Reduce movement for users who prefer less animation */
@media (prefers-reduced-motion: reduce) {
    .snowflake,
    .sleigh {
        animation: none;
        display: none;
    }
}
</style>