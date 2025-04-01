<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Regency;
use App\Models\Village;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Customer;
use App\Models\District;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CustomerResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CustomerResource\RelationManagers;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('Detail Customer')
                    ->schema([
                        Forms\Components\TextInput::make('id_customer')
                            ->label('ID Customer')
                            ->unique()

                            ->disabled()
                            ->maxLength(20)
                            ->required()
                            ->columnS(5),
                        Forms\Components\Select::make('sektor_usaha_id')
                            ->label('Sektor Usaha')
                            ->relationship('SektorUsaha', 'nama_sektor')
                            ->required()
                            ->preload()
                            ->searchable(),
                        Forms\Components\Select::make('network_id')
                            ->label('Jaringan')
                            ->relationship('Network', 'name')
                            ->required()
                            ->preload()
                            ->searchable(),

                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Perusahaan')
                            ->required()
                            ->maxLength(60),
                        Forms\Components\TextInput::make('kode')
                            ->hint('Kode Customer')
                            ->maxLength(20),
                        Forms\Components\TextInput::make('category_id')
                            ->label('Kategori')
                            ->required()
                            ->numeric()
                            ->default(1),
                        Forms\Components\DatePicker::make('start_date')
                            ->Label('Tanggal mulai')
                            ->required()
                            ->default(now()),
                        Forms\Components\TextArea::make('address')
                            ->label('Alamat Perusahaan')
                            ->required()
                            ->maxLength(255),





                        Forms\Components\Select::make('province_id')
                            ->label('Provinsi')
                            ->relationship('province', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('regency_id', null);
                                $set('district_id', null);
                                $set('village_id', null);
                            })
                            ->required(),
                        Forms\Components\Select::make('regency_id')
                            ->label('Kabupaten')
                            ->relationship('regency', 'name')
                            ->searchable()
                            ->options(fn(Get $get) => Regency::query()
                                ->where('province_id', $get('province_id'))
                                ->pluck('name', 'id'))
                            ->preload()
                            ->live()
                            // Mengisi kolom code dengan kode kabupaten + '0000'
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('district_id', null);

                                if ($state) {
                                    $regency = Regency::find($state);
                                    $YearCode = date('ny');
                                    $sequentialNumber = static::getNextSequentialNumber();
                                    if ($regency) {
                                        $set('id_customer', 'CS-' . $regency->id . '-' . $YearCode . str_pad($sequentialNumber, 3, '0', STR_PAD_LEFT));
                                        $set('district_id', null);
                                        $set('village_id', null);
                                    }
                                }
                            })

                            ->required(),

                        Forms\Components\Select::make('district_id')
                            ->label('Kecamatan')
                            ->relationship('district', 'name')
                            ->options(fn(Get $get) => District::query()
                                ->where('regency_id', $get('regency_id'))
                                ->pluck('name', 'id'))
                            ->preload()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),

                    ])->columns(4),

                Section::make('PIC Customer')
                    ->schema([
                        Forms\Components\TextInput::make('nama_pic')
                            ->label('Nama PIC')
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required(),
                        Forms\Components\TextInput::make('phone')
                            ->label('No HP')
                            ->required()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('position')
                            ->label('Jabatan')
                            ->required()
                            ->maxLength(20),
                        Forms\Components\Hidden::make('detail_pic'),
                    ])
                    ->columns(2)
                    ->afterStateHydrated(function (array $state, Set $set) {
                        if (!empty($state['detail_pic'])) {
                            $picDetails = json_decode($state['detail_pic'], true);
                            $set('nama_pic', $picDetails['nama_pic'] ?? null);
                            $set('email', $picDetails['email'] ?? null);
                            $set('phone', $picDetails['phone'] ?? null);
                            $set('position', $picDetails['position'] ?? null);
                        }
                    })
                    ->beforeStateDehydrated(function (array $state, Set $set) {
                        $set('detail_pic', json_encode([
                            'nama_pic' => $state['nama_pic'] ?? '',
                            'email' => $state['email'] ?? '',
                            'phone' => $state['phone'] ?? '',
                            'position' => $state['position'] ?? '',
                        ]));
                    })->columns(4),
                Section::make('Kontak Customer')
                    ->schema([
                        Forms\Components\TextInput::make('npwp')
                            ->hint('NPWP')
                            ->maxLength(30),
                        Forms\Components\Toggle::make('is_taxed')
                            ->label('Pajak')
                            ->required(),
                        //      Forms\Components\TextInput::make('is_active')
                        //    ->label('Status')
                        //    ->required()
                        //   ->numeric()
                        //   ->default(1),
                    ])->columns(3)


            ]);
    }

    //----------------------------------Perhitungan No resi auto
    public static function getNextSequentialNumber(): int
    {
        $lastResi = static::getModel()::latest('id_customer')->first();
        if (!$lastResi) {
            return 1;
        }

        $lastNumber = substr($lastResi->id_customer, -4);
        return intval($lastNumber) + 1;
    }




    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('SektorUsaha.nama_sektor')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('Network.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('id_customer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('npwp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('is_taxed')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('is_active')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('province_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('regency_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('district_id')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('detail_pic')
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'view' => Pages\ViewCustomer::route('/{record}'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
