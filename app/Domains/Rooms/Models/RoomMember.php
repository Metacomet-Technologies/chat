<?php

declare(strict_types=1);

namespace App\Domains\Rooms\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoomMember query()
 * @mixin \Eloquent
 */
class RoomMember extends Pivot
{
    protected $table = 'room_members';

    protected $fillable = [
        'room_id',
        'user_id',
        'role',
        'joined_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
    ];
}