<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aplikasi Dinonaktifkan Sementara</title>
    <style>
        :root {
            --color-bg: #0f172a;
            --color-bg-deep: #07111f;
            --color-card: rgba(15, 23, 42, 0.82);
            --color-card-border: rgba(214, 166, 57, 0.18);
            --color-text: #f8fafc;
            --color-muted: #cbd5e1;
            --color-soft: #94a3b8;
            --color-warning: #f97316;
            --color-warning-deep: #b91c1c;
            --color-whatsapp: #22c55e;
            --color-whatsapp-deep: #15803d;
            --color-whatsapp-text: #052e16;
            --space-1: 0.5rem;
            --space-2: 0.75rem;
            --space-3: 1rem;
            --space-4: 1.5rem;
            --space-5: 2rem;
            --space-6: 3rem;
            --radius-card: 1.75rem;
            --radius-pill: 999px;
            --shadow-card: 0 1.5rem 5rem rgba(0, 0, 0, 0.45);
            --shadow-button: 0 1rem 2rem rgba(34, 197, 94, 0.24);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: var(--space-5);
            background:
                radial-gradient(circle at top left, rgba(214, 166, 57, 0.16), transparent 34rem),
                radial-gradient(circle at bottom right, rgba(8, 76, 53, 0.26), transparent 30rem),
                linear-gradient(135deg, var(--color-bg-deep), var(--color-bg));
            color: var(--color-text);
            font-family: "Segoe UI", "Helvetica Neue", Helvetica, Arial, sans-serif;
            line-height: 1.6;
        }

        .page {
            width: min(100%, 42rem);
        }

        .card {
            position: relative;
            overflow: hidden;
            padding: clamp(var(--space-5), 6vw, var(--space-6));
            border: 1px solid var(--color-card-border);
            border-radius: var(--radius-card);
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.055), transparent),
                var(--color-card);
            box-shadow: var(--shadow-card);
            text-align: center;
        }

        .card::before {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.035) 1px, transparent 1px);
            background-size: 4rem 4rem;
            mask-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.6), transparent 70%);
        }

        .content {
            position: relative;
            z-index: 1;
        }

        .warning-icon {
            width: 5rem;
            height: 5rem;
            display: inline-grid;
            place-items: center;
            margin-bottom: var(--space-4);
            border: 1px solid rgba(249, 115, 22, 0.38);
            border-radius: 50%;
            background:
                radial-gradient(circle at 35% 25%, rgba(255, 255, 255, 0.34), transparent 28%),
                linear-gradient(135deg, var(--color-warning), var(--color-warning-deep));
            box-shadow: 0 1rem 2.5rem rgba(185, 28, 28, 0.34);
            color: var(--color-text);
            font-size: 2.25rem;
            font-weight: 800;
            line-height: 1;
        }

        h1 {
            margin: 0;
            color: var(--color-text);
            font-size: clamp(2rem, 5vw, 3.25rem);
            line-height: 1.1;
            letter-spacing: -0.04em;
        }

        .description {
            max-width: 35rem;
            margin: var(--space-4) auto 0;
            color: var(--color-muted);
            font-size: clamp(1rem, 2vw, 1.125rem);
        }

        .supporting-text {
            max-width: 33rem;
            margin: var(--space-3) auto 0;
            color: var(--color-soft);
            font-size: 0.98rem;
        }

        .actions {
            margin-top: var(--space-5);
        }

        .whatsapp-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 3.25rem;
            padding: 0 var(--space-5);
            border-radius: var(--radius-pill);
            background: linear-gradient(135deg, var(--color-whatsapp), var(--color-whatsapp-deep));
            box-shadow: var(--shadow-button);
            color: var(--color-whatsapp-text);
            font-weight: 800;
            letter-spacing: 0.01em;
            text-decoration: none;
            transition: transform 180ms ease, box-shadow 180ms ease, filter 180ms ease;
        }

        .whatsapp-button:hover,
        .whatsapp-button:focus-visible {
            transform: translateY(-2px);
            box-shadow: 0 1.25rem 2.75rem rgba(34, 197, 94, 0.3);
            filter: saturate(1.08);
        }

        .whatsapp-button:focus-visible {
            outline: 3px solid rgba(34, 197, 94, 0.36);
            outline-offset: 4px;
        }

        .note {
            margin: var(--space-4) 0 0;
            color: var(--color-soft);
            font-size: 0.9rem;
        }

        @media (max-width: 40rem) {
            body {
                padding: var(--space-3);
            }

            .card {
                border-radius: 1.25rem;
                padding: var(--space-5) var(--space-4);
            }

            .warning-icon {
                width: 4.25rem;
                height: 4.25rem;
                font-size: 2rem;
            }

            .whatsapp-button {
                width: 100%;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .whatsapp-button {
                transition: none;
            }

            .whatsapp-button:hover,
            .whatsapp-button:focus-visible {
                transform: none;
            }
        }
    </style>
</head>
<body>
    <main class="page" aria-labelledby="maintenance-title">
        <section class="card" aria-live="polite">
            <div class="content">
                <div class="warning-icon" aria-hidden="true">!</div>
                <h1 id="maintenance-title">Aplikasi Dinonaktifkan Sementara</h1>
                <p class="description">Sistem tidak dapat digunakan sementara waktu karena layanan belum aktif atau terdapat kewajiban administrasi yang belum diselesaikan.</p>
                <p class="supporting-text">Silakan hubungi developer/admin untuk informasi lebih lanjut dan aktivasi kembali sistem.</p>
                <div class="actions">
                    <a class="whatsapp-button" href="https://wa.me/6282282003020" target="_blank" rel="noopener noreferrer">Hubungi WhatsApp</a>
                </div>
                <p class="note">Terima kasih atas pengertiannya.</p>
            </div>
        </section>
    </main>
</body>
</html>
