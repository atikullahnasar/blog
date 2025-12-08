<?php

namespace atikullahnasar\blog\Repositories\BlogCategories;

use atikullahnasar\blog\Models\BlogCategory;
use atikullahnasar\blog\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class BlogCategoryRepository extends BaseRepository implements BlogCategoryRepositoryInterface
{
    /**
     * @param BlogCategory $model
     */
    public function __construct(BlogCategory $model)
    {
        parent::__construct($model);
    }

    /**
     * @return Collection
     */
    public function getActive(): Collection
    {
        return $this->model->where('status', true)
            ->orderBy('order')
            ->get();
    }

    /**
     * @return Collection
     */
    public function getWithBlogsCount(): Collection
    {
        return $this->model->withCount(['blogs' => function($query) {
                $query->published();
            }])
            ->orderBy('order')
            ->get();
    }

    /**
     * @param string $slug
     * @return BlogCategory
     */
    public function findBySlug(string $slug): BlogCategory
    {
        return $this->model->where('slug', $slug)->firstOrFail();
    }

    /**
     * @param int $id
     * @param int $order
     * @return BlogCategory
     */
    public function updateOrder(int $id, int $order): BlogCategory
    {
        $category = $this->find($id);
        $category->order = $order;
        $category->save();
        return $category;
    }
}
