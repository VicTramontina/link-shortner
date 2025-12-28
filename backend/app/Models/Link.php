<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Link Model
 *
 * Represents a shortened URL link.
 *
 * @property int $id
 * @property int $user_id
 * @property string $original_url
 * @property string $slug
 * @property string|null $title
 * @property int $access_count
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AccessLog> $accessLogs
 */
class Link extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'original_url',
        'slug',
        'title',
        'access_count',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'access_count' => 'integer',
        ];
    }

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'access_count' => 0,
    ];

    /**
     * Get the user that owns the link.
     *
     * @return BelongsTo<User, Link>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the access logs for the link.
     *
     * @return HasMany<AccessLog>
     */
    public function accessLogs(): HasMany
    {
        return $this->hasMany(AccessLog::class);
    }

    /**
     * Increment the access count.
     *
     * @return void
     */
    public function incrementAccessCount(): void
    {
        $this->increment('access_count');
    }

    /**
     * Get the short URL for the link.
     *
     * @return string
     */
    public function getShortUrlAttribute(): string
    {
        return url($this->slug);
    }
}
