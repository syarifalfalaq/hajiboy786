<?php

namespace App\Filament\Resources\VendorResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Regency;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\District;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class ShippingRatesRelationManager extends RelationManager
{
    protected static string $relationship = 'shippingRates';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\select::make('origin_city')
                    ->label('Kota Asal')
                    ->relationship('regency', 'name')
                    ->searchable()
                    ->default(function () {
                        $user = request()->user();
                        $network = $user->network()->first();
                        return $network ? $network->orig_regency_id : null;
                    })
                    ->required(),


                Forms\Components\select::make('province_id')
                    ->label('Provinsi Tujuan')
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
                    ->label('Kota Tujuan')
                    ->options(fn(Get $get) => Regency::query()->where('province_id', $get('province_id'))->pluck('name', 'id')) //Tabel regencies memiliki kolom bernama province_id yang berhubungan dengan provinsi.
                    ->searchable()                                                                   // Field destination_province dalam form Anda menyimpan ID provinsi dengan benar
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (Set $set) {
                        $set('destination_district', null);
                    })
                    ->required(),
                Forms\Components\select::make('destination_district')
                    ->label('Kecamatan Tujuan')
                    ->options(fn(Get $get) => District::query()->where('regency_id', $get('regency_id'))->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required(),
                Forms\Components\TextInput::make('harga')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\Select::make('jenis_pengiriman')
                    ->label('Jenis Pengiriman')
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
                // Tambahkan field lain sesuai kebutuhan
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('originCities.name')
                    ->label('Kota Asal'),

                Tables\Columns\TextColumn::make('regency.name')
                    ->label('Kota Tujuan'),
                Tables\Columns\TextColumn::make('district.name')
                    ->label('Kecamatan Tujuan'),
                Tables\Columns\TextColumn::make('harga')
                    ->money('idr'),

                Tables\Columns\TextColumn::make('berat_minimum'),
                Tables\Columns\TextColumn::make('harga_per_kg_tambahan')
                    ->money('idr'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
