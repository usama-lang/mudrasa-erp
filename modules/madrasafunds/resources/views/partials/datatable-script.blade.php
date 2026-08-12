@pushOnce('scripts')
<script>
class MfDatatable {
    constructor({ url, tableId, bodyId, paginationId, columns, renderRow, perPage = 15 }) {
        this.url = url;
        this.table = document.getElementById(tableId);
        this.tbody = document.getElementById(bodyId || tableId + '-body');
        this.paginationEl = document.getElementById(paginationId || tableId + '-pagination');
        this.columns = columns;
        this.renderRow = renderRow;
        this.perPage = perPage;
        this.currentPage = 1;
        this.sort = 'id';
        this.direction = 'desc';
        this.search = '';
        this.filters = {};
        this._searchTimer = null;
    }

    init() {
        this._bindSortHeaders();
        this.load();
    }

    async load() {
        this._showLoading();
        const params = new URLSearchParams({
            page: this.currentPage,
            per_page: this.perPage,
            sort: this.sort,
            direction: this.direction,
            search: this.search,
            ...this.filters,
        });

        try {
            const resp = await fetch(this.url + '?' + params.toString(), {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                }
            });

            if (!resp.ok) {
                this.tbody.innerHTML = '<tr><td colspan="100" class="text-center py-8 text-red-500">Error loading data.</td></tr>';
                this._renderPagination(null);
                return;
            }

            const json = await resp.json();
            this._renderRows(json.data);
            this._renderPagination(json.meta);
            this._updateSortIndicators();
        } catch (e) {
            this.tbody.innerHTML = '<tr><td colspan="100" class="text-center py-8 text-gray-500">Failed to fetch data.</td></tr>';
            this._renderPagination(null);
        }
    }

    _renderRows(data) {
        if (!data || data.length === 0) {
            this.tbody.innerHTML = '<tr><td colspan="100" class="text-center py-8 text-gray-500">No records found.</td></tr>';
            return;
        }
        this.tbody.innerHTML = data.map(item => this.renderRow(item)).join('');
    }

    _showLoading() {
        const cols = this.columns.length + 1;
        this.tbody.innerHTML = `<tr><td colspan="${cols}" class="text-center py-8"><div class="flex items-center justify-center gap-2 text-gray-400"><svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>Loading...</div></td></tr>`;
    }

    _renderPagination(meta) {
        if (!meta || meta.last_page <= 1) {
            this.paginationEl.innerHTML = '';
            return;
        }

        const { current_page, last_page, total, per_page } = meta;
        const from = (current_page - 1) * per_page + 1;
        const to = Math.min(current_page * per_page, total);

        let pages = '';
        const start = Math.max(1, current_page - 2);
        const end = Math.min(last_page, current_page + 2);

        for (let i = start; i <= end; i++) {
            const active = i === current_page
                ? 'bg-primary text-white border-primary'
                : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700';
            pages += `<button class="px-3 py-1 text-sm border rounded ${active}" onclick="window.__mfDt.goToPage(${i})">${i}</button>`;
        }

        this.paginationEl.innerHTML = `
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <p class="text-sm text-gray-600 dark:text-gray-400">Showing ${from}&ndash;${to} of ${total}</p>
                <div class="flex items-center gap-1">
                    <button class="px-3 py-1 text-sm border rounded bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed" onclick="window.__mfDt.goToPage(${current_page - 1})" ${current_page === 1 ? 'disabled' : ''}>&laquo; Prev</button>
                    ${pages}
                    <button class="px-3 py-1 text-sm border rounded bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed" onclick="window.__mfDt.goToPage(${current_page + 1})" ${current_page === last_page ? 'disabled' : ''}>Next &raquo;</button>
                </div>
            </div>`;
    }

    goToPage(page) {
        if (page < 1) return;
        this.currentPage = page;
        this.load();
    }

    bindSearch(inputId) {
        const input = document.getElementById(inputId);
        if (!input) return;
        input.addEventListener('input', (e) => {
            clearTimeout(this._searchTimer);
            this._searchTimer = setTimeout(() => {
                this.search = e.target.value;
                this.currentPage = 1;
                this.load();
            }, 350);
        });
    }

    bindFilter(selectId, paramName) {
        const select = document.getElementById(selectId);
        if (!select) return;
        select.addEventListener('change', (e) => {
            this.filters[paramName] = e.target.value;
            this.currentPage = 1;
            this.load();
        });
    }

    bindInput(inputId, paramName) {
        const input = document.getElementById(inputId);
        if (!input) return;
        input.addEventListener('change', (e) => {
            this.filters[paramName] = e.target.value;
            this.currentPage = 1;
            this.load();
        });
    }

    _bindSortHeaders() {
        if (!this.table) return;
        this.table.querySelectorAll('th[data-sort]').forEach(th => {
            th.style.cursor = 'pointer';
            th.addEventListener('click', () => {
                const col = th.dataset.sort;
                if (this.sort === col) {
                    this.direction = this.direction === 'asc' ? 'desc' : 'asc';
                } else {
                    this.sort = col;
                    this.direction = 'asc';
                }
                this.currentPage = 1;
                this.load();
            });
        });
    }

    _updateSortIndicators() {
        if (!this.table) return;
        this.table.querySelectorAll('th[data-sort]').forEach(th => {
            th.querySelector('.sort-icon')?.remove();
            if (th.dataset.sort === this.sort) {
                const icon = document.createElement('span');
                icon.className = 'sort-icon ml-1';
                icon.textContent = this.direction === 'asc' ? '↑' : '↓';
                th.appendChild(icon);
            }
        });
    }

    static badge(active, label, activeClass = 'badge-success', inactiveClass = 'badge-default') {
        const cls = active ? activeClass : inactiveClass;
        return `<span class="badge ${cls}">${label}</span>`;
    }

    static actions({ viewUrl, editUrl, deleteUrl, canEdit, canDelete, deleteConfirm }) {
        let html = '<div class="flex items-center gap-1">';
        if (viewUrl) {
            html += `<a href="${viewUrl}" class="btn btn-default btn-xs" title="View"><iconify-icon icon="lucide:eye" width="14"></iconify-icon></a>`;
        }
        if (canEdit && editUrl) {
            html += `<a href="${editUrl}" class="btn btn-default btn-xs" title="Edit"><iconify-icon icon="lucide:pencil" width="14"></iconify-icon></a>`;
        }
        if (canDelete && deleteUrl) {
            html += `<form method="POST" action="${deleteUrl}" onsubmit="return confirm('${deleteConfirm || 'Delete this record?'}')">
                <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]')?.content}">
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" class="btn btn-danger btn-xs" title="Delete"><iconify-icon icon="lucide:trash-2" width="14"></iconify-icon></button>
            </form>`;
        }
        html += '</div>';
        return html;
    }
}
</script>
@endPushOnce
