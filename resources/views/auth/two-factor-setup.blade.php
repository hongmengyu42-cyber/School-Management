@extends('layouts.app')
@section('title', 'Set up two-factor authentication')
@section('content')
    <div class="card" style="max-width:480px;">
        <div class="card-header"><h2 style="margin:0; font-size:15px;">Two-factor authentication required</h2></div>
        <div class="card-body">
            <p style="color:var(--ink-soft); margin-top:0;">
                Your role requires two-factor authentication before you can continue.
                Scan the QR code with an authenticator app (Google Authenticator, Authy, 1Password, etc.),
                then enter the 6-digit code it generates to confirm.
            </p>

            <div id="tfa-step-start">
                <button id="tfa-begin-btn" class="btn btn-primary">Begin setup</button>
            </div>

            <div id="tfa-step-qr" style="display:none;">
                <div id="tfa-qr-container" style="margin:16px 0; padding:16px; background:#fff; border:1px solid var(--line); border-radius:8px; display:flex; justify-content:center;"></div>
                <div class="field">
                    <label for="tfa-code">Enter the 6-digit code</label>
                    <input id="tfa-code" type="text" inputmode="numeric" maxlength="6" placeholder="123456">
                </div>
                <div id="tfa-error" class="field-error" style="display:none;"></div>
                <button id="tfa-confirm-btn" class="btn btn-primary">Confirm and enable</button>
            </div>

            <div id="tfa-step-done" style="display:none;">
                <div class="flash">Two-factor authentication is now enabled.</div>
                <p style="color:var(--ink-soft);">
                    Save these recovery codes somewhere safe — each one can be used once if you lose access to your authenticator app.
                </p>
                <div id="tfa-recovery-codes" style="background:var(--bg); border:1px solid var(--line); border-radius:8px; padding:14px; font-family:monospace; font-size:12.5px; line-height:1.8;"></div>
                <a href="{{ route('dashboard') }}" class="btn btn-primary" style="margin-top:16px;">Continue to dashboard</a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (!csrfMeta) {
        document.getElementById('tfa-step-start').innerHTML =
            '<div class="field-error">Setup page failed to load correctly (missing CSRF token). Please refresh the page, and if this persists, contact your administrator.</div>';
        console.error('two-factor-setup: meta[name="csrf-token"] not found on page — cannot proceed.');
        return;
    }
    const csrfToken = csrfMeta.content;
    const headers = {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
    };

    const stepStart = document.getElementById('tfa-step-start');
    const stepQr = document.getElementById('tfa-step-qr');
    const stepDone = document.getElementById('tfa-step-done');
    const errorBox = document.getElementById('tfa-error');

    function showFatalError(message) {
        errorBox.textContent = message;
        errorBox.style.display = 'block';
        console.error('two-factor-setup:', message);
    }

    document.getElementById('tfa-begin-btn').addEventListener('click', async function () {
        try {
            const enableRes = await fetch('/user/two-factor-authentication', { method: 'POST', headers });
            if (!enableRes.ok) {
                throw new Error(`Enable request failed with status ${enableRes.status}. Are you logged in?`);
            }

            const qrRes = await fetch('/user/two-factor-qr-code', { headers: { 'Accept': 'application/json' } });
            if (!qrRes.ok) {
                throw new Error(`QR code request failed with status ${qrRes.status}.`);
            }
            const qrData = await qrRes.json();
            document.getElementById('tfa-qr-container').innerHTML = qrData.svg;

            stepStart.style.display = 'none';
            stepQr.style.display = 'block';
        } catch (err) {
            showFatalError('Could not start 2FA setup: ' + err.message);
        }
    });

    document.getElementById('tfa-confirm-btn').addEventListener('click', async function () {
        errorBox.style.display = 'none';
        const code = document.getElementById('tfa-code').value.trim();

        try {
            const res = await fetch('/user/confirmed-two-factor-authentication', {
                method: 'POST',
                headers,
                body: JSON.stringify({ code }),
            });

            if (!res.ok) {
                const data = await res.json().catch(() => ({}));
                errorBox.textContent = data.message || `That code did not match (status ${res.status}). Please try again.`;
                errorBox.style.display = 'block';
                return;
            }

            const codesRes = await fetch('/user/two-factor-recovery-codes', { headers: { 'Accept': 'application/json' } });
            const codes = await codesRes.json();
            document.getElementById('tfa-recovery-codes').innerHTML = codes.join('<br>');

            stepQr.style.display = 'none';
            stepDone.style.display = 'block';
        } catch (err) {
            showFatalError('Could not confirm 2FA: ' + err.message);
        }
    });
})();
</script>
@endpush
