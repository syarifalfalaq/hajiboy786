<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SektorUsahaResource\Pages;
use App\Filament\Resources\SektorUsahaResource\RelationManagers;
use App\Models\SektorUsaha;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SektorUsahaResource extends Resource
{
    protected static ?string $model = SektorUsaha::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Data Master';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_sektor')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('kode_sektor')
                    ->required()
                    ->maxLength(20),
                Forms\Components\Textarea::make('deskripsi')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_sektor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kode_sektor')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
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
            'index' => Pages\ListSektorUsahas::route('/'),
            'create' => Pages\CreateSektorUsaha::route('/create'),
            'view' => Pages\ViewSektorUsaha::route('/{record}'),
            'edit' => Pages\EditSektorUsaha::route('/{record}/edit'),
        ];
    }
}
