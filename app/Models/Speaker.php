<?php

namespace App\Models;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Speaker extends Model
{
    use HasFactory;

    const QUALIFICATIONS = [
        'business-leader' => 'Business Leader',
        'charisma' => 'Charisma Speaker',
        'first-time' => 'First Time Speaker',
        'hometown-hero' => 'Hometown  Hero',
        'laracasts-contributor' => 'Laracasts Contributor',
        'twitter-influencer' => 'Large Twitter Following',
        'youtube-influencer' => 'Large YouTube Following',
        'open-source' => 'Open Source Contributor',
        'unique-perspective' => 'Unique Perspective'
    ];

    protected $casts = [
        'id' => 'integer','qualifications' => 'array'
    ];

    public function conferences(): BelongsToMany
    {
        return $this->belongsToMany(Conference::class);
    }

    public function talks(): HasMany
    {
        return $this->hasMany(Talk::class);
    }

    public static function  getForm(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),

            FileUpload::make('avatar')
                ->avatar()
                ->imageEditor()
                ->directory('avatars')
                ->maxSize(1024 * 1024 * 10),

            Forms\Components\TextInput::make('email')
                ->email()
                ->maxLength(255),
            Forms\Components\Textarea::make('bio')
                ->columnSpanFull(),
            Forms\Components\TextInput::make('twitter_handle')
                ->maxLength(255),
            Forms\Components\CheckboxList::make('qualifications')
                ->columnSpanFull()
                ->searchable()
                ->bulkToggleable()
                ->options(self::QUALIFICATIONS)
                ->descriptions([
                    'business-leader' => 'Business Leader',
                    'charisma' => 'Charisma Speaker',
                    'first-time' => 'First Time Speaker',
                ])
                ->columns(3)
        ];
    }
}
