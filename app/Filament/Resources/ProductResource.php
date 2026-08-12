<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Product Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Select::make('category_id')
                            ->label('Category')
                            ->options(Category::query()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('sku')
                            ->label('SKU')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true)
                            ->helperText('Must be unique across all products.'),

                        Textarea::make('description')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make('Pricing & Stock')
                    ->columns(3)
                    ->schema([
                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->minValue(0),

                        TextInput::make('cost_price')
                            ->label('Cost Price')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->minValue(0),

                        TextInput::make('stock_quantity')
                            ->label('Stock Quantity')
                            ->required()
                            ->numeric()
                            ->integer()
                            ->minValue(0)
                            ->default(0),
                    ]),

                Section::make('Media & Status')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('image_path')
                            ->label('Product Image')
                            ->image()
                            ->directory('products')
                            ->visibility('public')
                            ->imageEditor()
                            ->columnSpanFull(),

                        Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                            ])
                            ->required()
                            ->default('active')
                            ->native(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Image')
                    ->disk('public')
                    ->circular(),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->badge(),

                TextColumn::make('price')
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'danger',
                        $state < 10 => 'warning',
                        default => 'success',
                    }),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name'),

                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
