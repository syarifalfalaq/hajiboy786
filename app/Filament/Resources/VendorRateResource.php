<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VendorRateResource\Pages;
use App\Filament\Resources\VendorRateResource\RelationManagers;
use App\Models\VendorRate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;
use App\Models\Regency;
use App\Models\District;

class VendorRateResource extends Resource
{
    protected static ?string $model = VendorRate::class;
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?string $navigationLabel = 'Shipping Rate Vendor';
    protected static ?string $navigationParentItem = 'Management Vendor';





    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('vendor_id')
                    ->relationship('vendor', 'nama')
                    ->required(),

                Forms\Components\select::make('origin_city')
                    ->relationship('regency', 'name')
                    ->searchable()
                    ->default(function () {
                        $user = request()->user();
                        $network = $user->network()->first();
                        return $network ? $network->orig_regency_id : null;
                    })
                    ->required(),


                Forms\Components\select::make('province_id')
                    ->relationship('province', 'name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (Set $set) {
                        $set('regency_id', null);
                        $set('destination_district', null);
                    })
                    ->required()
                    ->default(63),
                Forms\Components\select::make('regency_id')
                    ->options(fn(Get $get) => Regency::query()->where('province_id', $get('province_id'))->pluck('name', 'id')) //Tabel regencies memiliki kolom bernama province_id yang berhubungan dengan provinsi.
                    ->searchable()                                                                   // Field destination_province dalam form Anda menyimpan ID provinsi dengan benar
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (Set $set) {
                        $set('destination_district', null);
                    })
                    ->required(),
                Forms\Components\select::make('destination_district')
                    ->options(fn(Get $get) => District::query()->where('regency_id', $get('regency_id'))->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required(),

                Forms\Components\TextInput::make('harga')

                    ->unique(ignoreRecord: false)
                    ->validationAttribute('Harga')
                    ->validationMessages([
                        'unique' => 'Harga ini sudah digunakan. Mohon masukkan harga yang berbeda.',
                    ])
                    ->required(),
                Forms\Components\Select::make('jenis_pengiriman')
                    ->options([
                        'reguler' => 'Reguler',
                        'express' => 'Express',

                    ])
                    ->required(),
                Forms\Components\TextInput::make('berat_minimum')
                    ->numeric()
                    ->required(),
                Forms\Components\TextInput::make('harga_per_kg_tambahan')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vendor.nama')
                    ->label('Nama Vendor')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('originCities.name'),

                Tables\Columns\BadgeColumn::make('vendor.jenis')
                    ->label('Jenis Vendor')
                    ->searchable()
                    ->colors([
                        'primary' => 'pengiriman',
                        'warning' => 'taksi',
                        'secondary' => 'pickup',

                    ])
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        return $record->vendor->jenis ?? '-';
                    })
                    ->visible(fn(Tables\Contracts\HasTable $livewire): bool => filled($livewire->tableFilters['vendor_id'] ?? null)),

                Tables\Columns\TextColumn::make('regency.name'),
                Tables\Columns\TextColumn::make('district.name'),
                Tables\Columns\TextColumn::make('harga')
                    ->money('idr'),
                Tables\Columns\TextColumn::make('jenis_pengiriman'),
                Tables\Columns\TextColumn::make('berat_minimum'),
                Tables\Columns\TextColumn::make('harga_per_kg_tambahan')
                    ->money('idr'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('vendor_id')
                    ->relationship('vendor', 'nama')
                    ->label('Vendor')
                    ->placeholder('Pilih Vendor')
                    ->searchable(),
            ])
            ->actions([
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
            'index' => Pages\ListVendorRates::route('/'),
            'create' => Pages\CreateVendorRate::route('/create'),
            'edit' => Pages\EditVendorRate::route('/{record}/edit'),
        ];
    }

    public static function afterSave($livewire, $record)
    {
        $data = $livewire->form->getState();
        Log::info('Data to be saved:', $data);

        try {
            $record->update($data);
        } catch (\Exception $e) {
            Log::error('Error updating vendor shipping rate: ' . $e->getMessage());
            Notification::make()
                ->title('Peringatan')
                ->body('Terjadi kesalahan saat menyimpan data: ' . $e->getMessage())
                ->warning()
                ->send();
        }
    }
}
