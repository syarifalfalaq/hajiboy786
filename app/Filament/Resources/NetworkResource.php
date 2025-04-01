<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NetworkResource\Pages;
use App\Filament\Resources\NetworkResource\RelationManagers;
use App\Models\Network;
use App\Models\Regency;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\District;
use App\Models\Province;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class NetworkResource extends Resource
{
    protected static ?string $model = Network::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?string $navigationLabel = 'Kantor Cabang';
    protected static ?int $navigationSort = 1; // Adjust as needed
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Asal')
                    ->schema([
                        Forms\Components\Radio::make('type_id')->label('Jenis Kantor Cabang')
                            ->options([
                                1 => 'Kantor Utama',
                                2 => 'Kantor Cabang',
                                3 => 'Outlet',
                            ])
                            ->required(),

                        Forms\Components\select::make('orig_province_id')
                            ->label('Asal provinsi')
                            ->relationship('province', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('orig_regency_id', null);
                                $set('orig_district_id', null);
                                $set('orig_village_id', null);
                            })
                            ->required()
                            ->default(63),

                        Forms\Components\Select::make('orig_regency_id')
                            ->label('Asal Kota')
                            ->options(fn(Get $get) => Regency::query()
                                ->where('province_id', $get('orig_province_id'))
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('orig_district_id', null);

                                // Mengisi kolom code dengan kode kabupaten + '0000'
                                if ($state) {
                                    $regency = Regency::find($state);
                                    if ($regency) {
                                        $set('code', $regency->id . '00');
                                    }
                                }
                            })
                            ->required(),

                        Forms\Components\select::make('orig_district_id')
                            ->label('Asal Kecamatan')
                            ->options(fn(Get $get) => District::query()->where('regency_id', $get('orig_regency_id'))->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->live()

                    ])->columns(2),
                Forms\Components\TextInput::make('code')
                    ->label('Kode Kantor Cabang')
                    ->required()
                    ->maxLength(8)
                    ->disabled() // Menonaktifkan input manual
                    ->dehydrated(), // Memastikan nilai tetap disimpan meskipun field dinonaktifkan
                Forms\Components\TextInput::make('name')
                    ->label('Nama Kantor Cabang')
                    ->required()
                    ->maxLength(60),
                Forms\Components\TextInput::make('address')
                    ->label('Alamat Kantor Cabang')
                    ->maxLength(255),
                Forms\Components\TextInput::make('kordinat')
                    ->label('Kordinat Kantor Cabang')
                    ->maxLength(60),
                Forms\Components\TextInput::make('postal_code')
                    ->label('Kode Pos Kantor Cabang')
                    ->maxLength(10),
                Forms\Components\TextInput::make('phone')
                    ->label('Nomor Telepon Kantor Cabang')
                    ->tel()
                    ->maxLength(20),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(60),

            ]);
    }

    //----------------------------------Perhitungan No resi auto
    protected static function getNextSequentialNumber(): int
    {
        $lastResi = static::getModel()::latest('noresi')->first();
        if (!$lastResi) {
            return 1;
        }

        $lastNumber = substr($lastResi->noresi, -4);
        return intval($lastNumber) + 1;
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type_id')
                    ->label('Jenis Kantor Cabang')
                    ->formatStateUsing(function ($state) {
                        $types = [
                            1 => 'Kantor Utama',
                            2 => 'Kantor Cabang',
                            3 => 'Outlet',
                        ];
                        return $types[$state] ?? 'Tidak Diketahui';
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kordinat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('postal_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('province.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('Regency.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('District.name')
                    ->numeric()
                    ->sortable(),
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
                //
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
            'index' => Pages\ListNetworks::route('/'),
            'create' => Pages\CreateNetwork::route('/create'),
            'edit' => Pages\EditNetwork::route('/{record}/edit'),
        ];
    }
}
