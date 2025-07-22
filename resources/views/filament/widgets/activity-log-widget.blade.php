<x-filament::widget>
    <x-filament::card>
        <h2 class="text-lg font-bold mb-4">Últimas Actividades</h2>
        <ul>
            @foreach($this->getRecords() as $activity)
                <li class="mb-2 text-sm">
                    <span class="font-semibold">{{ $activity->causer?->name ?? 'Sistema' }}</span>
                    {{ $activity->description }}
                    en <strong>{{ $activity->subject_type }}</strong>
                    <br>
                    <span class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</span>
                </li>
            @endforeach
        </ul>
    </x-filament::card>
</x-filament::widget>
