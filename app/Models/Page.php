<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $url
 * @property string|null $title
 * @property string|null $h1
 * @property string|null $body_text
 * @property int $word_count
 * @property string|null $language
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read Collection<int, InternalLink> $internalLinks
 *
 * @method static Builder|Page whereUrl(string $value)
 * @method static Builder|Page whereLanguage(string $value)
 * @method static updateOrCreate(array $array, array $array1)
 */
class Page extends Model
{
    protected $fillable = [
        'url',
        'title',
        'h1',
        'body_text',
        'word_count',
        'language',
    ];

    /**
     * @return HasMany
     */
    public function internalLinks(): HasMany
    {
        return $this->hasMany(InternalLink::class, 'source_page_id');
    }
}
