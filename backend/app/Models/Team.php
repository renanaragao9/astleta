<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Team extends BaseModel
{
    protected $fillable = [
        'name',
        'uuid',
        'nickname',
        'stadium_name',
        'primary_color',
        'secondary_color',
        'shield_path',
        'website',
        'founded_date',
        'description',
        'welcome_email',
        'max_members',
        'is_public',
        'user_id',
        'sport_id',
        'team_type_id',
    ];

    protected $casts = [
        'founded_date' => 'date',
        'max_members' => 'integer',
        'is_public' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->user();
    }

    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    public function teamType(): BelongsTo
    {
        return $this->belongsTo(TeamType::class);
    }

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function contacts(): MorphMany
    {
        return $this->morphMany(Contact::class, 'contactable');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function teamPlayers(): HasMany
    {
        return $this->hasMany(TeamPlayer::class);
    }

    public function tournamentTeams(): HasMany
    {
        return $this->hasMany(TournamentTeam::class);
    }
}
