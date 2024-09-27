<?php

namespace App\Models;

use App\Traits\ImageTrait;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Conversation
 *
 * @property int $id
 * @property int|null $from_id
 * @property int|null $to_id
 * @property string $message
 * @property int $status 0 for unread,1 for seen
 * @property int $message_type 0- text message, 1- image, 2- pdf, 3- doc, 4- voice
 * @property string|null $file_name
 * @property string|null $send_by
 * @property string $created_at
 * @property Carbon|null $updated_at
 * @property-read string $photo_url
 * @property-read User|null $receiver
 * @property-read User|null $sender
 * @property-read User|null $sendByUser
 * @method static Builder|Conversation newModelQuery()
 * @method static Builder|Conversation newQuery()
 * @method static Builder|Conversation query()
 * @method static Builder|Conversation whereCreatedAt($value)
 * @method static Builder|Conversation whereFileName($value)
 * @method static Builder|Conversation whereFromId($value)
 * @method static Builder|Conversation whereId($value)
 * @method static Builder|Conversation whereMessage($value)
 * @method static Builder|Conversation whereMessageType($value)
 * @method static Builder|Conversation whereStatus($value)
 * @method static Builder|Conversation whereToId($value)
 * @method static Builder|Conversation whereUpdatedAt($value)
 * @mixin Model
 * @property Carbon|null $deleted_at
 * @property-read User $user
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|Conversation onlyTrashed()
 * @method static bool|null restore()
 * @method static Builder|Conversation whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Conversation withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Conversation withoutTrashed()
 * @property-read string $time_from_now_in_min
 * @property string $to_type 1 => Message, 2 => Group Message
 * @property-write mixed $raw
 * @method static Builder|Conversation whereToType($value)
 * @method static Builder|Conversation message()
 * @property-read Collection|MessageAction $messageAction
 * @property-read int|null $read_by_all_count
 * @method static Builder|Conversation whereReplyTo($value)
 * @property-read Conversation|null $replyMessage
 */
class Conversation extends Model
{
    use ImageTrait;

    /**
     * @var string
     */
    public $table = 'conversations';

    public $fillable = [
        'from_id',
        'to_id',
        'message',
        'message_type',
        'status',
        'file_name',
        'url_details',
        'send_by',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'from_id'      => 'integer',
        'to_id'        => 'string',
        'message'      => 'string',
        'message_type' => 'integer',
        'file_name'    => 'string',
        'url_details'  => 'json',
        'status'       => 'integer',
    ];

    public static $rules = [
        'to_id'   => 'nullable|string',
        'from_id' => 'nullable|string',
        'message' => 'required|string',
    ];

    // time from now in minuts
    protected $appends = ['time_from_now_in_min'];

    const LIMIT = 5000;
    const PATH = 'conversation';
    const MEDIA_IMAGE = 1;
    const MEDIA_PDF = 2;
    const MEDIA_DOC = 3;
    const MEDIA_VOICE = 4;
    const MEDIA_VIDEO = 5;
    const YOUTUBE_URL = 6;
    const MEDIA_TXT = 7;
    const MEDIA_XLS = 8;
    const MESSAGE_TYPE_BADGES = 9;

    const MEDIA_MESSAGE_TYPES = [
        self::MEDIA_IMAGE, self::MEDIA_PDF, self::MEDIA_DOC, self::MEDIA_VOICE, self::MEDIA_VIDEO, self::YOUTUBE_URL,
        self::MEDIA_TXT, self::MEDIA_XLS,
    ];

    public function getMessageAttribute($value)
    {
        if (! empty($this->file_name)) {
            return $this->imageUrl(self::PATH.DIRECTORY_SEPARATOR.$value);
        }

        return $value;
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getPhotoUrlAttribute($value)
    {
        if (! empty($value)) {
            return $this->imageUrl(User::$PATH.DIRECTORY_SEPARATOR.$value);
        }

        return asset('assets/images/avatar.png');
    }

    /**
     * @return string
     */
    public function getTimeFromNowInMinAttribute()
    {
        return Carbon::now()->diffInMinutes($this->created_at);
    }

    /**
     * @return BelongsTo
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'from_id');
    }

    /**
     * @return BelongsTo
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'to_id');
    }

    /**
     * @return BelongsTo
     */
    public function sendByUser()
    {
        return $this->belongsTo(User::class, 'send_by');
    }


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
    public function archiveConversation()
    {
        return $this->belongsTo(ArchivedUser::class, 'to_id', 'owner_id')->where('archived_by', '=',
            getLoggedInUserId());
    }

    /**
     * @return BelongsTo
     */
    public function archiveUsers()
    {
        return $this->belongsTo(ArchivedUser::class, DB::raw('user_id'), 'owner_id')->where('archived_by', '=',
            getLoggedInUserId());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function messageAction()
    {
        return $this->hasOne(MessageAction::class, 'conversation_id')->where('deleted_by', getLoggedInUserId());
    }
}
