<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $phone
 * @property string|null $last_seen
 * @property int|null $is_online
 * @property int|null $is_active
 * @property string|null $about
 * @property int|null $gender
 * @property mixed $photo_url
 * @property string|null $activation_code
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property string|null $default_language
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int|null $is_system
 * @property string $region_code
 * @property string|null $region_code_flag
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Ticket[] $activeTickets
 * @property-read int|null $active_tickets_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Ticket[] $closeTickets
 * @property-read int|null $close_tickets_count
 * @property-read string $role_id
 * @property-read string $role_name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Ticket[] $inProgressTickets
 * @property-read int|null $in_progress_tickets_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\MediaLibrary\Models\Media[] $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Ticket[] $ticket
 * @property-read int|null $ticket_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Ticket[] $tickets
 * @property-read int|null $tickets_count
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAbout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereActivationCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDefaultLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsOnline($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsSystem($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastSeen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhotoUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRegionCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRegionCodeFlag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $email_update
 * @property-read \App\Models\AssignedChat|null $assign
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\BlockedUser[] $blockedBy
 * @property-read int|null $blocked_by_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailUpdate($value)
 */
class User extends Authenticatable implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use Notifiable, HasRoles;

    public static $PATH = 'users';
    const Faq = 'faq';
    const HEIGHT = 250;
    const WIDTH = 250;

    const MALE = 1;
    const FEMALE = 2;
    const IS_SYSTEM = 1;

    const BLOCK_UNBLOCK_EVENT = 1;
    const NEW_PRIVATE_CONVERSATION = 2;
    const PRIVATE_MESSAGE_READ = 4;
    const MESSAGE_DELETED = 5;
    const MESSAGE_NOTIFICATION = 6;
    const CHAT_REQUEST = 7;
    const CHAT_REQUEST_ACCEPTED = 8;
    const NEW_CUSTOMER_ARRIVED = 9;

    const PROFILE_UPDATES = 1;
    const STATUS_UPDATE = 2;
    const STATUS_CLEAR = 3;

    const PUBLIC_USER_MESSAGE_RECEIVED = 1;
    const PUBLIC_CHAT_ASSIGNED = 2;
    const FRONT_USER_ONLINE = 3;

    const PROFILE = 'profile-pictures';

    const ROLE_ALL = 0;
    const ADMIN = 1;
    const AGENT = 2;

    const LANGUAGES = [
        'en' => 'English',
        'es' => 'Spanish',
        'fr' => 'French',
        'de' => 'German',
        'ru' => 'Russian',
        'pt' => 'Portuguese',
        'ar' => 'Arabic',
        'zh' => 'Chinese',
        'tr' => 'Turkish',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'email_verified_at',
        'last_seen',
        'is_online',
        'about',
        'gender',
        'photo_url',
        'activation_code',
        'is_active',
        'is_system',
        'region_code',
        'default_language',
        'region_code_flag',
        'email_update',
    ];

//    /**
//     * @var array
//     */
//    protected $appends = ['role_name'];
//
//    protected $with = ['media', 'roles'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'                => 'integer',
        'name'              => 'string',
        'email_verified_at' => 'datetime',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'gender'            => 'integer',
        'archive'           => 'integer',
        'email_update'      => 'boolean',
    ];

    /**
     * @var array
     */
    public static $messages = [
        'email.regex' => 'Please enter valid email.',
    ];

    /**
     * @return string
     */
    public function getNameAttribute($value)
    {
        return ucfirst($value);
    }

    /**
     * @return string
     */
    public function getRoleNameAttribute()
    {
        $userRoles = $this->roles->first();

        return (! empty($userRoles)) ? $userRoles->name : 'N/A';
    }

    /**
     * @return string
     */
    public function getRoleIdAttribute()
    {
        $userRoles = $this->roles->first();

        return (! empty($userRoles)) ? $userRoles->id : '';
    }

    /**
     * @return mixed
     */
    public function getPhotoUrlAttribute()
    {
        $media = $this->getMedia(self::PROFILE)->first();
        if (! empty($media)) {
            return $media->getFullUrl();
        }

        if ($this->gender == self::MALE) {
            return asset('assets/icons/male.png');
        }
        if ($this->gender == self::FEMALE) {
            return asset('assets/icons/female.png');
        }

        return getUserImageInitial($this->id, $this->name);
    }

    /**
     * @return array
     */
    public function webObj()
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'email'     => $this->email,
            'phone'     => $this->phone,
            'last_seen' => $this->last_seen,
            'about'     => $this->about,
            'photo_url' => $this->photo_url,
            'gender'    => $this->gender,
        ];
    }

    /**
     * @return array
     */
    public function apiObj()
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'email'             => $this->email,
            'email_verified_at' => (! empty($this->email_verified_at)) ? $this->email_verified_at->toDateTimeString() : '',
            'phone'             => $this->phone,
            'last_seen'         => $this->last_seen,
            'is_online'         => $this->is_online,
            'is_active'         => $this->is_active,
            'gender'            => $this->gender,
            'about'             => $this->about,
            'photo_url'         => $this->photo_url,
            'activation_code'   => $this->activation_code,
            'created_at'        => (! empty($this->created_at)) ? $this->created_at->toDateTimeString() : '',
            'updated_at'        => (! empty($this->updated_at)) ? $this->updated_at->toDateTimeString() : '',
            'is_system'         => $this->is_system,
            'role_name'         => (! $this->roles->isEmpty()) ? $this->roles->first()->name : null,
            'role_id'           => (! $this->roles->isEmpty()) ? $this->roles->first()->id : null,
            'archive'           => (! empty($this->deleted_at)) ? 1 : 0,
        ];
    }

    /**
     * @return BelongsToMany
     */
    public function ticket()
    {
        return $this->belongsToMany(Ticket::class, 'ticket_user', 'user_id', 'ticket_id');
    }

    /**
     * @return hasMany
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'created_by');
    }

    /**
     * @return HasMany
     */
    public function activeTickets()
    {
        return $this->hasMany(Ticket::class, 'created_by')
            ->where('status', Ticket::STATUS_OPEN);
    }

    /**
     * @return HasMany
     */
    public function inProgressTickets()
    {
        return $this->hasMany(Ticket::class, 'created_by')
            ->where('status', Ticket::STATUS_IN_PROGRESS);
    }

    /**
     * @return HasMany
     */
    public function closeTickets()
    {
        return $this->hasMany(Ticket::class, 'created_by')
            ->where('status', Ticket::STATUS_CLOSED);
    }

    /**
     * @return HasMany
     */
    public function blockedBy()
    {
        return $this->hasMany(BlockedUser::class, 'blocked_by');
    }

    /**
     * @return HasOne
     */
    public function assign()
    {
        return $this->hasOne(AssignedChat::class, 'customer_id', 'id');
    }

    public function socialAccount() : HasMany
    {
        return $this->hasMany(SocialAccount::class, 'user_id');
    }
}
