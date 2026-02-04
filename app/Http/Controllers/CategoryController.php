<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        Gate::authorize('category.manage');

        return Inertia::render('Categories/Index', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('category.manage');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'sla_response_minutes' => ['nullable', 'integer', 'min:1'],
            'sla_resolution_minutes' => ['nullable', 'integer', 'min:1'],
        ]);

        Category::create($validated);

        return back();
    }

    public function update(Request $request, Category $category)
    {
        Gate::authorize('category.manage');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'sla_response_minutes' => ['nullable', 'integer', 'min:1'],
            'sla_resolution_minutes' => ['nullable', 'integer', 'min:1'],
        ]);

        $category->update($validated);

        return back();
    }

    public function destroy(Category $category)
    {
        Gate::authorize('category.manage');

        $category->delete();

        return back();
    }
}
