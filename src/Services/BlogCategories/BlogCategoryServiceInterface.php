<?php

namespace atikullahnasar\blog\Services\BlogCategories;

use atikullahnasar\blog\Models\BlogCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface BlogCategoryServiceInterface
{
    /**
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 10): LengthAwarePaginator;

    /**
     * @return Collection
     */
    public function getWithBlogsCount(): Collection;

    /**
     * @param int $id
     * @return BlogCategory|null
     */
    public function findById(int $id): ?BlogCategory;

    /**
     * @param array $data
     * @return BlogCategory
     */
    public function create(array $data): BlogCategory;

    /**
     * @param int $id
     * @param array $data
     * @return BlogCategory
     */
    public function update(int $id, array $data);

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * @param int $id
     * @return BlogCategory
     */
    public function toggleStatus(int $id): BlogCategory;
}
