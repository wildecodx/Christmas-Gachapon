
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🧧</text></svg>">

    <title>A Christmas Surprise! 🎄</title>

    @vite(['resources/css/app.css'])

    <style>
        :root {
            --green: #174b38;
            --green-dark: #102d26;
            --red: #b93236;
            --red-dark: #8e252b;
            --gold: #e8bc62;
            --cream: #fffaf0;
            --muted: #778278;
        }

        * {
            box-sizing: border-box;
        }

        [hidden] {
            display: none !important;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'DM Sans', sans-serif;
            color: var(--cream);
            background:
                radial-gradient(ellipse at 50% 0%, #286044 0%, transparent 58%),
                linear-gradient(145deg, #102d26, #081c17);
            overflow-x: hidden;
        }

        button {
            font: inherit;
        }

        button:focus-visible {
            outline: 3px solid var(--gold);
            outline-offset: 5px;
        }

        .christmas-scene {
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 38px 18px;
            position: relative;
            isolation: isolate;
            overflow: hidden;
        }

        .snowfall,
        .snowfall::before,
        .snowfall::after {
            position: absolute;
            inset: -100% 0 0;
            pointer-events: none;
            background-repeat: repeat;
            background-size: 180px 180px;
            background-image:
                radial-gradient(2px 2px at 25px 40px, #fff 98%, transparent),
                radial-gradient(2px 2px at 90px 115px, #e8bc62 98%, transparent),
                radial-gradient(3px 3px at 155px 65px, #fff 98%, transparent);
            animation: snowfall 18s linear infinite;
            z-index: -1;
        }

        .snowfall::before,
        .snowfall::after {
            content: '';
            inset: 0;
            background-position: 70px 30px;
            animation-duration: 25s;
            opacity: .45;
        }

        .snowfall::after {
            background-position: 120px 80px;
            animation-duration: 32s;
            opacity: .25;
        }

        @keyframes snowfall {
            from { transform: translateY(-10%); }
            to { transform: translateY(40%); }
        }

        .scene-decoration {
            position: absolute;
            color: var(--gold);
            opacity: .65;
            pointer-events: none;
            animation: twinkle 3s ease-in-out infinite;
        }

        .decoration-one {
            top: 13%;
            left: 9%;
            font-size: 27px;
        }

        .decoration-two {
            top: 25%;
            right: 10%;
            font-size: 22px;
            animation-delay: .8s;
        }

        .decoration-three {
            bottom: 14%;
            left: 12%;
            font-size: 20px;
            animation-delay: 1.4s;
        }

        .decoration-four {
            bottom: 20%;
            right: 9%;
            font-size: 30px;
            animation-delay: .4s;
        }

        @keyframes twinkle {
            0%, 100% { opacity: .35; transform: scale(.85); }
            50% { opacity: .95; transform: scale(1.15); }
        }

        .invitation {
            width: 100%;
            max-width: 650px;
            position: relative;
            z-index: 2;
            text-align: center;
        }

        .eyebrow {
            margin: 0 0 14px;
            color: var(--gold);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 4px;
            text-transform: uppercase;
        }

        .main-title {
            margin: 0;
            font-family: 'Playfair Display', serif;
            font-size: clamp(35px, 7vw, 54px);
            line-height: 1.12;
            font-weight: 700;
        }

        .intro {
            max-width: 420px;
            margin: 16px auto 28px;
            color: #d5e2d8;
            font-size: 15px;
            line-height: 1.8;
        }


        /* Envelope */

        .envelope-stage {
            height: 270px;
            display: flex;
            align-items: center;
            justify-content: center;
            perspective: 1000px;
            margin: 0 auto 15px;
        }

        /* Hover movement is applied to this wrapper, not the envelope itself. */
        .envelope-tilt {
            width: min(340px, 82vw);
            height: 205px;
            position: relative;
            transform-style: preserve-3d;
            transition: transform 180ms ease-out;
            will-change: transform;
        }

        .envelope {
            position: absolute;
            inset: 0;
            transform-style: preserve-3d;
            filter: drop-shadow(0 20px 24px #0005);
            animation: envelope-float 3s ease-in-out infinite;
        }

        @keyframes envelope-float {
            0%, 100% {
                translate: 0 0;
            }

            50% {
                translate: 0 -8px;
            }
        }

        .envelope-back {
            position: absolute;
            inset: 0;
            z-index: 1;
            overflow: hidden;
            border: 2px solid #e8bc62;
            border-radius: 9px;
            background: #f4e7c9;
        }

        .letter-preview {
            position: absolute;
            z-index: 2;
            left: 5%;
            top: 3%;
            width: 90%;
            height: 94%;
            padding: 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--green);
            background: var(--cream);
            border: 1px solid #e8bc62;
            transition:
                top 900ms cubic-bezier(.2, .8, .25, 1),
                transform 900ms cubic-bezier(.2, .8, .25, 1);
        }

        .preview-holly {
            font-size: 24px;
            margin-bottom: 7px;
        }

        .letter-preview strong {
            font-family: 'Playfair Display', serif;
            font-size: 23px;
        }

        .letter-preview small {
            color: var(--muted);
            margin-top: 8px;
        }

        .envelope-front {
            position: absolute;
            inset: 0;
            z-index: 3;
            border-radius: 8px;
            background: #b93236;
            clip-path: polygon(0 0, 50% 57%, 100% 0, 100% 100%, 0 100%);
        }

        .envelope-left {
            position: absolute;
            inset: 0;
            z-index: 4;
            border-radius: 8px;
            background: #d64a48;
            clip-path: polygon(0 0, 50% 57%, 0 100%);
        }

        .envelope-right {
            position: absolute;
            inset: 0;
            z-index: 4;
            border-radius: 8px;
            background: #9f292f;
            clip-path: polygon(100% 0, 50% 57%, 100% 100%);
        }

        .envelope-bottom {
            position: absolute;
            inset: 0;
            z-index: 5;
            border-radius: 8px;
            background: #c83c40;
            clip-path: polygon(0 100%, 50% 47%, 100% 100%);
        }

        /* The flap rotates backward from its top edge. */
        .envelope-flap {
            position: absolute;
            inset: 0;
            z-index: 8;
            border-radius: 8px;
            background: #e0524e;
            clip-path: polygon(0 0, 100% 0, 50% 61%);
            transform-origin: 50% 0%;
            transform: rotateX(0deg);
            backface-visibility: hidden;
            transition:
                transform 750ms cubic-bezier(.4, 0, .2, 1),
                opacity 250ms ease;
        }

        .wax-seal {
            position: absolute;
            z-index: 9;
            top: 43%;
            left: 50%;
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #f7d98f;
            border-radius: 50%;
            background: var(--gold);
            color: var(--red-dark);
            font-size: 27px;
            transform: translate(-50%, -50%);
            box-shadow: 0 4px 12px #0003;
            transition:
                opacity 250ms ease,
                transform 300ms ease;
        }

        /* Opening sequence */
        .envelope.opened {
            animation: none;
        }

        /* Hide the flap after it swings open. */
        .envelope.opened .envelope-flap {
            transform: rotateX(180deg);
            opacity: 0;
            pointer-events: none;
        }

        /* Hide the wax seal. */
        .envelope.opened .wax-seal {
            opacity: 0;
            transform: translate(-50%, -50%) scale(.5);
            pointer-events: none;
        }

        /*
        * Raise the letter above the envelope's front layers.
        * This is what lets the letter slide out visibly.
        */
        .envelope.opened .letter-preview {
            z-index: 10;
            top: -85px;
            transform: translateY(-12px);
        }

        /* Prevent hover interaction while the envelope is opening. */
        .envelope-tilt.is-opening {
            pointer-events: none;
        }

        @media (max-width: 480px) {
            .envelope-stage {
                height: 240px;
            }

            .envelope-tilt {
                width: min(310px, 84vw);
                height: 185px;
            }

            .envelope.opened .letter-preview {
                top: -65px;
            }
        }

        /* Buttons */

        .primary-button {
            min-height: 52px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            padding: 14px 27px;
            border: 1px solid #e75b57;
            border-radius: 999px;
            background: var(--red);
            color: #fff;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 6px 0 var(--red-dark);
            transition: transform .2s, box-shadow .2s, opacity .2s;
        }

        .primary-button:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 0 var(--red-dark);
        }

        .primary-button:active:not(:disabled) {
            transform: translateY(3px);
            box-shadow: 0 3px 0 var(--red-dark);
        }

        .primary-button:disabled {
            opacity: .55;
            cursor: not-allowed;
        }

        .open-instruction {
            margin: 24px 0 0;
            color: #d5e2d8;
            font-size: 13px;
            letter-spacing: 1px;
        }

        /* Full letter */

        .letter-overlay {
            position: fixed;
            inset: 0;
            z-index: 20;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 22px 14px;
            overflow-y: auto;
            background: rgb(4 20 15 / 82%);
            backdrop-filter: blur(7px);
            opacity: 0;
            transition: opacity .45s ease;
        }

        .letter-overlay.visible {
            opacity: 1;
        }

        .letter-card {
            position: relative;
            width: 100%;
            max-width: 560px;
            margin: auto;
            padding: 38px clamp(22px, 6vw, 48px);
            border: 2px solid var(--gold);
            border-radius: 5px;
            outline: 7px solid rgb(232 188 98 / 12%);
            background:
                radial-gradient(ellipse at top, #fffef8, transparent 65%),
                var(--cream);
            color: #24372d;
            box-shadow: 0 25px 80px #0007;
            transform: translateY(55px) scale(.92);
            opacity: 0;
        }

        .letter-overlay.visible .letter-card {
            animation: letter-arrive .75s cubic-bezier(.18,.8,.25,1) forwards;
        }

        @keyframes letter-arrive {
            0% {
                opacity: 0;
                transform: translateY(55px) scale(.92);
            }

            70% {
                opacity: 1;
                transform: translateY(-5px) scale(1.01);
            }

            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .letter-top-decoration {
            margin-bottom: 10px;
            color: var(--green);
            font-size: 28px;
            letter-spacing: 8px;
        }

        .letter-title {
            margin: 0 0 25px;
            color: var(--green);
            font-family: 'Playfair Display', serif;
            font-size: clamp(27px, 6vw, 36px);
            line-height: 1.2;
        }

        .letter-body {
            text-align: left;
            font-size: 15px;
            line-height: 1.85;
        }

        .letter-body p {
            margin: 0 0 15px;
        }

        .letter-body .greeting {
            color: var(--green);
            font-weight: 800;
        }

        .code-panel {
            margin: 25px 0;
            padding: 22px 15px;
            border: 1px dashed var(--gold);
            border-radius: 12px;
            background: #174b38;
            color: var(--cream);
            text-align: center;
        }

        .code-label {
            margin: 0 0 12px;
            color: var(--gold);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 3px;
        }

        .personal-code {
            margin: 0 0 14px;
            font-family: 'Playfair Display', serif;
            font-size: clamp(23px, 6vw, 34px);
            font-weight: 800;
            letter-spacing: 3px;
            overflow-wrap: anywhere;
        }

        .code-placeholder {
            color: #fffaf0;
            font-size: 14px;
            line-height: 1.7;
        }

        .copy-button {
            padding: 9px 16px;
            border: 1px solid var(--gold);
            border-radius: 999px;
            background: transparent;
            color: var(--cream);
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
        }

        .copy-button:hover {
            background: rgb(255 255 255 / 10%);
        }

        .letter-signoff {
            margin-top: 24px;
            color: var(--green);
            font-family: 'Playfair Display', serif;
            font-size: 21px;
            font-weight: 700;
        }

        .letter-actions {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            gap: 17px;
            margin-top: 28px;
        }

        .letter-actions .primary-button {
            width: 100%;
        }

        .close-letter {
            border: 0;
            background: transparent;
            color: var(--muted);
            cursor: pointer;
            font-size: 13px;
        }

        .close-letter:hover {
            color: var(--green);
        }

        .status-message {
            margin: 12px 0 0;
            min-height: 20px;
            color: var(--green);
            font-size: 13px;
        }

        @media (max-width: 480px) {
            .christmas-scene {
                padding: 30px 18px;
            }

            .eyebrow {
                letter-spacing: 3px;
            }

            .envelope-stage {
                height: 240px;
            }

            .envelope {
                width: min(310px, 84vw);
                height: 185px;
            }

            .envelope.opened .letter-preview {
                top: -55px;
            }

            .letter-overlay {
                align-items: flex-start;
                padding: 14px 10px;
            }

            .letter-card {
                padding: 29px 20px;
            }

            .letter-body {
                font-size: 14px;
            }

            .personal-code {
                letter-spacing: 2px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                scroll-behavior: auto !important;
                transition-duration: .01ms !important;
            }
        }

        /* Copy toast notification */
        .copy-toast {
            position: fixed;
            bottom: 30px;
            left: 50%;
            z-index: 9999;

            display: flex;
            align-items: center;
            gap: 12px;

            padding: 14px 22px;
            border: 1px solid #e8bc62;
            border-radius: 12px;

            background: #174b38;
            color: #fffaf0;

            font-size: 14px;
            font-weight: 700;

            box-shadow: 0 10px 30px rgb(0 0 0 / 25%);

            opacity: 0;
            visibility: hidden;
            transform: translate(-50%, 20px);

            transition:
                opacity 0.3s ease,
                transform 0.3s ease,
                visibility 0.3s ease;

            pointer-events: none;
            white-space: nowrap;
        }

        .copy-toast.show {
            opacity: 1;
            visibility: visible;
            transform: translate(-50%, 0);
        }

        .toast-icon {
            display: flex;
            align-items: center;
            justify-content: center;

            width: 25px;
            height: 25px;

            border-radius: 50%;
            background: #e8bc62;
            color: #174b38;

            font-size: 15px;
            font-weight: 900;
        }

        @media (max-width: 480px) {
            .copy-toast {
                bottom: 20px;
                max-width: calc(100vw - 32px);
                padding: 12px 16px;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <main class="christmas-scene">
        <div class="snowfall" aria-hidden="true"></div>

        <span class="scene-decoration decoration-one" aria-hidden="true">✦</span>
        <span class="scene-decoration decoration-two" aria-hidden="true">❄</span>
        <span class="scene-decoration decoration-three" aria-hidden="true">✧</span>
        <span class="scene-decoration decoration-four" aria-hidden="true">❄</span>

        <section class="invitation" aria-labelledby="page-title">
            <p class="eyebrow">A little Christmas magic</p>

            <h1 class="main-title" id="page-title">
                A Christmas<br>Surprise!
            </h1>

            <p class="intro">
                Someone very special has prepared a Christmas gift just for you.
                Open your letter to discover what awaits!
            </p>

            <div class="envelope-stage">
            <div class="envelope-tilt" id="envelope-tilt">
                <div class="envelope" id="envelope" aria-label="A sealed Christmas envelope">
                    <div class="envelope-back"></div>

                    <div class="letter-preview">
                        <div class="preview-holly">🎄 ✨ 🎄</div>
                        <strong>A Special Letter</strong>
                        <small>Made with love, just for you</small>
                    </div>

                    <div class="envelope-front"></div>
                    <div class="envelope-left"></div>
                    <div class="envelope-right"></div>
                    <div class="envelope-bottom"></div>
                    <div class="envelope-flap"></div>

                    <div class="wax-seal" aria-hidden="true">♥</div>
                </div>
            </div>
            </div>

            <button class="primary-button" id="open-envelope" type="button">
                <span aria-hidden="true">✉</span>
                OPEN ENVELOPE
            </button>

            <p class="open-instruction">
                ✨ Your Christmas surprise is waiting ✨
            </p>
        </section>
    </main>

    <section
        class="letter-overlay"
        id="letter-overlay"
        role="dialog"
        aria-modal="true"
        aria-labelledby="letter-title"
        hidden
    >
        <article class="letter-card">
            <div class="letter-top-decoration" aria-hidden="true">
                ❄ ✦ ❄
            </div>

            <h2 class="letter-title" id="letter-title">
                A Christmas Letter
            </h2>

            <div class="letter-body">
                <p class="greeting">Dear Godchild (Inaanak),</p>

                <p>
                    Merry Christmas! 🎄
                </p>

                <p>
                    This gift comes to you from your beloved
                    <strong>
                        {{ $machine->godparent_role === 'godfather' ? 'Ninong' : 'Ninang' }}
                    </strong>,
                    who has prepared a special Christmas surprise just for you!
                </p>

                <p>
                    To discover your gift, you will need your special code.
                    Keep it safe, because you'll need it to unlock your
                    Christmas Gachapon!
                </p>
            </div>

            <div class="code-panel">
                <p class="code-label">YOUR SPECIAL CODE</p>

                @if (!empty($godchild_code))
                    <p class="personal-code" id="personal-code">{{ $godchild_code }}</p>

                    <button class="copy-button" id="copy-code" type="button">
                        Copy my code
                    </button>
                @else
                    <p class="code-placeholder">
                        Every special code for this Christmas surprise
                        has already been claimed.
                    </p>
                @endif
            </div>

            <div class="letter-body">
                <p>
                    Once you have your code, proceed to the Gachapon machine,
                    enter it, and let the Christmas magic reveal your gift!
                </p>

                <p>Wishing you a Christmas filled with joy, laughter, and love.</p>
            </div>

            <div class="letter-signoff">
                With love and Christmas wishes ❤️
            </div>

            <div class="letter-actions">
                @if (!empty($godchild_code))
                    <button
                        class="primary-button"
                        id="proceed-button"
                        type="button"
                        data-play-url="{{ route('gachapon.machine', ['token' => $machine->public_token]) }}"
                    >
                        🎁 Generate your gift
                    </button>
                @else
                    <button class="primary-button" type="button" disabled>
                        🎁 Generate your gift
                    </button>
                @endif

                <button class="close-letter" id="close-letter" type="button">
                    Close letter
                </button>

                <p class="status-message" id="status-message" role="status" aria-live="polite"></p>
            </div>
        </article>
    </section>

<div id="copy-toast" class="copy-toast" role="status" aria-live="polite">
    <span class="toast-icon">✓</span>
    <span id="toast-message">Your code has been copied!</span>
</div>
<script>
    (() => {
        const envelope = document.getElementById('envelope');
        const envelopeTilt = document.getElementById('envelope-tilt');
        const openButton = document.getElementById('open-envelope');
        const overlay = document.getElementById('letter-overlay');
        const closeButton = document.getElementById('close-letter');
        const copyButton = document.getElementById('copy-code');
        const codeElement = document.getElementById('personal-code');
        const statusMessage = document.getElementById('status-message');
        const proceedButton = document.getElementById('proceed-button');

        const supportsHover = window.matchMedia(
            '(hover: hover) and (pointer: fine)'
        ).matches;

        let isOpening = false;
        let isClosing = false;
        let previousFocus = null;
        let openTimer = null;
        let focusTimer = null;
        let closeTimer = null;

        /*
         * Hover effect:
         * Move the cursor over different parts of the envelope
         * to tilt it toward the cursor's position.
         */
        if (supportsHover && envelopeTilt) {
            envelopeTilt.addEventListener('pointermove', (event) => {
                if (
                    isOpening ||
                    isClosing ||
                    envelope.classList.contains('opened')
                ) {
                    return;
                }

                const rect = envelopeTilt.getBoundingClientRect();

                const x = (event.clientX - rect.left) / rect.width;
                const y = (event.clientY - rect.top) / rect.height;

                // Left/right cursor movement.
                const rotateY = (x - 0.5) * 24;

                // Top/bottom cursor movement.
                const rotateX = (0.5 - y) * 18;

                envelopeTilt.style.transform =
                    `rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
            });

            envelopeTilt.addEventListener('pointerleave', () => {
                if (!isOpening && !isClosing) {
                    envelopeTilt.style.transform =
                        'rotateX(0deg) rotateY(0deg)';
                }
            });
        }

        /*
         * Open the envelope.
         */
        function openLetter() {
            if (isOpening || isClosing || !overlay.hidden) {
                return;
            }

            isOpening = true;
            previousFocus = document.activeElement;

            openButton.disabled = true;
            openButton.innerHTML = '✉ OPENING YOUR LETTER...';

            // Reset the tilt before the opening animation.
            envelopeTilt.classList.add('is-opening');
            envelopeTilt.style.transform =
                'rotateX(0deg) rotateY(0deg)';

            // Start opening the flap and sliding out the letter.
            envelope.classList.add('opened');

            // Show the full letter after the envelope animation.
            openTimer = window.setTimeout(() => {
                overlay.hidden = false;

                requestAnimationFrame(() => {
                    overlay.classList.add('visible');

                    focusTimer = window.setTimeout(() => {
                        closeButton.focus();
                        isOpening = false;
                    }, 500);
                });
            }, 850);
        }

        /*
         * Close the letter and reset the envelope.
         */
        function closeLetter() {
            if (overlay.hidden || isClosing) {
                return;
            }

            isClosing = true;
            isOpening = false;

            window.clearTimeout(openTimer);
            window.clearTimeout(focusTimer);
            window.clearTimeout(closeTimer);

            overlay.classList.remove('visible');

            closeTimer = window.setTimeout(() => {
                overlay.hidden = true;

                // Return the envelope to its original appearance.
                envelope.classList.remove('opened');
                envelopeTilt.classList.remove('is-opening');

                envelopeTilt.style.transform =
                    'rotateX(0deg) rotateY(0deg)';

                openButton.disabled = false;
                openButton.innerHTML =
                    '<span aria-hidden="true">✉</span> OPEN ENVELOPE';

                isClosing = false;

                if (previousFocus && typeof previousFocus.focus === 'function') {
                    previousFocus.focus();
                }
            }, 350);
        }

        openButton.addEventListener('click', openLetter);
        closeButton.addEventListener('click', closeLetter);

        // Close the letter by clicking outside the card.
        overlay.addEventListener('click', (event) => {
            if (event.target === overlay) {
                closeLetter();
            }
        });

        // Close the letter with Escape.
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !overlay.hidden) {
                closeLetter();
            }
        });

        /*
        * Copy the godchild's personal code and show a toast.
        */
        const copyToast = document.getElementById('copy-toast');
        const toastMessage = document.getElementById('toast-message');

        let toastTimer = null;

        function showToast(message) {
            if (!copyToast || !toastMessage) return;

            toastMessage.textContent = message;

            copyToast.classList.add('show');

            window.clearTimeout(toastTimer);

            toastTimer = window.setTimeout(() => {
                copyToast.classList.remove('show');
            }, 2500);
        }

        if (copyButton && codeElement) {
            copyButton.addEventListener('click', async () => {
                const code = codeElement.textContent.trim();

                try {
                    await navigator.clipboard.writeText(code);

                    copyButton.textContent = 'Copied! ✓';

                    showToast('Your code has been copied!');
                } catch (error) {
                    showToast('Unable to copy. Please copy your code manually.');
                }
            });
        }

        /*
         * Proceed to the Gachapon page.
         */
        if (proceedButton) {
            proceedButton.addEventListener('click', () => {
                const url = proceedButton.dataset.playUrl;

                if (url) {
                    window.location.href = url;
                }
            });
        }
    })();
</script>


</body>
</html>
