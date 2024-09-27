<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\SocialAccount
 *
 * @property int $id
 * @property int $user_id
 * @property string $provider
 * @property string $provider_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|SocialAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder|SocialAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialAccount whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialAccount whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialAccount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SocialAccount whereUserId($value)
 * @mixin \Eloquent
 */
class SocialAccount extends Model
{
    use HasFactory;
    
    protected $table = 'social_accounts';
    protected $fillable = [
        'user_id',
        'provider',
        'provider_id',
    ];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
