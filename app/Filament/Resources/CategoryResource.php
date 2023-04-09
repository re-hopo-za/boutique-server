<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $label = 'Categories';
    protected static ?string $navigationIcon = 'heroicon-o-color-swatch';
    protected static ?string $navigationGroup = 'setting';
    protected static ?int $navigationSort = 3;


    public static  function can(string $action, ?Model $record = null): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->getAllPermissions()->where('name' ,'Category')->count();
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()->columns(12) ->schema([
                    Grid::make()->schema([
                        Card::make()->schema([
                            Forms\Components\TextInput::make('title')
                                ->label('ُTitle')
                                ->required(),
                            Forms\Components\TextInput::make('slug')
                                ->label('Slug')
                                ->required() ,
                            TinyEditor::make('description')
                                ->label('Description') ,
                        ]),
                        Card::make()->schema([
                            TableRepeater::make('meta')
                                ->label('Meta')
                                ->relationship('meta')
                                ->schema([
                                    Forms\Components\Select::make('key')
                                        ->label('Key')
                                        ->required()
                                        ->options([
                                            'keywords'    => 'Meta Keywords',
                                            'description' => ' Meta Description',
                                        ]),
                                    Forms\Components\TextInput::make('value')
                                        ->label('Value')
                                        ->required(),
                                ])
                                ->collapsible()
                                ->defaultItems(0),
                        ]),
                    ])->columnSpan(9 ),
                    Grid::make()->schema([
                        Card::make()->schema([
                            Forms\Components\Select::make('model')
                                ->label('Post Type')
                                ->options(
                                    [
                                        'blog'    => 'Blog',
                                        'product' => 'Product',
                                        'work'    => 'Works',
                                        'faq'     => 'Faqs',
                                        'portfolio' => 'Portfolio',
                                    ]
                                )
                                ->required(),
                            Forms\Components\Select::make('parent_id')
                                ->label('Parent ID')
                                ->searchable()
                                ->getSearchResultsUsing(fn (string $search) =>  ( new Category)
                                    ->searchCategory( $search ) )
                                ->default('0' ),
                            Forms\Components\FileUpload::make('cover')
                                ->imagePreviewHeight('500')
                                ->panelAspectRatio('9:1')
                                ->label('Cover ')
                                ->hint('Select Icon'),
                        ]),
                    ])->columnSpan(3 ),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover')
                    ->label('Cover') ,

                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->sortable(),

                Tables\Columns\TextColumn::make('model')
                    ->enum([
                        'blog'    => 'Blog',
                        'service' => 'Service',
                        'work'    => 'Works',
                        'faq'     => 'Faqs',
                        'portfolio' => 'Portfolio',
                    ]),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
