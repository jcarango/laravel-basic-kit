<?php

namespace App\Filament\Resources;

use App\Models\Suffragan;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OpponentSuffraganResource extends SuffraganResource
{
    protected static ?string $model = Suffragan::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static ?int $navigationSort = 14;
    protected static ?string $navigationGroup = 'Control Electoral';
    protected static ?string $label = 'Sufragante Opositor';
    protected static ?string $pluralLabel = 'Sufragantes Opositores';
    protected static ?string $slug = 'sufragantes-opositores';

    public static function getNavigationBadge(): ?string
    {
        return static::getEloquentQuery()->count();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('candidate', function (Builder $query) {
                $query->where('is_opponent', true);
            });
    }
}
