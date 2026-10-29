
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🧧</text></svg>">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
    <title>{{ $machine->name }} | Christmas Gachapon</title>

    @if (file_exists(public_path('hot')) || file_exists(public_path('build/manifest.json')))
        @vite('resources/js/prize-qr.js')
    @endif

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

        body {
            margin: 0;
            min-height: 100vh;
            overflow-x: hidden;
            font-family: Arial, Helvetica, sans-serif;
            color: var(--green-dark);
            background:
                radial-gradient(ellipse at top, #fffdf7 0%, transparent 60%),
                linear-gradient(135deg, #fffaf0, #e8f1e8);
        }

        button {
            font: inherit;
        }

        .page {
            width: min(100% - 32px, 900px);
            margin: 0 auto;
            padding: 32px 0 55px;
            text-align: center;
        }

        .top-label {
            color: var(--red);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 4px;
            text-transform: uppercase;
        }

        h1 {
            margin: 12px 0;
            color: var(--green);
            font-family: Georgia, serif;
            font-size: clamp(32px, 7vw, 52px);
        }

        .subtitle {
            margin: 0 auto;
            max-width: 520px;
            color: #637267;
            line-height: 1.7;
        }

        .player-card {
            display: inline-flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin-top: 22px;
            padding: 11px 17px;
            border: 1px solid #e4dfcd;
            border-radius: 50px;
            background: rgba(255, 255, 255, .8);
            font-size: 13px;
        }

        .player-card strong {
            color: var(--green);
        }

        .machine-area {
            position: relative;
            display: flex;
            justify-content: center;
            margin: 32px auto 22px;
            perspective: 1000px;
        }

        .machine {
            position: relative;
            width: min(100%, 380px);
            padding: 20px 20px 24px;
            border: 5px solid #9b292e;
            border-radius: 35px 35px 24px 24px;
            background: linear-gradient(135deg, #d94749, #a8202b 65%, #801b26);
            box-shadow:
                0 18px 0 #6f2024,
                0 28px 45px rgba(29, 57, 41, .18),
                inset 0 0 0 3px rgba(255, 255, 255, .14);
            transform-style: preserve-3d;
        }

        .machine-title {
            margin-bottom: 13px;
            color: #fff4ce;
            font-family: Georgia, serif;
            font-size: 24px;
            font-weight: bold;
            text-shadow: 0 2px 0 #751b22;
        }

        .machine-window {
            position: relative;
            height: 285px;
            overflow: hidden;
            border: 7px solid #f0c66e;
            border-radius: 46% 46% 25px 25px / 28% 28% 25px 25px;
            background:
                radial-gradient(circle at 50% 10%, #ffffff 0 2px, transparent 3px),
                radial-gradient(circle at 25% 30%, #ffffff 0 2px, transparent 3px),
                radial-gradient(circle at 80% 45%, #ffffff 0 2px, transparent 3px),
                linear-gradient(180deg, #e9f7ed, #b8dfca);
            background-size: 60px 60px, 80px 80px, 70px 70px, auto;
            box-shadow: inset 0 5px 18px rgba(18, 69, 48, .16);
        }

        .capsule-pile {
            position: absolute;
            inset: 0;
            display: flex;
            align-content: end;
            align-items: end;
            justify-content: center;
            flex-wrap: wrap;
            gap: 5px;
            padding: 24px 12px 10px;
            transition: opacity .25s ease;
        }

        .capsule {
            position: relative;
            width: 54px;
            height: 65px;
            flex: 0 0 54px;
            overflow: hidden;
            border: 2px solid rgba(115, 72, 31, .28);
            border-radius: 50%;
            background: linear-gradient(135deg, #fff1a8, #f0b83f);
            box-shadow: inset -5px -4px 0 rgba(0, 0, 0, .08),
                        0 3px 3px rgba(0, 0, 0, .1);
            transform-origin: center bottom;
        }

        .capsule::before {
            content: "";
            position: absolute;
            top: 49%;
            left: 0;
            width: 100%;
            height: 4px;
            background: rgba(255, 255, 255, .75);
        }

        .capsule:nth-child(3n + 1) {
            background: linear-gradient(135deg, #ffb8bb, #d93b48);
        }

        .capsule:nth-child(3n + 2) {
            background: linear-gradient(135deg, #b7f0ce, #23875c);
        }

        .capsule:nth-child(4n) {
            background: linear-gradient(135deg, #d8c4ff, #8054bf);
        }

        .capsule:nth-child(5n) {
            background: linear-gradient(135deg, #fff0a8, #e4a82e);
        }

        .machine.is-spinning .capsule-pile {
            animation: pile-shake .18s linear infinite alternate;
        }

        .machine.is-spinning .capsule:nth-child(odd) {
            animation: capsule-wiggle .22s ease-in-out infinite alternate;
        }

        .machine.is-spinning .capsule:nth-child(even) {
            animation: capsule-wiggle .3s ease-in-out infinite alternate-reverse;
        }

        @keyframes pile-shake {
            from { transform: translateX(-3px) rotate(-1deg); }
            to { transform: translateX(3px) rotate(1deg); }
        }

        @keyframes capsule-wiggle {
            from { translate: 0 -2px; }
            to { translate: 0 3px; }
        }

        .selected-capsule {
            position: absolute;
            z-index: 2;
            top: 42%;
            left: 50%;
            display: none;
            width: 86px;
            height: 102px;
            border: 3px solid rgba(124, 78, 29, .35);
            border-radius: 50%;
            background: linear-gradient(135deg, #fff1a8, #efb93f);
            box-shadow: 0 12px 25px rgba(0, 0, 0, .2);
            transform: translate(-50%, -50%);
        }

        .selected-capsule::before {
            content: "";
            position: absolute;
            top: 49%;
            left: 0;
            width: 100%;
            height: 6px;
            background: #fff;
        }

        .machine.is-spinning .selected-capsule {
            display: block;
            animation: capsule-drop 1.2s cubic-bezier(.2, .8, .25, 1) forwards;
        }

        @keyframes capsule-drop {
            0% { transform: translate(-50%, -240%) rotate(-20deg) scale(.65); }
            50% { transform: translate(-50%, -50%) rotate(15deg) scale(1.05); }
            75% { transform: translate(-50%, -40%) rotate(-10deg) scale(1); }
            100% { transform: translate(-50%, -45%) rotate(0) scale(1); }
        }

        .machine-base {
            margin-top: 13px;
            padding: 15px 12px;
            border: 3px solid #e8bc62;
            border-radius: 15px;
            background: linear-gradient(135deg, #fff4cc, #e8bc62);
            color: #75441e;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 2px;
        }

        .machine-knob {
            position: absolute;
            right: -17px;
            bottom: 90px;
            width: 36px;
            height: 36px;
            border: 5px solid #f4d88f;
            border-radius: 50%;
            background: #fff8e5;
            box-shadow: 0 3px 0 #751b22;
        }

        .machine-knob::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 18px;
            height: 5px;
            border-radius: 10px;
            background: var(--red);
            transform: translate(-50%, -50%) rotate(-35deg);
        }

        .code-entry {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            width: min(100%, 360px);
            margin: 0 auto 22px;
        }

        .code-entry label {
            color: var(--green);
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .code-entry input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e4dfcd;
            border-radius: 14px;
            background: #fff;
            color: var(--green-dark);
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 2px;
            text-align: center;
            text-transform: uppercase;
        }

        .code-entry input:focus {
            outline: 2px solid var(--gold);
            border-color: var(--gold);
        }

        .open-button {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            min-width: 220px;
            padding: 16px 24px;
            border: 0;
            border-radius: 50px;
            color: white;
            background: linear-gradient(135deg, var(--green), #22684b);
            box-shadow: 0 5px 0 #103a2b, 0 10px 22px rgba(23, 75, 56, .18);
            font-weight: 800;
            cursor: pointer;
            transition: transform .2s, box-shadow .2s, opacity .2s;
        }

        .open-button:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 8px 0 #103a2b, 0 13px 25px rgba(23, 75, 56, .2);
        }

        .open-button:active:not(:disabled) {
            transform: translateY(2px);
            box-shadow: 0 2px 0 #103a2b;
        }

        .open-button:disabled {
            opacity: .65;
            cursor: not-allowed;
        }

        .open-button[hidden] {
            display: none;
        }

        .status {
            min-height: 24px;
            margin: 20px auto 0;
            color: var(--muted);
            font-size: 14px;
        }

        .status.error {
            color: var(--red-dark);
        }

        .machine.is-revealed .machine-title,
        .machine.is-revealed .capsule-pile,
        .machine.is-revealed .selected-capsule {
            display: none;
        }

        .machine.is-revealed .machine-window {
            height: auto;
            min-height: 285px;
            background: #fffaf0;
        }

        .result {
            display: none;
            position: relative;
            z-index: 3;
            width: 100%;
            min-height: 285px;
            margin: 0;
            padding: 24px 16px;
            border: 0;
            border-radius: 0;
            background: #fffaf0;
            box-shadow: none;
            animation: result-appear .55s cubic-bezier(.2, .8, .2, 1) both;
        }

        .result.visible {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .result .open-button {
            margin-top: 16px;
        }

        @keyframes result-appear {
            from { opacity: 0; transform: translateY(24px) scale(.94); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .result-icon {
            font-size: 44px;
        }

        .result h2 {
            margin: 10px 0;
            color: var(--green);
            font-family: Georgia, serif;
            font-size: 30px;
        }

        .result h3 {
            margin: 0 0 8px;
            color: var(--green-dark);
            font-size: 22px;
        }

        .gift-image {
            display: none;
            width: min(100%, 230px);
            max-height: 220px;
            margin: 18px auto;
            object-fit: contain;
            border-radius: 15px;
        }

        .gift-image.visible {
            display: block;
        }

        .gift-description {
            color: #637267;
            line-height: 1.7;
            overflow-wrap: anywhere;
        }

        .gift-amount {
            margin-top: 12px;
            color: var(--red);
            font-size: 20px;
            font-weight: 900;
        }

        .prize-history {
            width: min(100%, 720px);
            margin: 36px auto 0;
            text-align: left;
        }

        .prize-history h2 {
            margin: 0 0 8px;
            color: var(--green);
            font-family: Georgia, serif;
            font-size: 28px;
            text-align: center;
        }

        .prize-code {
            margin: 0 0 14px;
            color: #637267;
            text-align: center;
        }

        .prize-code strong {
            color: var(--green-dark);
            letter-spacing: 1px;
        }

        .prize-table-wrap {
            overflow-x: auto;
            border: 2px solid #e8bc62;
            border-radius: 16px;
            background: #fff;
        }

        .prize-table {
            width: 100%;
            border-collapse: collapse;
        }

        .prize-table th,
        .prize-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #efe8d6;
            text-align: left;
            vertical-align: top;
        }

        .prize-table th {
            color: var(--green);
            font-size: 12px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .prize-table tr:last-child td {
            border-bottom: 0;
        }

        .prize-qr {
            margin-top: 18px;
            text-align: center;
        }

        .prize-qr p {
            margin: 10px 0 0;
            color: #637267;
        }

        .prize-qr a {
            color: var(--green);
            overflow-wrap: anywhere;
        }

        .footer-note {
            margin-top: 30px;
            color: #839087;
            font-size: 12px;
            line-height: 1.7;
        }

        .snow {
            position: fixed;
            top: -15px;
            z-index: -1;
            color: rgba(23, 75, 56, .18);
            pointer-events: none;
            animation: snowfall linear infinite;
        }

        @keyframes snowfall {
            to { transform: translateY(110vh) rotate(360deg); }
        }

        @media (max-width: 480px) {
            .page {
                width: min(100% - 22px, 900px);
                padding-top: 25px;
            }

            .machine {
                padding: 15px 13px 19px;
                border-radius: 27px 27px 20px 20px;
            }

            .machine-window {
                height: 260px;
            }

            .capsule {
                width: 47px;
                height: 57px;
                flex-basis: 47px;
            }

            .machine-knob {
                right: -12px;
                width: 29px;
                height: 29px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                scroll-behavior: auto !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>
<body>
    <main class="page">
        <div class="top-label">✦ A little Christmas magic ✦</div>

        <h1>{{ $machine->name }}</h1>

        <p class="subtitle">
            {{ $machine->welcome_message ?: 'A little surprise, wrapped with love. Turn the magic and see what Christmas has for you!' }}
        </p>

        @if ($participant)
            <div class="player-card">
                <span>For <strong>{{ $participant->name }}</strong></span>
                <span aria-hidden="true">•</span>
                <span>
                    Openings left:
                    <strong id="openings-left">
                        {{ $openingsRemaining === null ? 'Unlimited' : $openingsRemaining }}
                    </strong>
                </span>
            </div>
        @endif

        <section class="machine-area" aria-label="Christmas capsule machine">
            <div class="machine" id="machine">
                <div class="machine-title">CHRISTMAS CAPSULES</div>

                <div class="machine-window">
                    <div class="capsule-pile" id="capsule-pile" aria-hidden="true">
                        @for ($i = 0; $i < 18; $i++)
                            <div class="capsule"></div>
                        @endfor
                    </div>

                    <div class="selected-capsule" aria-hidden="true"></div>

                    <section class="result" id="result" aria-live="polite">
                        <div class="result-icon" aria-hidden="true">🎄🎁✨</div>
                        <h2>Your Gift!</h2>
                        <h3 id="gift-name"></h3>
                        <img id="gift-image" class="gift-image" alt="">
                        <p class="gift-description" id="gift-description"></p>
                        <p class="gift-amount" id="gift-amount"></p>
                        <button type="button" class="open-button" id="open-again-button" hidden>
                            You have 1 opening. Open now?
                        </button>
                    </section>
                </div>

                <div class="machine-base">YOUR SURPRISE AWAITS</div>
                <div class="machine-knob" aria-hidden="true"></div>
            </div>
        </section>

        @if (!$participant)
            <form class="code-entry" id="code-entry" autocomplete="off">
                <label for="special-code">Enter your special code</label>
                <input
                    id="special-code"
                    name="code"
                    type="text"
                    inputmode="text"
                    maxlength="20"
                    placeholder="GIFT-XXXX"
                    required
                >
                <button type="submit" class="open-button" id="use-code-button">
                    Use my code
                </button>
            </form>
        @endif

        <button type="button" class="open-button" id="open-button"
            @disabled(!$participant || ($openingsRemaining !== null && $openingsRemaining <= 0))>
            <span aria-hidden="true">🎁</span>
            <span id="button-text">
                @if (!$participant)
                    ENTER YOUR CODE
                @elseif ($openingsRemaining !== null && $openingsRemaining <= 0)
                    ALL OPENINGS USED
                @else
                    OPEN CAPSULE
                @endif
            </span>
        </button>

        <p class="status" id="status" role="status" aria-live="polite">
            @if (!$participant)
                Enter the special code from your letter.
            @elseif ($openingsRemaining !== null && $openingsRemaining <= 0)
                You have used all your openings.
            @else
                Ready to discover your Christmas surprise?
            @endif
        </p>

        @if ($participant)
            <section class="prize-history" aria-labelledby="prize-history-title">
                <h2 id="prize-history-title">Your prizes</h2>

                <p class="prize-code">
                    @if (!empty($godchild_code))
                        Your code: <strong>{{ $godchild_code }}</strong>
                    @else
                        This code was issued before codes were saved.
                    @endif
                </p>

                <div class="prize-table-wrap">
                    <table class="prize-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>When</th>
                                <th>Prize</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody id="prize-history-body">
                            @forelse ($prize_history as $prize)
                                <tr>
                                    <td>{{ $godchild_code ?: '—' }}</td>
                                    <td>{{ $prize['opened_label'] }}</td>
                                    <td>{{ $prize['name'] }}</td>
                                    <td>
                                        @if ($prize['amount'] !== null)
                                            ₱{{ number_format((float) $prize['amount'], 2) }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr id="prize-empty">
                                    <td colspan="4">No prizes yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if (!empty($prize_url))
                    <div class="prize-qr">
                        <div data-prize-qr="{{ $prize_url }}"></div>
                        <p>Godparent: scan this code to open the official prize record.</p>
                        <a href="{{ $prize_url }}">{{ $prize_url }}</a>
                    </div>
                @endif
            </section>
        @endif

        <p class="footer-note">
            Made with love and a little Christmas magic.<br>
            Every capsule is a surprise chosen just for you. ❤️
        </p>
    </main>

    <script>
        (() => {
            const open_url = @json(route('gachapon.open', ['token' => $machine->public_token]));
            const verify_url = @json(route('gachapon.verify-code', ['token' => $machine->public_token]));
            const csrf_token = document.querySelector('meta[name="csrf-token"]').content;

            const machine = document.getElementById('machine');
            const button = document.getElementById('open-button');
            const buttonText = document.getElementById('button-text');
            const status = document.getElementById('status');
            const openingsLeft = document.getElementById('openings-left');
            const result = document.getElementById('result');

            const giftName = document.getElementById('gift-name');
            const giftDescription = document.getElementById('gift-description');
            const giftImage = document.getElementById('gift-image');
            const giftAmount = document.getElementById('gift-amount');
            const open_again_button = document.getElementById('open-again-button');
            const history_code = @json($godchild_code ?? null);

            let isOpening = false;
            let reveal_succeeded = false;

            const openings_label = (count) => {
                if (count === null) {
                    return 'You have unlimited openings. Open now?';
                }

                if (count === 1) {
                    return 'You have 1 opening. Open now?';
                }

                return 'You have ' + count + ' openings. Open now?';
            };

            function append_prize_row(data) {
                const body = document.getElementById('prize-history-body');

                if (!body) {
                    return;
                }

                const empty_row = document.getElementById('prize-empty');

                if (empty_row) {
                    empty_row.remove();
                }

                const gift = data.gift || {};
                const row = document.createElement('tr');
                const amount_label = gift.amount !== null && gift.amount !== undefined
                    ? '₱' + Number(gift.amount).toLocaleString('en-PH', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    })
                    : '—';
                const values = [
                    history_code || '—',
                    data.opened_label || '',
                    gift.name || 'A Christmas Surprise',
                    amount_label
                ];

                values.forEach((value) => {
                    const cell = document.createElement('td');
                    cell.textContent = value;
                    row.appendChild(cell);
                });

                body.prepend(row);
            }

            function showError(message) {
                status.textContent = message || 'Something went wrong. Please try again.';
                status.classList.add('error');
            }

            function displayGift(data) {
                const gift = data.gift;

                giftName.textContent = gift.name || 'A Christmas Surprise';
                giftDescription.textContent = gift.description || 'A special gift, chosen with love!';

                giftImage.classList.remove('visible');
                giftImage.removeAttribute('src');

                if (gift.image_url) {
                    giftImage.onload = () => giftImage.classList.add('visible');
                    giftImage.onerror = () => giftImage.classList.remove('visible');
                    giftImage.src = gift.image_url;
                    giftImage.alt = gift.name || 'Your Christmas gift';
                }

                giftAmount.textContent = gift.amount !== null && gift.amount !== undefined
                    ? '₱' + Number(gift.amount).toLocaleString('en-PH', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    })
                    : '';

                if (openingsLeft) {
                    if (data.openings_remaining === null) {
                        openingsLeft.textContent = 'Unlimited';
                    } else {
                        openingsLeft.textContent = data.openings_remaining;
                    }
                }

                result.classList.remove('visible');
                machine.classList.add('is-revealed');

                // Restart the reveal animation.
                void result.offsetWidth;
                result.classList.add('visible');

                if (data.openings_remaining === 0) {
                    button.hidden = false;
                    button.disabled = true;
                    buttonText.textContent = 'ALL OPENINGS USED';
                    open_again_button.hidden = true;
                    status.textContent = 'That was your last opening. Merry Christmas!';
                } else {
                    button.hidden = true;
                    open_again_button.hidden = false;
                    open_again_button.textContent = openings_label(data.openings_remaining);
                    status.textContent = 'Congratulations! Your surprise has been revealed!';
                }

                append_prize_row(data);
            }

            const code_form = document.getElementById('code-entry');
            const code_input = document.getElementById('special-code');
            const use_code_button = document.getElementById('use-code-button');

            if (code_form && code_input && use_code_button) {
                code_form.addEventListener('submit', async (event) => {
                    event.preventDefault();

                    if (use_code_button.disabled) {
                        return;
                    }

                    use_code_button.disabled = true;
                    status.classList.remove('error');
                    status.textContent = 'Checking your code...';

                    const request_body = {
                        code: code_input.value.trim(),
                    };

                    try {
                        const response = await fetch(verify_url, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrf_token,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify(request_body)
                        });

                        const res = await response.json();
                        let { code, title, msg, data } = res;

                        if (code == 1 && data) {
                            window.location.reload();
                            return;
                        }

                        showError(msg || title || 'That code could not be used.');
                        use_code_button.disabled = false;
                    } catch (error) {
                        showError('Something went wrong. Please try again.');
                        use_code_button.disabled = false;
                    }
                });
            }

            const begin_opening = async () => {
                if (isOpening) {
                    return;
                }

                if (button.disabled && open_again_button.hidden) {
                    return;
                }

                isOpening = true;
                reveal_succeeded = false;
                button.disabled = true;
                open_again_button.disabled = true;
                buttonText.textContent = 'TURNING THE MACHINE...';
                status.classList.remove('error');
                status.textContent = 'Mixing the capsules... your surprise is on its way!';
                result.classList.remove('visible');
                machine.classList.remove('is-revealed');
                machine.classList.add('is-spinning');

                try {
                    const request_body = {};
                    const response = await fetch(open_url, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf_token,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(request_body)
                    });

                    const res = await response.json();
                    let { code, title, msg, data } = res;

                    if (code != 1 || !data || !data.gift) {
                        throw new Error(msg || title || 'Unable to open a capsule.');
                    }

                    await new Promise((resolve) => {
                        window.setTimeout(resolve, 1400);
                    });

                    displayGift(data);
                    reveal_succeeded = true;
                } catch (error) {
                    showError(error.message);
                } finally {
                    machine.classList.remove('is-spinning');
                    isOpening = false;
                    open_again_button.disabled = false;

                    if (reveal_succeeded) {
                        return;
                    }

                    button.hidden = false;
                    open_again_button.hidden = true;
                    machine.classList.remove('is-revealed');
                    result.classList.remove('visible');

                    if (!buttonText.textContent.includes('ALL OPENINGS USED')) {
                        buttonText.textContent = 'OPEN CAPSULE';
                        button.disabled = false;
                    }
                }
            };

            button.addEventListener('click', begin_opening);
            open_again_button.addEventListener('click', begin_opening);

            for (let i = 0; i < 22; i++) {
                const snow = document.createElement('span');
                snow.className = 'snow';
                snow.textContent = '❄';
                snow.setAttribute('aria-hidden', 'true');
                snow.style.left = (Math.random() * 100) + 'vw';
                snow.style.fontSize = (12 + Math.random() * 16) + 'px';
                snow.style.animationDuration = (7 + Math.random() * 8) + 's';
                snow.style.animationDelay = (-Math.random() * 8) + 's';
                document.body.appendChild(snow);
            }
        })();
    </script>
</body>
</html>
