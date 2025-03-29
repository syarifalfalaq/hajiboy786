<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Vendor;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;

use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\VendorResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\VendorResource\RelationManagers;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Actions;

class VendorResource extends Resource
{
    protected static ?string $model = Vendor::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?string $navigationLabel = 'Vendor';
    protected static ?string $navigationParentItem = 'Management Vendor';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('jenis')
                    ->options([
                        'pengiriman' => 'Perusahaan Pengiriman',
                        'taksi' => 'Taksi',
                        'pickup' => 'Pickup',
                        'kurir' => 'Kurir',
                    ])
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if (in_array($state, ['taksi', 'pickup', 'kurir'])) {
                            $set('show_vehicle_button', true);
                        } else {
                            $set('show_vehicle_button', false);
                        }
                    }),

                Forms\Components\Hidden::make('show_vehicle_button')
                    ->default(false),

                Actions::make([
                    Action::make('add_vehicle')
                        ->label('Tambah Kendaraan')
                        ->icon('heroicon-o-truck')
                        ->modalHeading('Tambah Kendaraan')
                        ->modalButton('Simpan')
                        ->modalWidth('lg')
                        ->form(fn(Form $form) => $form->schema(static::getVehicleForm()))
                        ->action(function (array $data, $record) {
                            // Simpan data kendaraan
                            $record->vehicleUnits()->create($data);
                        })
                        ->hidden(fn(callable $get) => !$get('show_vehicle_button')),
                ]),
                Forms\Components\Textarea::make('alamat')
                    ->required()
                    ->maxLength(65535),
                Forms\Components\TextInput::make('telepon')
                    ->tel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->options([
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama'),
                Tables\Columns\BadgeColumn::make('jenis')
                    ->colors([
                        'info' => 'pengiriman',
                        'warning' => 'taksi',
                        'success' => 'pickup',
                        'danger' => 'kurir'

                    ]),
                Tables\Columns\TextColumn::make('telepon'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'aktif',
                        'danger' => 'nonaktif',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis')
                    ->options([
                        'pengiriman' => 'Perusahaan Pengiriman',
                        'taksi' => 'Taksi',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    public static function getRelations(): array
    {
        return [
            RelationManagers\ShippingRatesRelationManager::class,

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVendors::route('/'),
            'create' => Pages\CreateVendor::route('/create'),
            'edit' => Pages\EditVendor::route('/{record}/edit'),
        ];
    }


    protected static function getVehicleForm(): array
    {
        return [
            Forms\Components\TextInput::make('jenis_kendaraan')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('merk')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('model')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('nomor_plat')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            // ... tambahkan field lain yang diperlukan ...
        ];
    }
}
