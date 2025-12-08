<?php

namespace atikullahnasar\blog\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Blog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'beft_blogs';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'category_id',
        'author_id',
        'featured_image',
        'published_at',
        'status',
        'meta_title',
        'show_home',
        'meta_description'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published_at' => 'datetime',
        'category_id' => 'integer',
        'author_id' => 'integer'
    ];

    /**
     * The attributes that should be appended to the model's array and JSON form.
     *
     * @var array<int, string>
     */
    protected $appends = ['thumbnail_image'];

    /**
     * Get the category that owns the blog.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    /**
     * Get the author that owns the blog.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Scope a query to only include published blogs.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope a query to only include drafts.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Accessor for the thumbnail image path.
     *
     * @return string|null
     */
    public function getThumbnailImageAttribute(): ?string
    {
        if (!$this->featured_image) {
            return null;
        }

        $directory = dirname($this->featured_image);
        $filename = basename($this->featured_image);
        $thumbnail = "{$directory}/thumb_{$filename}";

        return asset("storage/{$thumbnail}");
    }

    /**
     * Toggle the status of the blog.
     */
    public function toggleStatus(): void
    {
        $this->status = $this->status === 'published' ? 'draft' : 'published';
        $this->save();
    }
}
