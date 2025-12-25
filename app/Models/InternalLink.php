<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property int $source_page_id
 * @property string $target_url
 * @property string|null $anchor_text
 * @property bool $nofollow
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read Page $sourcePage
 *
 * @method static Builder|InternalLink whereSourcePageId(int $value)
 * @method static Builder|InternalLink whereTargetUrl(string $value)
 */
class InternalLink extends Model
{
    protected $table = 'internal_links';

    protected $fillable = [
        'source_page_id',
        'target_url',
        'anchor_text',
        'nofollow',
    ];

    protected $casts = [
        'nofollow' => 'bool',
    ];

    /**
     * @return BelongsTo
     */
    public function sourcePage(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'source_page_id');
    }
}
