/**
 * Passkey Login - handles navigator.credentials.get() for backend login
 */
document.addEventListener('DOMContentLoaded', function () {
    var btn = document.getElementById('passkey-login-btn');
    if (!btn) return;

    // Hide button if WebAuthn is not supported
    if (!window.PublicKeyCredential) {
        btn.closest('#passkey-login-section').style.display = 'none';
        return;
    }

    btn.addEventListener('click', function () {
        startPasskeyLogin();
    });

    function showError(msg) {
        var el = document.getElementById('passkey-login-error');
        if (el) {
            el.textContent = msg;
            el.style.display = 'block';
        }
    }

    function hideError() {
        var el = document.getElementById('passkey-login-error');
        if (el) {
            el.style.display = 'none';
        }
    }

    function setLoading(loading) {
        btn.disabled = loading;
        btn.classList.toggle('passkey-btn-loading', loading);
    }

    function startPasskeyLogin() {
        hideError();
        setLoading(true);

        // Step 1: Get assertion options from server
        $.request('onPasskeyLoginOptions', {
            success: function (data) {
                handleLoginOptions(data.options);
            },
            error: function (xhr) {
                setLoading(false);
                var msg = 'Failed to start passkey authentication.';
                try {
                    var resp = JSON.parse(xhr.responseText);
                    if (resp.X_OCTOBER_ERROR_MESSAGE) msg = resp.X_OCTOBER_ERROR_MESSAGE;
                } catch (e) {}
                showError(msg);
            }
        });
    }

    function handleLoginOptions(options) {
        // Convert base64url challenge to ArrayBuffer
        options.publicKey.challenge = base64urlToBuffer(options.publicKey.challenge);

        // Convert allowCredentials if present
        if (options.publicKey.allowCredentials) {
            for (var i = 0; i < options.publicKey.allowCredentials.length; i++) {
                options.publicKey.allowCredentials[i].id = base64urlToBuffer(
                    options.publicKey.allowCredentials[i].id
                );
            }
        }

        // Step 2: Call browser WebAuthn API
        navigator.credentials.get(options)
            .then(function (assertion) {
                handleLoginAssertion(assertion);
            })
            .catch(function (err) {
                setLoading(false);
                if (err.name === 'NotAllowedError') {
                    showError('Authentication was cancelled or timed out.');
                } else {
                    showError('Passkey error: ' + err.message);
                }
            });
    }

    function handleLoginAssertion(assertion) {
        // Step 3: Send assertion to server for verification
        var data = {
            credential_id: bufferToBase64url(assertion.rawId),
            client_data_json: bufferToBase64url(assertion.response.clientDataJSON),
            authenticator_data: bufferToBase64url(assertion.response.authenticatorData),
            signature: bufferToBase64url(assertion.response.signature),
            user_handle: assertion.response.userHandle
                ? bufferToBase64url(assertion.response.userHandle)
                : ''
        };

        $.request('onPasskeyLoginVerify', {
            data: data,
            success: function (resp) {
                if (resp.redirect) {
                    window.location.href = resp.redirect;
                } else {
                    window.location.reload();
                }
            },
            error: function (xhr) {
                setLoading(false);
                var msg = 'Passkey verification failed.';
                try {
                    var resp = JSON.parse(xhr.responseText);
                    if (resp.X_OCTOBER_ERROR_MESSAGE) msg = resp.X_OCTOBER_ERROR_MESSAGE;
                } catch (e) {}
                showError(msg);
            }
        });
    }

    // --- Base64url helpers ---

    function base64urlToBuffer(base64url) {
        // Handle both base64url and standard base64
        var base64 = base64url.replace(/-/g, '+').replace(/_/g, '/');
        var padding = base64.length % 4;
        if (padding) {
            base64 += '='.repeat(4 - padding);
        }
        var binary = atob(base64);
        var buffer = new ArrayBuffer(binary.length);
        var view = new Uint8Array(buffer);
        for (var i = 0; i < binary.length; i++) {
            view[i] = binary.charCodeAt(i);
        }
        return buffer;
    }

    function bufferToBase64url(buffer) {
        var bytes = new Uint8Array(buffer);
        var binary = '';
        for (var i = 0; i < bytes.byteLength; i++) {
            binary += String.fromCharCode(bytes[i]);
        }
        return btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
    }
});
