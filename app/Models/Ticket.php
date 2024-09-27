<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * App\Models\Ticket
 *
 * @property int $id
 * @property string $title
 * @property string $ticket_id
 * @property string $email
 * @property int $status
 * @property string|null $description
 * @property int|null $created_by
 * @property int $category_id
 * @property bool $is_public
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $assignTo
 * @property-read int|null $assign_to_count
 * @property-read \App\Models\Category $category
 * @property-read bool $attachments
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\MediaLibrary\Models\Media[] $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TicketReplay[] $replay
 * @property-read int|null $replay_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket query()
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ticket whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Ticket extends Model implements HasMedia
{
    use  InteractsWithMedia;

    public $table = 'tickets';

    public const COLLECTION_TICKET = 'tickets';

    const STATUS_OPEN = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_CLOSED = 3;
    const STATUS_ACTIVE = 0;

    const STATUS = [
        0 => 'Active',
        1 => 'Open',
        2 => 'In Progress',
        3 => 'Closed',
    ];

    const STATUS_COLOR = [
        1 => 'success',
        2 => 'warning',
        3 => 'danger',
    ];

    const TICKET = [
        1 => 'Public',
        0 => 'Private',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'title'       => 'required',
        'email'       => 'required|email|regex:/^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,6}$/',
        'category_id' => 'required',
        'description' => 'required',
        'created_by'  => 'nullable',
    ];

    public static $messages = [
        'email.regex' => 'Please enter valid email.',
    ];

    public $fillable = [
        'title',
        'email',
        'ticket_id',
        'category_id',
        'is_public',
        'description',
        'status',
        'created_by',
        'close_at',
    ];

    /**
     * @var array
     */
    public $casts = [
        'id'          => 'integer',
        'category_id' => 'integer',
        'is_public'   => 'boolean',
        'ticket_id'   => 'string',
        'status'      => 'integer',
        'title'       => 'string',
        'description' => 'string',
        'email'       => 'string',
    ];

    /**
     * @var string[]
     */
    protected $appends = ['attachments'];

    /**
     * @return bool
     */
    public function getAttachmentsAttribute()
    {
        $media = $this->getMedia(self::COLLECTION_TICKET)->first();
        if (! empty($media)) {
            return $media->getFullUrl();
        }

        return false;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replay()
    {
        return $this->hasMany(TicketReplay::class, 'ticket_id', 'id')->orderByDesc('updated_at');
    }

    /**
     * @return BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsToMany
     */
    public function assignTo()
    {
        return $this->belongsToMany(User::class, 'ticket_user', 'ticket_id', 'user_id');
    }
}
