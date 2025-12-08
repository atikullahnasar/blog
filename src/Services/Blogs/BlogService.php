<?php

namespace atikullahnasar\blog\Services\Blogs;

use atikullahnasar\blog\Models\Blog;
use atikullahnasar\blog\Repositories\Blogs\BlogRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BlogService implements BlogServiceInterface
{
    /**
     * @param BlogRepositoryInterface $blogRepository
     */
    public function __construct(
        private readonly BlogRepositoryInterface $blogRepository
    ) {
    }

    public function publisheddata()
    {
        return $this->blogRepository->paginate(null, 10, ['*'], ['category',], ['status' => 'published']);
    }
    /**
     * @param int $perPage
     * @param array $relations
     * @return LengthAwarePaginator
     */
    public function getAllPaginated(int $perPage = 10, array $relations = []): LengthAwarePaginator
    {
        return $this->blogRepository->paginate(null, $perPage, ['*'], $relations);
    }

    /**
     * @param array $relations
     * @return Collection
     */
    public function getAllWithRelations(array $relations = []): Collection
    {
        return $this->blogRepository->all(['*'], $relations);
    }

    /**
     * @param int $id
     * @param array $relations
     * @return Blog|null
     */
    public function findById(int $id, array $relations = []): ?Blog
    {
        return $this->blogRepository->find($id, ['*'], $relations);
    }

    /**
     * @param string $slug
     * @param array $relations
     * @return Blog|null
     */

    /**
     * @param array $data
     * @return Blog
     */
    public function create(array $data): Blog
    {
        try {
            $data['slug'] = Str::slug($data['title']);
            $data['author_id'] = Auth::id();

            if ($data['status'] === 'published' && empty($data['published_at'])) {
                $data['published_at'] = now();
            }

            if (isset($data['featured_image']) && $data['featured_image'] instanceof UploadedFile) {
                $data['featured_image'] = $this->uploadFeaturedImage($data['featured_image']);
            }

            return $this->blogRepository->create($data);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) { // Duplicate entry
                throw new HttpException(422, 'The blog title already exists.');
            }
            Log::error('Database error in BlogService create: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * @param int $id
     * @param array $data
     * @return Blog
     */
    public function update(int $id, array $data)
    {
        $blog = $this->blogRepository->find($id);

        $data['slug'] = Str::slug($data['title']);

        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        if (isset($data['featured_image']) && $data['featured_image'] instanceof UploadedFile) {
            if ($blog->featured_image) {
                Storage::disk('public')->delete($blog->featured_image);
            }
            $data['featured_image'] = $this->uploadFeaturedImage($data['featured_image']);
        } else {
            $data['featured_image'] = $blog->featured_image;
        }

        try {
            // dd($id, $data);
            return $this->blogRepository->update($id, $data);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) { // Duplicate entry
                throw new HttpException(422, 'The blog title already exists.');
            }
            Log::error('Database error in BlogService update: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $blog = $this->blogRepository->find($id);

        if ($blog->featured_image) {
            Storage::disk('public')->delete($blog->featured_image);
        }

        return $this->blogRepository->delete($id);
    }

    /**
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPublishedBlogs(int $perPage = 10): LengthAwarePaginator
    {
        return $this->blogRepository->getPublished($perPage);
    }

    /**
     * @param int $categoryId
     * @return LengthAwarePaginator
     */
    public function getByCategory(int $categoryId): LengthAwarePaginator
    {
        return $this->blogRepository->getByCategory($categoryId);
    }

    /**
     * @param string $query
     * @return LengthAwarePaginator
     */
    public function search(string $query): LengthAwarePaginator
    {
        return $this->blogRepository->search($query);
    }

    /**
     * @param int $id
     * @return Blog
     */
    public function toggleStatus(int $id): Blog
    {
        $blog = $this->blogRepository->find($id);
        $blog->toggleStatus();
        return $blog;
    }

    /**
     * @param UploadedFile $image
     * @return string
     */
    protected function uploadFeaturedImage(UploadedFile $image): string
    {
        $filename = uniqid() . '.' . $image->getClientOriginalExtension();
        $directory = 'blogs';

        $originalPath = $image->storeAs($directory, $filename, 'public');

        $thumbnailFilename = 'thumb_' . $filename;
        $thumbnailPath = storage_path("app/public/{$directory}/{$thumbnailFilename}");

        Image::read($image->getRealPath())
            ->resize(150, 150, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })
            ->save($thumbnailPath);

        return $originalPath;
    }
}
