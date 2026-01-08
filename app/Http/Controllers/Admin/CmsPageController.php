<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use Illuminate\Http\Request;

class CmsPageController extends Controller
{
    public function index(Request $request)
    {
        $pages = CmsPage::latest()
            ->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'data' => $pages
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|unique:cms_pages',
            'content' => 'required|string',
            'meta_description' => 'nullable|string|max:160',
        ]);

        $page = CmsPage::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Page created successfully',
            'data' => $page
        ], 201);
    }

    public function update(Request $request, CmsPage $page)
    {
        $request->validate([
            'title' => "required|string|unique:cms_pages,title,$page->id",
            'content' => 'required|string',
            'meta_description' => 'nullable|string|max:160',
            'is_published' => 'boolean',
        ]);

        $page->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Page updated successfully',
            'data' => $page
        ]);
    }

    public function destroy(CmsPage $page)
    {
        $page->delete();

        return response()->json([
            'success' => true,
            'message' => 'Page deleted successfully'
        ]);
    }
}
