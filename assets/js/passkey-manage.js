/**
 * Passkey Management - handles navigator.credentials.create() for registration.
 * Delete uses standard October CMS data-request attributes (no JS needed).
 * Uses event delegation so it works regardless of when October CMS renders the tab partial.
 */
(function ($) {
    'use strict';

    // Show registration form
    $(document).on('click', '#passkey-register-btn', function (e) {
        e.preventDefault();
        e.stopPropagation();
        if (!window.PublicKeyCredential) {
            showError('WebAuthn is not supported in this browser.');
            return;
        }
        $('#passkey-register-form').show();
        $(this).hide();
        $('#passkey-name-input').focus();
    });

    // Cancel registration
    $(document).on('click', '#passkey-register-cancel', function (e) {
        e.preventDefault();
        e.stopPropagation();
        resetForm();
    });

    // Start registration ceremony
    $(document).on('click', '#passkey-register-confirm', function (e) {
        e.preventDefault();
        e.stopPropagation();
        startRegistration();
    });

    function resetForm() {
        $('#passkey-register-form').hide();
        $('#passkey-register-btn').show();
        $('#passkey-register-confirm').prop('disabled', false);
        $('#passkey-name-input').val('');
        $('#passkey-password-input').val('');
        hideError();
    }

    function showError(msg) {
        $('#passkey-manage-error').text(msg).show();
    }

    function hideError() {
        $('#passkey-manage-error').hide();
    }

    /**
     * Extract a server error message from a $.request error callback argument.
     * October v4 (Larajax): decorated object, message in data.$env.getMessage().
     * October v3: plain-text message body, or an object with X_OCTOBER_ERROR_MESSAGE (406).
     */
    function extractErrorMessage(data, fallback) {
        try {
            if (data && data.$env && typeof data.$env.getMessage === 'function' && data.$env.getMessage()) {
                return String(data.$env.getMessage());
            }
            if (data && typeof data === 'object') {
                if (data.X_OCTOBER_ERROR_MESSAGE) return String(data.X_OCTOBER_ERROR_MESSAGE);
                if (typeof data.message === 'string' && data.message) return data.message;
                if (typeof data.responseText === 'string') {
                    return extractErrorMessage(JSON.parse(data.responseText), fallback);
                }
            }
            if (typeof data === 'string') {
                var text = data.trim();
                if (text && text.length < 500 && text.charAt(0) !== '<') return text;
            }
        } catch (e) {}
        return fallback;
    }

    // Step 1: Get registration options from server
    function startRegistration() {
        hideError();
        $('#passkey-register-confirm').prop('disabled', true);

        // Current password is checked server-side before a challenge is issued
        var password = $('#passkey-password-input').val() || '';
        $('#passkey-password-input').val('');

        $.request('onPasskeyRegisterOptions', {
            data: { current_password: password },
            success: function (data) {
                // Don't call this.success() — we handle the response manually
                if (!data || !data.options || !data.options.publicKey) {
                    $('#passkey-register-confirm').prop('disabled', false);
                    showError('Failed to start passkey registration.');
                    return;
                }
                callBrowserCredentialsCreate(data.options);
            },
            error: function (data) {
                $('#passkey-register-confirm').prop('disabled', false);
                showError(extractErrorMessage(data, 'Failed to start passkey registration.'));
            }
        });
    }

    // Step 2: Call browser WebAuthn API
    function callBrowserCredentialsCreate(options) {
        options.publicKey.challenge = base64urlToBuffer(options.publicKey.challenge);
        options.publicKey.user.id = base64urlToBuffer(options.publicKey.user.id);

        if (options.publicKey.excludeCredentials) {
            for (var i = 0; i < options.publicKey.excludeCredentials.length; i++) {
                options.publicKey.excludeCredentials[i].id = base64urlToBuffer(
                    options.publicKey.excludeCredentials[i].id
                );
            }
        }

        navigator.credentials.create(options)
            .then(function (credential) {
                sendAttestationToServer(credential);
            })
            .catch(function (err) {
                $('#passkey-register-confirm').prop('disabled', false);
                if (err.name === 'NotAllowedError') {
                    showError('Registration was cancelled or timed out.');
                } else if (err.name === 'InvalidStateError') {
                    showError('This passkey is already registered.');
                } else {
                    showError('Passkey error: ' + err.message);
                }
            });
    }

    // Step 3: Send attestation to server — let October handle partial update
    function sendAttestationToServer(credential) {
        var failed = false;
        $.request('onPasskeyRegisterVerify', {
            data: {
                client_data_json: bufferToBase64url(credential.response.clientDataJSON),
                attestation_object: bufferToBase64url(credential.response.attestationObject),
                passkey_name: $('#passkey-name-input').val().trim() || 'Passkey'
            },
            // No success override — October CMS handles #passkey-list-container update + flash
            // complete runs after error in both v3 and v4; keep the error visible
            complete: function () {
                if (!failed) {
                    resetForm();
                }
            },
            error: function (data) {
                failed = true;
                $('#passkey-register-confirm').prop('disabled', false);
                showError(extractErrorMessage(data, 'Failed to register passkey.'));
            }
        });
    }

    // --- Base64url helpers ---

    function base64urlToBuffer(base64url) {
        var base64 = base64url.replace(/-/g, '+').replace(/_/g, '/');
        var padding = base64.length % 4;
        if (padding) base64 += '='.repeat(4 - padding);
        var binary = atob(base64);
        var buffer = new ArrayBuffer(binary.length);
        var view = new Uint8Array(buffer);
        for (var i = 0; i < binary.length; i++) view[i] = binary.charCodeAt(i);
        return buffer;
    }

    function bufferToBase64url(buffer) {
        var bytes = new Uint8Array(buffer);
        var binary = '';
        for (var i = 0; i < bytes.byteLength; i++) binary += String.fromCharCode(bytes[i]);
        return btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
    }

})(jQuery);
