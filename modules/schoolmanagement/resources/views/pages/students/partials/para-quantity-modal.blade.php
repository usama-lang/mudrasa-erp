<div x-data="{
    open: false,
    isLoading: false,
    studentId: null,
    studentName: '',
    value: '',
    error: '',
    init() {
        window.addEventListener('open-para-quantity-modal', (event) => {
            this.studentId = event.detail.id;
            this.studentName = event.detail.name;
            this.value = event.detail.value ?? '';
            this.error = '';
            this.open = true;
        });
    },
    async save() {
        this.isLoading = true;
        this.error = '';

        try {
            const response = await fetch(`{{ url('admin/school/students') }}/${this.studentId}/para-quantity`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                },
                body: JSON.stringify({ para_quantity: this.value }),
            });

            const data = await response.json();

            if (!response.ok) {
                this.error = data.message || Object.values(data.errors || {}).flat()[0] || '{{ bi('Something went wrong.') }}';
                return;
            }

            this.open = false;
            window.__currentDt?.load();
        } catch (e) {
            this.error = '{{ bi('Something went wrong.') }}';
        } finally {
            this.isLoading = false;
        }
    }
}">
    <x-modal id="para-quantity-modal">
        <x-slot name="header">{{ bi('Para Quantity') }}</x-slot>

        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4" x-text="studentName"></p>

        <template x-if="error">
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3 mb-4 text-sm text-red-700 dark:text-red-300" x-text="error"></div>
        </template>

        <label class="form-label" for="para-quantity-input">{{ bi('Para Quantity') }}</label>
        <input
            id="para-quantity-input"
            type="number"
            min="0"
            max="30"
            step="0.5"
            class="form-control"
            x-model="value"
            @keydown.enter="save()"
        >

        <x-slot name="footer">
            <button type="button" class="btn btn-default" @click="open = false" :disabled="isLoading">
                {{ __('Cancel') }}
            </button>
            <button type="button" class="btn btn-primary" @click="save()" :disabled="isLoading">
                <span x-show="!isLoading">{{ __('Save') }}</span>
                <span x-show="isLoading">{{ __('Saving...') }}</span>
            </button>
        </x-slot>
    </x-modal>
</div>
