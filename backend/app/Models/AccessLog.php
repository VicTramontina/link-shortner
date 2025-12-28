<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * AccessLog Model
 *
 * Represents an access log entry for a shortened link.
 *
 * @property int $id
 * @property int $link_id
 * @property string $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon $accessed_at
 * @property-read Link $link
 */
class AccessLog extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'link_id',
        'ip_address',
        'user_agent',
        'accessed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'accessed_at' => 'datetime',
        ];
    }

    /**
     * Get the link that owns the access log.
     *
     * @return BelongsTo<Link, AccessLog>
     */
    public function link(): BelongsTo
    {
        return $this->belongsTo(Link::class);
    }
}
