<?php

namespace atikullahnasar\blog\Services\BlogCategories;

use atikullahnasar\blog\Models\BlogCategory;
use atikullahnasar\blog\Repositories\BlogCategories\BlogCategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BlogCategoryService implements BlogCategoryServiceInterface
{
    /**
     * @param BlogCategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        private readonly BlogCategoryRepositoryInterface $categoryRepository
    ) {
    }

    /**
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return $this->categoryRepository->paginate($perPage);
    }

    /**
     * @return Collection
     */
    public function getWithBlogsCount(): Collection
    {
        return $this->categoryRepository->getWithBlogsCount();
    }

    /**
     * @param int $id
     * @return BlogCategory|null
     */
    public function findById(int $id): ?BlogCategory
    {
        return $this->categoryRepository->find($id);
    }

    /**
     * @param array $data
     * @return BlogCategory
     */
    public function create(array $data): BlogCategory
    {
        $data['slug'] = Str::slug($data['name']);

        try {
            return $this->categoryRepository->create($data);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) { // Duplicate entry
                throw new HttpException(422, 'The category name already exists.');
            }
            throw $e;
        }
    }

    /**
     * @param int $id
     * @param array $data
     * @return BlogCategory
     */
    public function update(int $id, array $data)
    {
        $data['slug'] = Str::slug($data['name']);

        try {
            return $this->categoryRepository->update($id, $data);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) { // Duplicate entry
                throw new HttpException(422, 'The category name already exists.');
            }
            throw $e;
        }
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->categoryRepository->delete($id);
    }

    /**
     * @param int $id
     * @return BlogCategory
     */
    public function toggleStatus(int $id): BlogCategory
    {
        $category = $this->categoryRepository->find($id);
        $category->toggleStatus();
        return $category;
    }
}
