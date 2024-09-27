<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AssignedChat
 *
 * @property int $id
 * @property int|null $customer_id
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AssignedChat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignedChat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignedChat query()
 * @method static \Illuminate\Database\Eloquent\Builder|AssignedChat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignedChat whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignedChat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignedChat whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssignedChat whereUserId($value)
 * @mixin \Eloquent
 * @property-read \App\Models\User|null $agent
 */
class AssignedChat extends Model
{
    public $table = 'assigned_chats';

    public $fillable = [
        'customer_id',
        'user_id',
    ];

    protected $casts = [
        'customer_id' => 'integer',
        'user_id'     => 'integer',
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'user_id')->without('media','roles');
    }
}
