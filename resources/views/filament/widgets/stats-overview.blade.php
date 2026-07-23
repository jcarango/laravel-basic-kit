<div>
    <form wire:submit.prevent="submit">
        <div class="grid grid-cols-2 gap-4">
            {{ $this->form }}
        </div>
        <button type="submit" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded">
            Filtrar
        </button>
    </form>
    <div class="mt-8">
        @foreach ($this->getStats() as $stat)
            <div class="p-4 bg-gray-100 rounded-lg shadow">
                <h3 class="text-lg font-semibold">{{ $stat->getLabel() }}</h3>
                <p class="text-2xl">{{ $stat->getValue() }}</p>
                <p class="text-sm text-gray-500">{{ $stat->getDescription() }}</p>
            </div>
        @endforeach
    </div>
</div>
