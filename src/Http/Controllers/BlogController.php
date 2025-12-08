<?php

namespace atikullahnasar\blog\Http\Controllers;

use App\Http\Controllers\Controller;
use atikullahnasar\blog\Http\Request\StoreBlogRequest;
use atikullahnasar\blog\Models\Blog;
use atikullahnasar\blog\Services\Blogs\BlogServiceInterface;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class BlogController extends Controller
{
    public function __construct(
        private readonly BlogServiceInterface $blogService,
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax())
        {
            $blogs = $this->blogService->getAllWithRelations(['category', 'author']);
            return DataTables::of($blogs)
                ->addIndexColumn()
                ->addColumn('actions', function ($blog) {
                    return ''; // Actions will be rendered by DataTables
                })
                ->editColumn('status', function ($blog) {
                    return $blog->status;
                })
                ->addColumn('published_at', function ($blog) {
                    return $blog->published_at ? $blog->published_at->format('M d, Y') : 'N/A';
                })
                ->make(true);
        }

        return view('blogs::blogs.index');
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
    public function store(StoreBlogRequest $request)
    {
        $this->blogService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Blog created successfully!',
        ], 201);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // dd('show', $id);
        // $blog = $this->blogService->findBySlug($slug);

        // if (!$blog) {
        //     abort(404);
        // }

        // return view('blogs.show', [
        //     'blog' => $blog
        // ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Blog $blog)
    {
        return $blog->load('category');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreBlogRequest $request, string $id)
    {
        $this->blogService->update($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Blog updated successfully!'
        ]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->blogService->delete($id);
        return response()->json([
            'success' => true,
            'message' => 'Blog deleted successfully!'
        ]);
    }

    public function toggleStatus(Blog $blog)
    {
        $blog = $this->blogService->toggleStatus($blog->id);

        return response()->json([
            'success' => true,
            'message' => 'Blog status updated successfully!'
        ]);
    }
}
