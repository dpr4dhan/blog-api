<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\InteractsWithUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\PostComments.
 *
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property string $title
 * @property string $content
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $author
 * @method static Builder|Post findByUuid(string $uuid)
 * @method static Builder|Post findOrFailByUuid(string $uuid)
 * @method static Builder|Post newModelQuery()
 * @method static Builder|Post newQuery()
 * @method static Builder|Post query()
 * @method static Builder|Post whereContent($value)
 * @method static Builder|Post whereCreatedAt($value)
 * @method static Builder|Post whereId($value)
 * @method static Builder|Post whereTitle($value)
 * @method static Builder|Post whereUpdatedAt($value)
 * @method static Builder|Post whereUserId($value)
 * @method static Builder|Post whereUuid($value)
 * @mixin \Eloquent
 */
final class PostComments extends Model
{
    use InteractsWithUuid;

    protected static $unguarded = true;

    public function commentator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
