<?php

namespace atikullahnasar\blog\Services\Blogs;

use atikullahnasar\blog\Models\Blog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface BlogServiceInterface
{
    public function publisheddata();
    /**
     * @param int $perPage
     * @param array $relations
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 10, array $relations = []): LengthAwarePaginator;

    /**
     * @param array $relations
     * @return Collection
     */
    public function getAllWithRelations(array $relations = []): Collection;

    /**
     * @param int $id
     * @param array $relations
     * @return Blog|null
     */
    public function findById(int $id, array $relations = []): ?Blog;

    /**
     * @param array $data
     * @return Blog
     */
    public function create(array $data): Blog;

    /**
     * @param int $id
     * @param array $data
     * @return Blog
     */
    public function update(int $id, array $data);

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPublishedBlogs(int $perPage = 10): LengthAwarePaginator;

    /**
     * @param int $categoryId
     * @return LengthAwarePaginator
     */
    public function getByCategory(int $categoryId): LengthAwarePaginator;

    /**
     * @param string $query
     * @return LengthAwarePaginator
     */
    public function search(string $query): LengthAwarePaginator;

    /**
     * @param int $id
     * @return Blog
     */
    public function toggleStatus(int $id): Blog;
}
