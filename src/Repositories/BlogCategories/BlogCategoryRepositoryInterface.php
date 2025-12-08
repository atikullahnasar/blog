<?php

namespace atikullahnasar\blog\Repositories\BlogCategories;

use atikullahnasar\blog\Models\BlogCategory;
use atikullahnasar\blog\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface BlogCategoryRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @return Collection
     */
    public function getActive(): Collection;

    /**
     * @return Collection
     */
    public function getWithBlogsCount(): Collection;

    /**
     * @param string $slug
     * @return BlogCategory|
     */
    public function findBySlug(string $slug): BlogCategory;

    /**
     * @param int $id
     * @param int $order
     * @return BlogCategory
     */
    public function updateOrder(int $id, int $order): BlogCategory;
}
