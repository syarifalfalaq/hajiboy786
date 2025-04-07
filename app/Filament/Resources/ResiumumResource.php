<?php

namespace App\Filament\Resources;


use Filament\Forms;
use TextInput\Mask;
use App\Models\Rate;
use Filament\Tables;
use App\Enums\Layanan;
use App\Enums\AdminFee;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Resiumum;
use App\Models\Sequence;
//------------------------- PERUBAHAN
use Filament\Forms\Form;
use App\Enums\JenisBarang;
use Filament\Tables\Table;
use App\Enums\CustomerType;
use App\Enums\JenisLayanan;
use Filament\Support\RawJs;
use App\Enums\ResiStatusEnum;
use Filament\Resources\Resource;
use App\Models\rate as ModelsRate;
use App\Services\ResiStatusService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Radio;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use App\Services\ResiumumCalculations;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Section;
//--- khusus progress resi
use Filament\Forms\Components\Repeater;
use Filament\Tables\Actions\BulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Components\Actions\Action;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Filament\Resources\ResiumumResource\Pages;
use Filament\Forms\Components\Actions\ActionGroup;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ResiumumResource\RelationManagers;
use Filament\Forms\Components\Split;

//--------------end
class ResiumumResource extends Resource
{
    protected static ?string $model = Resiumum::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Resi umum';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Resi')
                    ->schema([

                        Forms\Components\Select::make('ty_cust')
                            ->label('Jenis Customer')
                            ->options([
                                CustomerType::UMUM->value => CustomerType::UMUM->value,
                                CustomerType::LANGGANAN->value => CustomerType::LANGGANAN->value
                            ])
                            ->default(CustomerType::UMUM->value)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                if ($get('ty_cust') === CustomerType::UMUM->value) {
                                    $set('nama_customer', null);
                                    $set('province_id', null);
                                    $set('regency_id', null);
                                    $set('district_id', null);
                                    $set('layanan_id', null);
                                    $set('charged_on', null);
                                }
                                // Jangan mengubah nilai ty_cust di sini
                                ResiumumCalculations::calculateTariff($get, $set);
                            }),
                        Forms\Components\Select::make('nama_customer')
                            ->label('Nama Customer')
                            ->relationship('customer', 'nama')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $set('province_id', null);
                                $set('regency_id', null);
                                $set('district_id', null);
                                $set('layanan_id', null);

                                if ($get('ty_cust') === CustomerType::UMUM->value) {
                                    $set('province_id', null);
                                    $set('regency_id', null);
                                    $set('district_id', null);
                                    $set('layanan_id', null);
                                    $set('charged_on', null);
                                }
                                ResiumumCalculations::calculateTariff($get, $set);
                            })
                            ->visible(fn(Get $get) => $get('ty_cust') === CustomerType::LANGGANAN->value)
                            ->exists('customers', 'id') // Memastikan id customer ada di tabel customers
                            ->validationAttribute('nama_customer'), // Mengubah atribut validasi untuk pesan error yang lebih jelas



                        Forms\Components\Select::make('orig_regency_id')
                            ->label('Kabupaten/Kota Asal')
                            ->relationship('regency', 'name')
                            ->searchable()
                            ->required()
                            ->default(function () {
                                $user = request()->user();
                                $network = $user->network()->first();
                                return $network ? $network->orig_regency_id : null;
                            })
                            ->reactive()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $set('province_id', null);
                                $set('regency_id', null);
                                $set('district_id', null);

                                ResiumumCalculations::calculateTariff($get, $set);
                            }),
                        Forms\Components\Select::make('province_id')
                            ->label('Provinsi')
                            ->options(function (Get $get) {
                                $tyCust = $get('ty_cust');
                                $namacustId = $get('nama_customer');
                                $origRegencyId = $get('orig_regency_id');

                                if ($tyCust === CustomerType::UMUM->value) {
                                    // untuk 'umum' customers, tarik semua data provinsi dari rates
                                    return ModelsRate::join('provinces', 'rates.province_id', '=', 'provinces.id')
                                        ->distinct()
                                        ->pluck('provinces.name', 'rates.province_id')
                                        ->toArray();
                                } elseif ($tyCust === CustomerType::LANGGANAN->value) {
                                    // For 'langganan' customers, use the existing logic
                                    if (!$origRegencyId && !$namacustId) {
                                        return [];
                                    }
                                    return ModelsRate::where('nama_customer', $namacustId)
                                        ->join('provinces', 'rates.province_id', '=', 'provinces.id')
                                        ->distinct()
                                        ->pluck('provinces.name', 'rates.province_id')
                                        ->toArray();
                                }
                                return [];
                            })
                            ->Columnspan(1)
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $set('regency_id', null);
                                $set('district_id', null);
                                ResiumumCalculations::calculateTariff($get, $set);
                            })
                            ->disabled(fn(Get $get): bool => !$get('orig_regency_id'))
                            ->dehydrated(fn(Get $get): bool => filled($get('orig_regency_id')))
                            ->visible(fn(Get $get): bool => $get('orig_regency_id') !== null),

                        Forms\Components\Select::make('regency_id')
                            ->label('Kabupaten/Kota Tujuan')
                            ->options(function (Get $get) {
                                $tyCust = $get('ty_cust');
                                $namaCustomer = $get('nama_customer');
                                $origRegencyId = $get('orig_regency_id');
                                $provinceId = $get('province_id');

                                if (!$origRegencyId || !$provinceId || (!$tyCust && !$namaCustomer)) {
                                    return [];
                                }

                                $query = ModelsRate::where('rates.orig_regency_id', $origRegencyId)
                                    ->where('rates.province_id', $provinceId);

                                if ($tyCust === CustomerType::UMUM->value) {
                                    // Untuk pelanggan umum
                                    $query->where('rates.customer_id', CustomerType::UMUM->value);
                                } elseif ($tyCust === CustomerType::LANGGANAN->value) {
                                    // Untuk pelanggan langganan
                                    if ($namaCustomer) {
                                        $query->where('rates.nama_customer', $namaCustomer);
                                    } else {
                                        $query->where('rates.customer_id', CustomerType::LANGGANAN->value);
                                    }
                                }

                                return $query->join('regencies', 'rates.regency_id', '=', 'regencies.id')
                                    ->distinct()
                                    ->pluck('regencies.name', 'rates.regency_id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $set('district_id', null);
                                $set('weight', null);
                                $set('koli', null);
                                $set('pcs_count', null);
                                $set('layanan_id', null);
                                ResiumumCalculations::calculateTariff($get, $set);
                            })
                            ->disabled(fn(Get $get): bool => !$get('orig_regency_id') || !$get('province_id'))
                            ->dehydrated(fn(Get $get): bool => filled($get('orig_regency_id')) && filled($get('province_id')))
                            ->columnSpan(1),

                        Forms\Components\Select::make('district_id')
                            ->label('Kecamatan Tujuan')
                            ->options(function (Get $get) {
                                $regencyId = $get('regency_id');
                                $tyCust = $get('ty_cust');
                                $namaCustomer = $get('nama_customer');

                                if (!$regencyId) {
                                    return [];
                                }

                                $query = ModelsRate::where('rates.regency_id', $regencyId);

                                if ($tyCust === CustomerType::UMUM->value) {
                                    $query->where('rates.customer_id', CustomerType::UMUM->value);
                                } elseif ($tyCust === CustomerType::LANGGANAN->value && $namaCustomer) {
                                    $query->where('rates.nama_customer', $namaCustomer);
                                }

                                return $query->join('districts', 'rates.district_id', '=', 'districts.id')
                                    ->distinct()
                                    ->pluck('districts.name', 'rates.district_id')
                                    ->toArray();
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                // Hanya perbarui field yang diperlukan
                                $set('layanan_id', null);
                                ResiumumCalculations::calculateTariff($get, $set);
                            })
                            ->disabled(fn(Get $get): bool => !$get('regency_id'))
                            ->dehydrated(fn(Get $get): bool => filled($get('regency_id'))),


                        Forms\Components\Select::make('layanan_id')
                            ->label('Jenis Layanan')
                            ->options(function (Get $get) {
                                $origRegencyId = $get('orig_regency_id');
                                $districtId = $get('district_id');
                                $tyCust = $get('ty_cust');
                                $namaCustomer = $get('nama_customer');

                                if (!$origRegencyId || !$districtId || !$tyCust || !$namaCustomer) {
                                    return [];
                                }


                                return \App\Models\Rate::where('orig_regency_id', $origRegencyId)
                                    ->where('district_id', $districtId)
                                    ->where('nama_customer', $namaCustomer)
                                    ->distinct()
                                    ->orderBy('service_id')
                                    ->pluck('service_id', 'service_id')
                                    ->toArray();
                            })

                            /*  $query = \App\Models\Rate::where('orig_regency_id', $origRegencyId)
                                    ->where('district_id', $districtId);

                                if ($tyCust === CustomerType::UMUM->value) {
                                    $query->where('customer_id', CustomerType::UMUM->value);
                                } elseif ($tyCust === CustomerType::LANGGANAN->value && $namaCustomer) {
                                    $query->where('nama_customer', $namaCustomer);
                                }

                                return $query->distinct()
                                    ->orderBy('service_id')
                                    ->pluck('service_id', 'service_id')
                                    ->toArray();
                            })
                                    */
                            ->searchable()
                            ->preload()
                            ->required()

                            ->reactive()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                ResiumumCalculations::calculateTariff($get, $set);
                            }),

                        /*Forms\Components\Select::make('ty_cust')
                            ->label('Jenis Customer')
                            ->options(function (Get $get) {
                                $origRegencyId = $get('orig_regency_id');
                                $regencyId = $get('regency_id');
                                $layananId = $get('layanan_id');

                                if (!$origRegencyId || !$regencyId || !$layananId) {
                                    return [];
                                }

                                return \App\Models\Rate::where('orig_regency_id', $origRegencyId)
                                    ->where('regency_id', $regencyId)
                                    ->where('service_id', $layananId)
                                    ->distinct()
                                    ->orderBy('customer_id')
                                    ->pluck('customer_id', 'customer_id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                ResiumumCalculations::calculateTariff($get, $set);
                            })
                            ->Columnspan(1),*/


                        Forms\Components\TextInput::make('weight')
                            ->label('Berat')
                            ->required()
                            ->numeric()
                            ->hint(fn(Get $get) => "Berat minimum: " . ($get('$rate->min_weight') ?? 5) . " kg")
                            ->reactive()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                ResiumumCalculations::calculateTariff($get, $set);
                            }),
                        Forms\Components\TextInput::make('koli')
                            // ->required()
                            ->numeric()
                            ->reactive()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                ResiumumCalculations::calculateTariff($get, $set);
                            }),
                        Forms\Components\TextInput::make('pcs_count')
                            ->label('Dus')
                            //->required()
                            ->numeric()
                            ->reactive()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                ResiumumCalculations::calculateTariff($get, $set);
                            }),
                        Forms\Components\Select::make('charged_on')
                            ->label('Jenis Kiriman')
                            ->options(function (Get $get) {
                                $origRegencyId = $get('orig_regency_id');
                                $regencyId = $get('regency_id');
                                $districtId = $get('district_id');

                                if (!$origRegencyId || !$regencyId || !$districtId) {
                                    return [];
                                }

                                return \App\Models\Rate::where('orig_regency_id', $origRegencyId)
                                    ->where('district_id', $districtId)
                                    ->distinct()
                                    ->orderBy('pack_type_id')
                                    ->pluck('pack_type_id', 'pack_type_id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                ResiumumCalculations::calculateTariff($get, $set);
                            })
                            ->columnSpan(1),
                        //---------------------
                        Forms\Components\Select::make('satuan_barang') /// tidak ada di database ...sementara
                            ->label('Satuan Barang')
                            ->options([
                                'rate_kg' => 'Kilogram (Kg)',
                                'rate_koli' => 'Koli',
                                'rate_pc' => 'Kubik',
                            ])
                            ->required()
                            ->dehydrated(true)
                            ->reactive()
                            ->afterStateUpdated(function (Get $get, Set $set) {

                                ResiumumCalculations::calculateTariff($get, $set);
                            })

                            ->columnSpan(1),
                        Forms\Components\TextInput::make('discount')
                            ->prefix('Rp')
                            //->mask(RawJs::make(<<<'JS'
                            //   $money($input,0)
                            // JS))
                            ->afterStateUpdated(function (Get $get, Set $set) {

                                ResiumumCalculations::calculateOngkir($get, $set);
                            })
                            ->numeric()
                            ->default(0),
                        Forms\Components\Select::make('biaya_admin')
                            ->options([AdminFee::Charges->value => AdminFee::Charges->value, AdminFee::Free->value  => AdminFee::Free->value])->default(AdminFee::Free->value)
                            ->prefix('Rp')
                            ->reactive()
                            ->lazy(),


                        Forms\Components\Select::make('etd')
                            ->label('Estimated Time of Delivery')
                            ->dehydrated(true)
                            ->reactive()
                            ->lazy()
                            ->options(function (Get $get) {
                                $origRegencyId = $get('orig_regency_id');
                                $districtId = $get('district_id');

                                if (!$origRegencyId || !$districtId) {
                                    return [];
                                }

                                return \App\Models\Rate::where('orig_regency_id', $origRegencyId)
                                    ->where('district_id', $districtId)
                                    ->distinct()
                                    ->orderBy('etd')
                                    ->pluck('etd', 'etd')
                                    ->toArray();
                            })
                            ->afterStateHydrated(function (Get $get, Set $set) {
                                $origRegencyId = $get('orig_regency_id');
                                $districtId = $get('district_id');

                                if (!$origRegencyId || !$districtId) {
                                    $set('etd', null);
                                    return;
                                }

                                $etd = \App\Models\Rate::where('orig_regency_id', $origRegencyId)
                                    ->where('district_id', $districtId)
                                    ->value('etd');

                                $set('etd', $etd);
                            })
                            ->disabled(fn(Get $get) => !$get('orig_regency_id') || !$get('district_id'))
                            ->placeholder('Select ETD'),


                    ])->columns(3),


                //--------------DIMENSI
                Section::make('Dimensi')
                    ->schema([
                        Repeater::make('items_detail')
                            ->schema([
                                TextInput::make('p')
                                    ->label('Panjang (cm)')
                                    ->required()
                                    ->formatStateUsing(function ($state) {
                                        return number_format((float) str_replace([',', '.'], '', $state), 0, ',', '.');
                                    })
                                    ->reactive(),

                                TextInput::make('l')
                                    ->label('Lebar (cm)')
                                    ->required()
                                    ->formatStateUsing(function ($state) {
                                        return number_format((float) str_replace([',', '.'], '', $state), 0, ',', '.');
                                    })
                                    ->reactive(),

                                TextInput::make('t')
                                    ->label('Tinggi (cm)')
                                    ->required()
                                    ->formatStateUsing(function ($state) {
                                        return number_format((float) str_replace([',', '.'], '', $state), 0, ',', '.');
                                    })
                                    ->reactive(),

                                TextInput::make('volume')
                                    ->label('Kubikasi CBM (m3)')
                                    ->disabled()
                                    ->dehydrated(true),

                                TextInput::make('weight_volume')
                                    ->label('Berat Volume')
                                    ->disabled()
                                    ->dehydrated(true),
                            ])
                            ->columns(5)
                            ->reactive()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $items_detail = $get('items_detail');
                                $totalVolume = 0;
                                $totalWeightVolume = 0;

                                foreach ($items_detail as $key => $item) {
                                    $p = floatval($item['p'] ?? 0);
                                    $l = floatval($item['l'] ?? 0);
                                    $t = floatval($item['t'] ?? 0);
                                    //----------simpan dan tampilkan juga  di

                                    $volume = ($p * $l * $t) / 1000000;
                                    $weightVolume = ($p * $l * $t) / 4000;

                                    $set("items_detail.{$key}.volume", number_format($volume, 6));
                                    $set("items_detail.{$key}.weight_volume", number_format($weightVolume, 2));

                                    $totalVolume += $volume;
                                    $totalWeightVolume += $weightVolume;
                                }

                                $set('total_volume', number_format($totalVolume, 3));
                                $set('total_weight_volume', number_format($totalWeightVolume, 0));
                            }),

                        TextInput::make('total_volume')
                            ->label('Total Kubikasi CBM (m3)')
                            ->disabled()
                            ->reactive()
                            ->dehydrated(),

                        TextInput::make('total_weight_volume')
                            ->label('Total Berat Volume')
                            ->disabled()
                            ->reactive()
                            ->dehydrated(),
                        //->dehydrated(false),
                    ]),


                Section::make('Asuransi')
                    ->schema([
                        Forms\Components\Radio::make('insurance')
                            ->label('Asuransi')
                            ->options([
                                'ada' => 'Ada',
                                'tidak_ada' => 'Tidak ada',
                            ])
                            ->required()
                            ->reactive()
                            ->default('tidak_ada')
                            ->columnspan(1),

                        TextInput::make('insurance_value')
                            ->label('Nilai Paket Kiriman ')
                            ->dehydrated()
                            ->hidden(fn(Get $get) => $get('insurance') !== 'ada')
                            ->hint(' isi kemudian klik spasi+tab')
                            ->prefix('Rp')
                            ->mask(RawJs::make(<<<'JS'
                        $money($input, ',', '.', 0)
                        JS))
                            ->reactive()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                if ($get('insurance') === 'ada') {
                                    $insuranceValue = (float) str_replace([',', '.'], '', $get('insurance_value'));
                                    $insuranceCost = ($insuranceValue * 0.0035); // 0.35%
                                    $set('insurance_cost', number_format($insuranceCost, 0, ',', '.'));
                                } else {
                                    $set('insurance_cost', '0,0');
                                }
                            })
                            ->formatStateUsing(function ($state) {
                                return number_format((float) str_replace([',', '.'], '', $state), 0, ',', '.');
                            }),
                        TextInput::make('insurance_cost')
                            ->label('Total Biaya Asuransi')
                            ->prefix('Rp')
                            ->disabled()
                            ->reactive()
                            ->lazy()
                            ->mask(RawJs::make(<<<'JS'
                        $money($input, ['', '.'] ,0)
                    JS))
                            ->dehydrated(false)
                            ->hidden(fn(Get $get) => $get('insurance') !== 'ada'),
                        Forms\Components\TextInput::make('biaya_packing')
                            ->prefix('Rp')
                            ->lazy()
                            // ->mask(RawJs::make(<<<'JS'
                            // $money($input,0)
                            // JS))
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('biaya_tambahan')
                            ->prefix('Rp')
                            //->mask(RawJs::make(<<<'JS'
                            //  $money($input, 0)
                            //  JS))
                            ->numeric()
                            ->default(0),

                    ])
                    ->columns(3),


                //-------------------------------BIAYA
                Section::make('Biaya')
                    ->schema([
                        TextInput::make('tariff')
                            ->label('Biaya Tarif')
                            ->prefix('Rp')
                            ->mask(RawJs::make(<<<'JS'
                              $money($input, 0)
    JS))
                            ->required()
                            ->numeric()
                            ->reactive()
                            ->dehydrateStateUsing(fn($state) => str_replace(['.', ','], '', $state))
                            ->hint('Hitung')
                            ->suffixAction(
                                Action::make('recalculateTariff')
                                    ->icon('heroicon-m-arrow-path')
                                    ->action(function (Get $get, Set $set) {
                                        $totalWeightVolume = floatval(str_replace([',', '.'], '', $get('total_weight_volume')));
                                        $weight = floatval(str_replace([',', '.'], '', $get('weight')));
                                        $koli = intval($get('koli'));
                                        $pcsCount = intval($get('pcs_count'));
                                        $deskBrg = $get('satuan_barang');

                                        $effectiveWeight = max($weight, $totalWeightVolume);

                                        $rate = Rate::where('customer_id', $get('ty_cust'))
                                            ->where('service_id', $get('layanan_id'))
                                            ->where('orig_regency_id', $get('orig_regency_id'))
                                            ->where('province_id', $get('province_id'))
                                            ->where('regency_id', $get('regency_id'))
                                            ->where('district_id', $get('district_id'))
                                            ->first();

                                        if ($rate) {
                                            $newTariff = match ($deskBrg) {
                                                'rate_kg' => $rate->rate_kg * $effectiveWeight,
                                                'rate_koli' => $rate->rate_koli * $koli,
                                                'rate_pc' => $rate->rate_pc * $pcsCount,
                                                default => 0,
                                            };

                                            $newTariff = round($newTariff, -2);

                                            $set('tariff', number_format($newTariff, 0, ',', '.'));

                                            // Simpan nilai asli (tanpa format) ke state untuk disimpan ke database
                                            $set('tariff_raw', $newTariff);
                                        } else {
                                            $set('tariff', '0');
                                            $set('tariff_raw', 0);
                                        }

                                        // Hitung ulang field lain yang tergantung
                                        ResiumumCalculations::calculateOngkir($get, $set);
                                        ResiumumCalculations::calculateOngkirTotal($get, $set);
                                    })
                            ),

                        /////////////////////////////

                        Forms\Components\TextInput::make('ongkir')
                            ->label('Sub Ongkir')
                            ->prefix('Rp')
                            ->hint('Tarif-Diskon')
                            ->required()
                            ->numeric()
                            ->reactive()
                            ->lazy()
                            ->mask(RawJs::make(<<<'JS'
                            $money($input, ',', '.', 0)
                        JS))
                            ->readonly(fn($record) => !request()->user()->can('edit', $record))
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                Log::info('Memanggil calculateOngkir');
                                ResiumumCalculations::calculateOngkir($get, $set);
                                ResiumumCalculations::calculateOngkirTotal($get, $set);
                            })
                            ->disabled(),
                        ///////////////////==========================

                        TextInput::make('total')
                            ->prefix('Rp')
                            ->hint('ongkir + biaya admin + biaya packing + biaya tambahan + asuransi')
                            ->mask(RawJs::make(<<<'JS'
                    $money($input, 0)
                JS))
                            ->disabled()
                            ->reactive()
                            ->dehydrated()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                ResiumumCalculations::calculateOngkirTotal($get, $set);
                            })
                    ])


                    ->columns(3),


                Section::make('Data Resi')
                    ->schema([

                        Forms\Components\TextInput::make('noresi')
                            ->label('No Resi')
                            ->default(function () {
                                //----------------- v2
                                $user = request()->user();
                                if (!$user || !$user->network) {
                                    throw new \Exception('Anda tidak memiliki jaringan yang terkait. Silakan hubungi administrator.');
                                }
                                //------------
                                $networkCode = request()->user()->network->code;
                                $dateCode = date('dny');
                                $sequentialNumber = static::getNextSequentialNumber();
                                return $networkCode . $dateCode  . str_pad($sequentialNumber, 4, '0', STR_PAD_LEFT);
                            })
                            ->unique()
                            //->disabled()
                            ->dehydrated()
                            ->required(),
                        Forms\Components\TextInput::make('nofakt')
                            //   ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('date_input')
                            ->label('Tanggal Input')
                            ->default(now()->startOfDay())
                            ->required(),
                        Forms\Components\Select::make('jenis_pembayaran')
                            ->options([
                                'tunai' => 'Tunai',
                                'transfer' => 'Transfer',
                                'cod' => 'COD',
                                'lainnya' => 'Lainnya',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('kurir_pickup')

                            ->label('Nama Kurir Pickup'),
                        Forms\Components\Select::make('vendor_id')
                            ->relationship('Vendor', 'nama')
                            ->label('vendor Pickup'),
                        Forms\Components\TextInput::make('desk_brg')
                            ->label('Deskripsi Barang')
                            ->maxLength(255),

                        Forms\Components\TextArea::make('catatan')

                            ->columnspan(2)
                            ->maxLength(255),
                        Forms\Components\Hidden::make('last_officer_id')
                            ->default(function () {
                                return $userId = Auth::id();
                            }),
                    ])
                    ->columns(3),




                Split::make([
                    Section::make('Data Pengirim')
                        ->schema([
                            Forms\Components\TextInput::make('nama_pengirim')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('alamat_pengirim')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('telp_pengirim')
                                ->tel()
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('kode_pos_asal')
                                ->label('Kode Pos')
                                ->maxLength(255),
                        ])
                        ->columns(2),
                    Section::make('Data Penerima')
                        ->schema([
                            Forms\Components\TextInput::make('nama_penerima')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('alamat_penerima')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('telp_penerima')
                                ->tel()
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('kode_pos')
                                ->label('Kode Pos')
                                ->maxLength(255),
                        ])
                        ->columns(2),
                ])
                    ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('noresi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer.nama')
                    ->searchable()
                    ->sortable(),


                Tables\Columns\TextColumn::make('regency.name')
                    ->label('Asal')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('regencydest.name')
                    ->label('Tujuan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('district.name')
                    ->label('Kecamatan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jenis_pembayaran')
                    ->searchable(),

                /*
                Tables\Columns\TextColumn::make('weight')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('koli')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pcs_count')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_volume')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_weight_volume')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('charged_on'),
                Tables\Columns\TextColumn::make('jenis_kiriman')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('biaya_admin')
                    ->searchable(),
                Tables\Columns\TextColumn::make('insurance')
                    ->searchable(),
                Tables\Columns\TextColumn::make('insurance_value')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('biaya_packing')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('biaya_tambahan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tariff')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ongkir')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nofakt')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_input')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('satuan_barang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kurir_pickup')
                    ->searchable(),
                Tables\Columns\TextColumn::make('desk_brg')
                    ->searchable(),
                Tables\Columns\TextColumn::make('catatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_pengirim')
                    ->searchable(),
                Tables\Columns\TextColumn::make('alamat_pengirim')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telp_pengirim')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_penerima')
                    ->searchable(),
                Tables\Columns\TextColumn::make('alamat_penerima')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telp_penerima')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kode_pos')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kode_pos_asal')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lastOfficer.name')
                    ->label('Last Officer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ty_cust')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_location_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pickup_courier_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('delivery_courier_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('province.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_weight_volume')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('etd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('layanan_id')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    */
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                //-----unutk  print resi (langkah 1)
                Tables\Actions\Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->url(fn(Resiumum $record): string => route('resiumum.print', $record))
                    ->openUrlInNewTab(),
                //    Tables\Actions\Action::make('pdf')
                //     ->label('Download PDF')
                //     ->icon('heroicon-o-arrow-down-tray')
                //  ->url(fn(Resiumum $record): string => route('resiumum.pdf', $record)),
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
            'index' => Pages\ListResiumums::route('/'),
            'create' => Pages\CreateResiumum::route('/create'),
            'view' => Pages\ViewResiumum::route('/{record}'),
            'edit' => Pages\EditResiumum::route('/{record}/edit'),
        ];
    }
}
