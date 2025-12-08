<?php

namespace atikullahnasar\blog\Http\Controllers;

use App\Http\Controllers\Controller;
use atikullahnasar\blog\Http\Request\StoreBlogCategoryRequest;
use atikullahnasar\blog\Services\BlogCategories\BlogCategoryServiceInterface;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class BlogCategoryController extends Controller
{
    public function __construct(
        private readonly BlogCategoryServiceInterface $categoryService
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            $categories = $this->categoryService->getWithBlogsCount();

            return DataTables::of($categories)
                ->addIndexColumn()
                ->addColumn('actions', function ($category) {
                    return ''; // Actions will be rendered by DataTables
                })
                ->editColumn('status', function ($category) {
                    return $category->status;
                })
                ->make(true);
        }
        return view('blogs::blog-categories.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBlogCategoryRequest $request)
    {
        $this->categoryService->create($request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Category created successfully!',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return $this->categoryService->findById($id);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(StoreBlogCategoryRequest $request, string $id)
    {
        $this->categoryService->update($id, $request->validated());
        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully!',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->categoryService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully!'
        ]);
    }
    public function toggleStatus($blogCategory)
    {
        $this->categoryService->toggleStatus($blogCategory);
        return response()->json([
            'success' => true,
            'message' => 'Category status updated successfully!',
        ]);
    }
}
