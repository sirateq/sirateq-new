@script
<script>
    (() => {
        const ensurePaystack = () => new Promise((resolve, reject) => {
            if (typeof PaystackPop !== 'undefined') {
                return resolve();
            }
            const existing = document.querySelector('script[data-paystack-inline]');
            const script = existing ?? document.createElement('script');
            if (!existing) {
                script.src = 'https://js.paystack.co/v1/inline.js';
                script.async = true;
                script.dataset.paystackInline = 'true';
                document.head.appendChild(script);
            }
            script.addEventListener('load', () => resolve(), { once: true });
            script.addEventListener('error', () => reject(new Error('Failed to load Paystack')), { once: true });
        });

        if (window.__paystackLivewireListenerBound) {
            return;
        }
        window.__paystackLivewireListenerBound = true;

        Livewire.on('paystack:open', async (payload) => {
            const detail = Array.isArray(payload) ? payload[0] : payload;

            if (!detail || !detail.publicKey) {
                console.warn('Paystack public key missing — aborting checkout.');
                return;
            }

            try {
                await ensurePaystack();
            } catch (error) {
                console.error(error);
                Livewire.dispatch('paystack:cancelled', { reference: detail.reference });
                return;
            }

            const handler = PaystackPop.setup({
                key: detail.publicKey,
                email: detail.email,
                amount: detail.amount,
                ref: detail.reference,
                currency: detail.currency || 'GHS',
                metadata: detail.metadata || {},
                callback: (response) => {
                    Livewire.dispatch('paystack:callback', { reference: response.reference });
                },
                onClose: () => {
                    Livewire.dispatch('paystack:cancelled', { reference: detail.reference });
                },
            });

            handler.openIframe();
        });
    })();
</script>
@endscript
