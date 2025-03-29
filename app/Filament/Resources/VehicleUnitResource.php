<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\VehicleUnit;
use Filament\Resources\Radio;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\VehicleUnitResource\Pages;
use App\Filament\Resources\VehicleUnitResource\RelationManagers;

class VehicleUnitResource extends Resource
{
    protected static ?string $model = VehicleUnit::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Unit Kendaraan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Select::make('vendor_id')
                    ->relationship('vendor', 'nama')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->visible(fn(callable $get) => $get('kepemilikan') === 'vendor'),

                Forms\Components\Radio::make('kepemilikan')
                    ->label('Kepemilikan Kendaraan')
                    ->options([
                        'sendiri' => 'Milik Sendiri',
                        'vendor' => 'Milik Vendor',
                    ])
                    ->required()
                    ->inline()
                    ->reactive(),
                Forms\Components\TextInput::make('jenis_kendaraan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('merk')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('model')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('tahun')
                    ->required()
                    ->integer()
                    ->minValue(2000)
                    ->maxValue(date('Y')),
                Forms\Components\TextInput::make('nomor_plat')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('nomor_rangka')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('nomor_mesin')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('warna')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('foto')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif'])
                    ->directory('vehicle-photos')
                    ->visibility('public')
                    ->maxSize(5120) // 5MB
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->options([
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                        'maintenance' => 'Maintenance',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kepemilikan_vendor')
                    ->label('Kepemilikan / Vendor')
                    ->getStateUsing(function (VehicleUnit $record): string {
                        if ($record->kepemilikan === 'sendiri') {
                            return 'Milik Sendiri';
                        } else {
                            return 'Vendor: ' . ($record->vendor->nama ?? 'Tidak ada');
                        }
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where('kepemilikan', 'like', "%{$search}%")
                            ->orWhereHas('vendor', function ($query) use ($search) {
                                $query->where('nama', 'like', "%{$search}%");
                            });
                    }),
                Tables\Columns\TextColumn::make('jenis_kendaraan')->searchable(),
                Tables\Columns\TextColumn::make('merk')->searchable(),
                Tables\Columns\TextColumn::make('model')->searchable(),
                Tables\Columns\TextColumn::make('nomor_plat')->searchable(),
                Tables\Columns\ImageColumn::make('foto')
                    ->square()
                    ->width(100)
                    ->height(100)
                    ->defaultImageUrl(url('/images/default-vehicle.png'))
                    ->disk('public')
                    ->openUrlInNewTab(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'aktif',
                        'danger' => 'nonaktif',
                        'warning' => 'maintenance',
                    ]),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListVehicleUnits::route('/'),
            'create' => Pages\CreateVehicleUnit::route('/create'),
            'view' => Pages\ViewVehicleUnit::route('/{record}'),
            'edit' => Pages\EditVehicleUnit::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['kepemilikan', 'vendor.nama', 'jenis_kendaraan', 'merk', 'model', 'nomor_plat'];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('vendor');
    }
}
