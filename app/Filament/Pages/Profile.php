<?php

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Hash;

class Profile extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static string $view = 'filament.pages.profile';
    protected static ?string $title = 'Mi Perfil';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'name' => auth()->user()->name,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('name')->label('Nombre'),
            TextInput::make('password')
                ->password()
                ->label('Nueva Contraseña')
                ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null),
        ];
    }

    public function save(): void
    {
        $user = auth()->user();
        $user->fill($this->form->getState());
        $user->save();
        session()->flash('success', 'Perfil actualizado correctamente.');
    }
}
