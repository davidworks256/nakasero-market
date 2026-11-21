<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.categories', [
            'categories' => Category::withCount('listings')->orderBy('name')->paginate(20),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(['name' => ['required', 'string', 'max:100', 'unique:categories,name']]);

        Category::create($validated);

        return back()->with('status', 'Category added.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate(['name' => ['required', 'string', 'max:100', 'unique:categories,name,'.$category->id]]);

        $category->update($validated);

        return back()->with('status', 'Category renamed.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->listings()->exists()) {
            return back()->withErrors(['name' => 'Move or delete this category\'s listings first.']);
        }

        $category->delete();

        return back()->with('status', 'Category deleted.');
    }
}
