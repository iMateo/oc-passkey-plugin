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
        hideError();
    }

    function showError(msg) {
        $('#passkey-manage-error').text(msg).show();
    }

    function hideError() {
        $('#passkey-manage-error').hide();
    }

    // Step 1: Get registration options from server
    function startRegistration() {
        hideError();
        $('#passkey-register-confirm').prop('disabled', true);

        $.request('onPasskeyRegisterOptions', {
            success: function (data) {
                // Don't call this.success() — we handle the response manually
                callBrowserCredentialsCreate(data.options);
            },
            error: function () {
                $('#passkey-register-confirm').prop('disabled', false);
                showError('Failed to start passkey registration.');
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
        $.request('onPasskeyRegisterVerify', {
            data: {
                client_data_json: bufferToBase64url(credential.response.clientDataJSON),
                attestation_object: bufferToBase64url(credential.response.attestationObject),
                passkey_name: $('#passkey-name-input').val().trim() || 'Passkey'
            },
            // No success override — October CMS handles #passkey-list-container update + flash
            complete: function () {
                resetForm();
            },
            error: function () {
                $('#passkey-register-confirm').prop('disabled', false);
                showError('Failed to register passkey.');
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
