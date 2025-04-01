<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Rate;
use Filament\Tables;
use App\Models\Regency;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\District;
use Filament\Forms\Form;
use App\Enums\JenisBarang;
use Filament\Tables\Table;
use App\Enums\CustomerType;
use App\Enums\JenisLayanan;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\RateResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RateResource\RelationManagers;


class RateResource extends Resource
{
    protected static ?string $model = Rate::class;
    protected static ?string $navigationGroup = 'Tarif';
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = ' Tarif Dasar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('')
                    ->schema([
                        Forms\Components\Select::make('customer_id')
                            ->options([
                                CustomerType::UMUM->value => CustomerType::UMUM->value,
                                CustomerType::LANGGANAN->value => CustomerType::LANGGANAN->value
                            ])
                            ->default(CustomerType::UMUM->value)
                            ->live(),

                        Forms\Components\Select::make('nama_customer')
                            ->label('Nama Customer')
                            ->relationship('customer', 'nama')
                            ->visible(fn(Get $get) => $get('customer_id') === CustomerType::LANGGANAN->value),

                        Forms\Components\Select::make('service_id')
                            ->options([
                                JenisLayanan::REGULER->value => JenisLayanan::REGULER->value,
                                JenisLayanan::EXPRESS->value => JenisLayanan::EXPRESS->value
                            ])->default(JenisLayanan::REGULER->value),

                        Forms\Components\Select::make('pack_type_id')
                            ->options([
                                JenisBarang::PACKAGE->value => JenisBarang::PACKAGE->value,
                                JenisBarang::DOCUMENT->value => JenisBarang::KUBIKASI->value,
                                JenisBarang::KUBIKASI->value => JenisBarang::DOCUMENT->value
                            ])->default(JenisBarang::PACKAGE->value),



                    ])->columns(4),

                Section::make('')
                    ->schema([
                        Forms\Components\Select::make('orig_regency_id')->label('Kabupaten/Kota Asal')
                            ->relationship('regency', 'name')
                            ->searchable()
                            ->required()
                            ->default(6371),

                        Forms\Components\Select::make('province_id')->label('Provinsi Tujuan')
                            ->relationship('province', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('regency_id', null);
                                $set('district_id', null);
                                $set('village_id', null);
                            })
                            ->required()
                            ->default(63),

                        Forms\Components\Select::make('regency_id')->label('Kabupaten/Kota Tujuan')
                            ->options(fn(Get $get) => Regency::query()->where('province_id', $get('province_id'))->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('district_id', null);
                                $set('village_id', null);
                            })
                            ->required(),

                        Forms\Components\Select::make('district_id')->label('Kecamatan Tujuan')
                            ->options(fn(Get $get) => District::query()->where('regency_id', $get('regency_id'))->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('village_id', null);
                            })
                            ->required()
                    ])->columns(4),
                Section::make('')
                    ->schema([
                        Forms\Components\TextInput::make('rate_kg')->label('Harga Per Kg')
                            ->numeric(),
                        Forms\Components\TextInput::make('rate_pc')->label('Harga udara')
                            ->numeric(),
                        Forms\Components\TextInput::make('rate_koli')->label('Harga Per koli')
                            ->numeric(),
                        Forms\Components\TextInput::make('min_weight')->label('Berat Minimal')
                            ->numeric()
                            ->default(5),
                    ])->columns(4),

                Section::make('')
                    ->schema([
                        Forms\Components\TextInput::make('etd')
                            ->required()
                            ->maxLength(10),
                        Forms\Components\Select::make('discount')->label('Diskon %')
                            ->options(['0', '10', '20']),
                        //->default(0),
                        Forms\Components\TextInput::make('add_cost')->label('Biaya Tambahan')
                            ->numeric(),
                    ])->columns(3),
                Section::make('')
                    ->schema([
                        Forms\Components\TextArea::make('notes')->label('Catatan')
                        //  ->maxLength(355),
                    ])->columns(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer_id') //nama_customer
                    ->label('Jenis Customer')
                    ->hidden()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.nama') //nama_customer
                    ->label('Nama Langganan')
                    ->sortable()
                    ->hidden()
                    ->searchable(),

                Tables\Columns\TextColumn::make('service_id')->label('Jenis Layanan')
                    //->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pack_type_id')->label('Jenis Barang')
                    //->numeric()
                    ->sortable(),
                //  Tables\Columns\TextColumn::make('province.name')->label('Provinsi Tujuan')

                //        ->sortable(),
                Tables\Columns\TextColumn::make('originCities.name')->label('Kabupaten/Kota Asal')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('regency.name')->label('Kabupaten/Kota Tujuan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('district.name')->label('Kecamatan Tujuan')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('rate_kg')->label('Kg')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rate_pc')->label('udara')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rate_koli')->label('Koli')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_weight')->label('Berat Minimal')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('etd')->label('ETD')
                    ->searchable(),
                Tables\Columns\TextColumn::make('discount')->label('Diskon %')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('add_cost')->label('Biaya Tambahan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('notes')->label('Catatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('customer_type')
                    ->form([
                        Forms\Components\Select::make('customer_id')
                            ->label('Jenis Customer')
                            ->options([
                                CustomerType::UMUM->value => 'Umum',
                                CustomerType::LANGGANAN->value => 'Langganan',
                            ])
                            ->default(CustomerType::UMUM->value),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['customer_id'],
                            fn(Builder $query, $customerType): Builder => $query->where('customer_id', $customerType)
                        );
                    })
                    ->indicateUsing(function (array $data): array {
                        return $data['customer_id'] ? ['Jenis Customer: ' . $data['customer_id']] : [];
                    }),

                Filter::make('customer_name')
                    ->form([
                        Forms\Components\Select::make('nama_customer')
                            ->label('Nama Langganan')
                            ->relationship('customer', 'nama')
                            ->preload()
                            ->searchable()
                            ->disabled(fn(Get $get) => $get('customer_id') === CustomerType::UMUM->value),
                    ])

                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['nama_customer'],
                            fn(Builder $query, $namaCustomer): Builder => $query->whereHas('customer', function ($query) use ($namaCustomer) {
                                $query->where('id', $namaCustomer);
                            })
                        );
                    })
                    ->indicateUsing(function (array $data): array {
                        if ($data['nama_customer'] ?? null) {
                            $customerName = \App\Models\Customer::find($data['nama_customer'])->nama ?? '';
                            return ['Nama Customer: ' . $customerName];
                        }
                        return [];
                    }),
            ])

            ->filtersLayout(Tables\Enums\FiltersLayout::AboveContent)
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
            'index' => Pages\ListRates::route('/'),
            'create' => Pages\CreateRate::route('/create'),
            //  'view' => Pages\ViewRate::route('/{record}'),
            'edit' => Pages\EditRate::route('/{record}/edit'),
        ];
    }
}
