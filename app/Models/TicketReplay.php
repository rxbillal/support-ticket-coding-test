<?php

namespace App\Models;

use Embed\Providers\OEmbed\Tiktok;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * App\Models\TicketReplay
 *
 * @property int $id
 * @property int $user_id
 * @property int $ticket_id
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $ticket
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|TicketReplay newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketReplay newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketReplay query()
 * @method static \Illuminate\Database\Eloquent\Builder|TicketReplay whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketReplay whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketReplay whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketReplay whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketReplay whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TicketReplay whereUserId($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\MediaLibrary\Models\Media[] $media
 * @property-read int|null $media_count
 */
class TicketReplay extends Model implements HasMedia
{
    use InteractsWithMedia;

    public const COLLECTION_TICKET = 'tickets_reply';

    public $table = 'ticket_replay';

    public $fillable = [
        'ticket_id',
        'description',
        'user_id',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'ticket_id'   => 'nullable',
        'description' => 'required',
    ];

    /**
     * @var array
     */
    public $casts = [
        'id'          => 'integer',
        'user_id'     => 'integer',
        'ticket_id'   => 'integer',
        'description' => 'string',
    ];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }
}
