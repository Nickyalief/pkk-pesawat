<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FlightResource\Pages;
use App\Filament\Resources\FlightResource\RelationManagers;
use App\Models\Flight;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FlightResource extends Resource
{
    protected static ?string $model = Flight::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-on-square-stack';
    protected static ?string $navigationGroup = 'System Manejement';
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                forms\Components\Wizard::make([
                    forms\Components\Wizard\Step::make('Flight Information')
                        ->schema([
                            forms\Components\TextInput::make('flight_number')
                            ->required()
                            ->unique(ignoreRecord: true),
                            forms\Components\Select::make('airline_id')
                            ->relationship('airline', 'name')
                            ->required()
                        ]),
                    forms\Components\Wizard\Step::make('Flight Segments')
                        ->schema([
                            forms\Components\Repeater::make('flight_segments')
                            ->relationship('segments')
                            ->schema([
                                forms\Components\TextInput::make('sequence')
                                ->numeric()
                                ->required()
                                ->minValue(1)
                                ->maxValue(100),
                                forms\Components\Select::make('airport_id')
                                ->relationship('airport', 'name')
                                ->required(),
                                forms\Components\DateTimePicker::make('time')
                                ->required()
                            ])
                            ->collapsed(false)
                            ->minItems(1)
                            ->defaultItems(1),
                        ]),
                    forms\Components\Wizard\Step::make('Flight Class')
                        ->schema([
                            forms\Components\Repeater::make('Flight_classes')
                            ->relationship('classes')
                            ->schema([
                                forms\Components\Select::make('class_type')
                                ->options([
                                    'Economy' => 'Economy',
                                    'Business' => 'Business',
                                ])
                                ->required(),
                                forms\Components\TextInput::make('price')
                                ->required()
                                ->prefix('IDR')
                                ->numeric()
                                ->minValue(0),
                                forms\Components\TextInput::make('total_seats')
                                ->required()
                                ->numeric()
                                ->minValue(1)
                                ->label('Total Seats'),
                                forms\Components\Select::make('facilities')
                                ->relationship('facilities', 'name')
                                ->multiple(),
                                
                                
                            ])
                        ]),
                ])->columnspan(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('flight_number'),
                Tables\Columns\TextColumn::make('airline.name'),
                Tables\Columns\TextColumn::make('segments')
                    ->label('Route & Duration')
                    ->formatStateUsing(function (Flight $record): string {
                        $firtsSegment = $record->segments->first();
                        $lastSegment = $record->segments->last();
                        $route = $firtsSegment->airport->iata_code . ' - ' . $lastSegment->airport->iata_code;
                        $duration = (new \DateTime($firtsSegment->time))->format('d F Y H:i') . ' - ' . (new \DateTime($lastSegment->time))->format('d F Y H:i');
                        return $route . '|' . $duration;
                    }),
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
            'index' => Pages\ListFlights::route('/'),
            'create' => Pages\CreateFlight::route('/create'),
            'edit' => Pages\EditFlight::route('/{record}/edit'),
        ];
    }
}
