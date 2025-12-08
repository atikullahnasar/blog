<?php

namespace atikullahnasar\blog\Repositories\Blogs;

use atikullahnasar\blog\Repositories\BaseRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface BlogRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPublished(int $perPage = 10): LengthAwarePaginator;

    /**
     * @param int $categoryId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByCategory(int $categoryId, int $perPage = 10): LengthAwarePaginator;

    /**
     * @param int $limit
     * @return Collection
     */
    public function getFeatured(int $limit = 5): Collection;

    /**
     * @param string $query
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function search(string $query, int $perPage = 10): LengthAwarePaginator;
}
