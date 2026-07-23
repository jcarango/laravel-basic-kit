<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit.prevent="exportPdf">
            {{ $this->form }}

            <div class="mt-6 flex flex-wrap gap-4">
                <x-filament::button
                    type="button"
                    wire:click="exportPdf"
                    color="danger"
                    icon="heroicon-o-document-arrow-down"
                >
                    Exportar Reporte PDF
                </x-filament::button>

                <x-filament::button
                    type="button"
                    wire:click="exportExcel"
                    color="success"
                    icon="heroicon-o-table-cells"
                >
                    Exportar Reporte Excel
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>
