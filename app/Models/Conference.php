<?php

namespace App\Models;
use Filament\Forms;
use App\Enums\Region;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Tabs;
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
            Tabs::make()
                ->columnSpanFull()
                ->tabs([
                    Tabs\Tab::make('Conference Details')

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

                        Fieldset::make('Status')
                            ->columns(1)
                            ->schema([

                            Forms\Components\Select::make('status')
                                ->options([
                                        'draft' => 'Draft',
                                        'published' => 'Published',
                                        'archived' => 'Archived'
                                    ])
                                ->required(),
                            ]),

                            Toggle::make('is_published'),

                    ]),

                    Tabs\Tab::make('Location')
                    ->schema([
                        Forms\Components\Select::make('region')
                            ->live()
                            ->enum(Region::class)
                            ->options(Region::class),

                        Forms\Components\Select::make('venue_id')
                            ->searchable()
                            ->preload()
                            ->createOptionForm(Venue::getForm())
                            // unset($data['venue_id'])
                            // ->editOptionForm(Venue::getForm()) /// unset($data['venue_id']);
                            ->relationship('venue', 'name', modifyQueryUsing: function (Builder $query, Forms\Get $get){
                            return $query->where('region', $get('region'));
                        }),

                    ]),

                    //last tab
                    Tabs\Tab::make('Speaker')
                    ->schema([
                        Forms\Components\CheckboxList::make('speaker')
                        ->relationship('speakers', 'name')
                        ->options(Speaker::all()->pluck('name','id'))
                        ->required(),
                    ])
                ]),
            // Section::make('Conference Details')
            //     ->collapsible()
            //     ->description('These are the details of the conference.')
            //     ->icon('heroicon-o-information-circle')
            //     ->columns(2)



            // Section::make('Location')
            //     ->columns(2)

        Actions::make([
            Action::make('Save')
            ->label('Fill with factory data')
            ->icon('heroicon-o-star')
            ->action(function ($livewire){
                $data = Conference::factory()->make()->toArray();
                // unset($data['venue_id']);
                $livewire->form->fill($data);
            }),
        ])


        ];
    }
}
