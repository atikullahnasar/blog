<?php

namespace atikullahnasar\blog\Repositories\Blogs;

use atikullahnasar\blog\Models\Blog;
use atikullahnasar\blog\Repositories\BaseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BlogRepository extends BaseRepository implements BlogRepositoryInterface
{
    /**
     * @param Blog $model
     */
    public function __construct(Blog $model)
    {
        parent::__construct($model);
    }

    /**
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPublished(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->with(['category', 'author'])
            ->published()
            ->orderBy('published_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * @param int $categoryId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByCategory(int $categoryId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->with(['category', 'author'])
            ->where('category_id', $categoryId)
            ->published()
            ->orderBy('published_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * @param int $limit
     * @return Collection
     */
    public function getFeatured(int $limit = 5): Collection
    {
        return $this->model->with(['category', 'author'])
            ->published()
            ->whereNotNull('featured_image')
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * @param string $query
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function search(string $query, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->with(['category', 'author'])
            ->where('title', 'LIKE', "%{$query}%")
            ->orWhere('content', 'LIKE', "%{$query}%")
            ->published()
            ->orderBy('published_at', 'desc')
            ->paginate($perPage);
    }
}
