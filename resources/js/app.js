import Alpine from 'alpinejs';
import Sortable from 'sortablejs';

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

document.addEventListener('DOMContentLoaded', () => {
    initReorderableTables();
    initRowLinks();
});
