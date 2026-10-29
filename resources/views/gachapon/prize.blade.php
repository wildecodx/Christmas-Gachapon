<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🧧</text></svg>">
    <title>Official prize record | Christmas Gachapon</title>
    <style>
        :root {
            --green: #174b38;
            --green-dark: #102d26;
            --red: #b93236;
            --gold: #e8bc62;
            --cream: #fffaf0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            color: var(--green-dark);
            background:
                radial-gradient(ellipse at top, #fffdf7 0%, transparent 60%),
                linear-gradient(135deg, #fffaf0, #e8f1e8);
        }

        .page {
            width: min(100% - 32px, 760px);
            margin: 0 auto;
            padding: 36px 0 56px;
        }

        .eyebrow {
            margin: 0;
            color: var(--red);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 3px;
            text-align: center;
            text-transform: uppercase;
        }

        h1 {
            margin: 10px 0;
            color: var(--green);
            font-family: Georgia, serif;
            font-size: clamp(32px, 6vw, 46px);
            text-align: center;
        }

        .intro,
        .code-line {
            margin: 0 auto 18px;
            max-width: 560px;
            color: #637267;
            line-height: 1.7;
            text-align: center;
        }

        .code-line strong {
            color: var(--green-dark);
            letter-spacing: 1px;
        }

        .table-wrap {
            overflow-x: auto;
            border: 2px solid var(--gold);
            border-radius: 16px;
            background: #fff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 12px 14px;
            border-bottom: 1px solid #efe8d6;
            text-align: left;
        }

        th {
            color: var(--green);
            font-size: 12px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        tr:last-child td {
            border-bottom: 0;
        }

        .note {
            margin-top: 18px;
            color: #839087;
            font-size: 13px;
            text-align: center;
        }
    </style>
</head>
<body>
    <main class="page">
        <p class="eyebrow">Official prize record</p>
        <h1>{{ $machine->name ?? 'Christmas Gachapon' }}</h1>
        <p class="intro">
            This page shows only the prizes opened by
            <strong>{{ $participant->name }}</strong>.
        </p>
        <p class="code-line">
            @if (!empty($participant->code))
                Godchild code: <strong>{{ $participant->code }}</strong>
            @else
                This code was issued before codes were saved.
            @endif
        </p>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>When</th>
                        <th>Prize</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($prize_history as $prize)
                        <tr>
                            <td>{{ $participant->code ?: '—' }}</td>
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
                        <tr>
                            <td colspan="4">No prizes yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <p class="note">
            Scanned from the gachapon. This record does not open the private management page.
        </p>
    </main>
</body>
</html>
