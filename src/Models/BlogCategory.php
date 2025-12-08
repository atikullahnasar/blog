<?php

namespace atikullahnasar\blog\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'beft_blog_categories';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
        'order',
        'meta_title',
        'meta_description'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'boolean',
        'order' => 'integer'
    ];

    /**
     * Get the blogs for the category.
     */
    public function blogs(): HasMany
    {
        return $this->hasMany(Blog::class, 'category_id');
    }

    /**
     * Toggle the status of the category.
     */
    public function toggleStatus(): void
    {
        $this->status = !$this->status;
        $this->save();
    }
}

