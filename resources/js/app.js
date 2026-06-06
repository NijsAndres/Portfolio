import Alpine from 'alpinejs';
import Sortable from 'sortablejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;

Alpine.start();

/**
 * Drag-and-drop reordering for admin list tables.
 *
 * Any <tbody data-reorder="<url>"> becomes sortable by its rows. Dragging only
 * starts from the `.js-drag-handle` cell. On drop the new row order is POSTed to
 * the reorder URL as { order: [id, ...] }; the server rewrites sort_order.
 */
function initReorderableTables() {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;

    document.querySelectorAll('tbody[data-reorder]').forEach((tbody) => {
        const url = tbody.dataset.reorder;

        Sortable.create(tbody, {
            handle: '.js-drag-handle',
            animation: 150,
            ghostClass: 'drag-ghost',
            onEnd: () => {
                const order = Array.from(tbody.querySelectorAll('tr[data-id]'))
                    .map((row) => row.dataset.id);

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ order }),
                }).catch((error) => console.error('Reorder failed', error));
            },
        });
    });
}

/**
 * Make rows with `data-edit-url` navigate to their edit form on click, except
 * when the click lands on the drag handle, the actions cell, or a link/button.
 */
function initRowLinks() {
    document.querySelectorAll('tr.js-row-link[data-edit-url]').forEach((row) => {
        row.addEventListener('click', (event) => {
            if (event.target.closest('.js-drag-handle, .js-row-actions, a, button')) {
                return;
            }

            window.location = row.dataset.editUrl;
        });
    });
}

/**
 * WYSIWYG bold editor for plain-text fields.
 *
 * Each `[data-rich-field="<textarea id>"]` contenteditable element shows real
 * bold while editing (no visible asterisks), but stays in sync with a hidden
 * <textarea> that submits the value as `**markers**`. So the database and the
 * frontend (components/rich-text.blade.php) keep dealing with safe plain text;
 * only the editing surface is rich. Without JS the plain textarea stays usable.
 *
 * The matching `[data-bold-target="<editor id>"]` button and Cmd/Ctrl+B toggle
 * bold on the current selection via execCommand.
 */
function markdownToHtml(md) {
    const escaped = md
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');

    return escaped
        .replace(/\*\*(.+?)\*\*/gs, '<strong>$1</strong>')
        .replace(/\n/g, '<br>');
}

function isBoldElement(el) {
    const tag = el.tagName;
    if (tag === 'STRONG' || tag === 'B') return true;
    const weight = el.style && el.style.fontWeight;
    return weight === 'bold' || parseInt(weight, 10) >= 600;
}

/** Serialize an editor's DOM back to `**markers**` plain text. */
function htmlToMarkdown(node) {
    let out = '';

    node.childNodes.forEach((child) => {
        if (child.nodeType === Node.TEXT_NODE) {
            out += child.nodeValue;
            return;
        }
        if (child.nodeType !== Node.ELEMENT_NODE) return;

        if (child.tagName === 'BR') {
            out += '\n';
            return;
        }

        const inner = htmlToMarkdown(child);

        if (child.tagName === 'DIV' || child.tagName === 'P') {
            // Block elements (created on Enter) become line breaks.
            if (out && !out.endsWith('\n')) out += '\n';
            out += inner;
        } else if (isBoldElement(child) && inner.trim()) {
            // Keep any leading/trailing spaces outside the ** markers.
            const lead = inner.match(/^\s*/)[0];
            const trail = inner.match(/\s*$/)[0];
            const core = inner.slice(lead.length, inner.length - trail.length);
            out += `${lead}**${core}**${trail}`;
        } else {
            out += inner;
        }
    });

    return out;
}

function initRichEditors() {
    // Prefer real tags (<b>) over inline styles when toggling bold.
    try { document.execCommand('styleWithCSS', false, false); } catch (e) { /* no-op */ }

    document.querySelectorAll('[data-rich-field]').forEach((editor) => {
        const field = document.getElementById(editor.dataset.richField);
        if (!field) return;

        // Enhance: reveal the rich editor, hide the raw textarea (still submits).
        editor.innerHTML = markdownToHtml(field.value);
        editor.classList.remove('hidden');
        field.classList.add('hidden');

        const sync = () => { field.value = htmlToMarkdown(editor).replace(/\n+$/, ''); };
        editor.addEventListener('input', sync);
        editor.closest('form')?.addEventListener('submit', sync);

        editor.addEventListener('keydown', (event) => {
            if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'b') {
                event.preventDefault();
                document.execCommand('bold');
                sync();
            }
        });
    });

    document.querySelectorAll('[data-bold-target]').forEach((button) => {
        const editor = document.getElementById(button.dataset.boldTarget);
        if (!editor) return;

        // Keep the editor's selection when the button is clicked.
        button.addEventListener('mousedown', (event) => event.preventDefault());
        button.addEventListener('click', () => {
            editor.focus();
            document.execCommand('bold');
            editor.dispatchEvent(new Event('input', { bubbles: true }));
        });
    });
}

/**
 * Render the dashboard's 30-day page-view chart (Step 11). Only runs when the
 * canvas is present, reading its { labels, data } series from data-series.
 */
function initAnalyticsChart() {
    const canvas = document.getElementById('analytics-chart');
    if (!canvas) return;

    let series;
    try {
        series = JSON.parse(canvas.dataset.series);
    } catch (e) {
        return;
    }

    new Chart(canvas, {
        type: 'line',
        data: {
            labels: series.labels,
            datasets: [{
                label: 'Page views',
                data: series.data,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.3,
                pointRadius: 2,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } },
            },
        },
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initReorderableTables();
    initRowLinks();
    initRichEditors();
    initAnalyticsChart();
});
