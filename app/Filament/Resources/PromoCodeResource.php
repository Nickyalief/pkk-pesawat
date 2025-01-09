<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromoCodeResource\Pages;
use App\Filament\Resources\PromoCodeResource\RelationManagers;
use App\Models\PromoCode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PromoCodeResource extends Resource
{
    protected static ?string $model = PromoCode::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'System Manejement';
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                forms\Components\TextInput::make('code')
                ->required()
                ->maxLength(255),
                forms\Components\Select::make('discount_type')
                ->options([
                    'percentage' => 'Percentage',
                    'fixed' => 'Fixed',
                ]),
                forms\Components\TextInput::make('discount')
                ->required()
                ->numeric()
                ->minValue(0),
                forms\Components\DateTimePicker::make('valid_until')
                ->required(),
                forms\Components\Toggle::make('is_uses')
                ->required(),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                tables\Columns\TextColumn::make('code'),
                tables\Columns\TextColumn::make('discount_type'),
                tables\Columns\TextColumn::make('discount')
                ->formatStateUsing(fn (PromoCode $record): string => match ($record->discount_type) {
                    'fixed' => 'Rp' . number_format($record->discount, 0, ',', '.'),
                    'percentage' => $record->discount . '%',
                }),
                tables\Columns\ToggleColumn::make('is_uses'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPromoCodes::route('/'),
            'create' => Pages\CreatePromoCode::route('/create'),
            'edit' => Pages\EditPromoCode::route('/{record}/edit'),
        ];
    }
}
