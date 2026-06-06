/**
 * DIY analytics (Step 11). Records a page view on load and fires an event for
 * any clicked element carrying [data-track-event]. Events are POSTed to /track
 * as JSON. navigator.sendBeacon is preferred so the request still completes when
 * the click navigates the page away (e.g. outbound LinkedIn/GitHub links); a
 * keepalive fetch is the fallback when sendBeacon is unavailable.
 */
(function () {
    const ENDPOINT = '/track';

    function send(event, meta) {
        const body = JSON.stringify(meta ? { event, meta } : { event });

        if (navigator.sendBeacon) {
            navigator.sendBeacon(ENDPOINT, new Blob([body], { type: 'application/json' }));
            return;
        }

        fetch(ENDPOINT, {
            method: 'POST',
            keepalive: true,
            headers: { 'Content-Type': 'application/json' },
            body,
        }).catch(() => { /* analytics is best-effort */ });
    }

    // Page view.
    send('page_view');

    // Delegated click tracking. meta falls back to data-title (project cards).
    document.addEventListener('click', (e) => {
        const el = e.target.closest('[data-track-event]');
        if (!el) return;

        send(el.dataset.trackEvent, el.dataset.trackMeta || el.dataset.title || null);
    });
})();
