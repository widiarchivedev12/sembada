<?php

namespace App\Models;

use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Event extends Model
{
    /** @use HasFactory<EventFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'created_by',
        'title',
        'slug',
        'description',
        'venue',
        'starts_at',
        'ends_at',
        'registration_closes_at',
        'quota',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'registration_closes_at' => 'datetime',
            'quota' => 'integer',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function tickets(): HasManyThrough
    {
        return $this->hasManyThrough(Ticket::class, Registration::class);
    }

    public function checkinLogs(): HasMany
    {
        return $this->hasMany(CheckinLog::class);
    }

    public function confirmedRegistrationsCount(): int
    {
        return $this->registrations()->where('status', 'confirmed')->count();
    }

    public function isQuotaFull(): bool
    {
        return $this->confirmedRegistrationsCount() >= $this->quota;
    }

    public function isRegistrationOpen(): bool
    {
        if ($this->status !== 'open') {
            return false;
        }

        if ($this->registration_closes_at && now()->isAfter($this->registration_closes_at)) {
            return false;
        }

        return ! $this->isQuotaFull();
    }
}
