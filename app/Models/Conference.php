<?php

namespace App\Models;
use Filament\Forms;
use App\Enums\Region;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Conference extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'date',
        'region' => Region::class,
        'venue_id' => 'integer',
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(Speaker::class);
    }

    public function talks(): BelongsToMany
    {
        return $this->belongsToMany(Talk::class);
    }

    public static function getForm(): array
    {
        return [
            Section::make('Conference Details')
                ->columns(2)
                ->schema([

                    Forms\Components\TextInput::make('name')
                        ->columnSpanFull()
                        ->label('Conference Name')
                        ->hintIcon('heroicon-o-rectangle-stack')
                        ->default('New Conference')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\MarkdownEditor::make('description')
                        ->columnSpanFull()
                        ->required(),
                        
                    Forms\Components\DateTimePicker::make('start_date')
                        ->native(false)
                        ->required(),
                    Forms\Components\DateTimePicker::make('end_date')
                        ->required(),
                ]),

            Toggle::make('is_published'),

            Forms\Components\Select::make('status')
                ->options(
                    [
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'archived' => 'Archived'
                    ]
                    )
                ->required(),
                Forms\Components\Select::make('region')
                ->live()
                ->enum(Region::class)
                ->options(Region::class),

            Forms\Components\Select::make('venue_id')
                ->searchable()
                ->preload()
                ->createOptionForm(Venue::getForm())
                // ->editOptionForm(Venue::getForm())
                ->relationship('venue', 'name', modifyQueryUsing: function (Builder $query, Forms\Get $get){
                return $query->where('region', $get('region'));
            }),

            Forms\Components\CheckboxList::make('speaker')
                ->relationship('speakers', 'name')
                ->options(
                    Speaker::all()->pluck('name','id')
                )
            ->required(),

                ];
    }
}
