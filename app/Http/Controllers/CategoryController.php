<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryFormRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    use \App\Traits\CategoryImageFileStorage;

    public function index(Request $request): View
    {
        $filterByName = $request->query('name');
        $categoriesQuery = Category::query();
        if ($filterByName) {
            $categoriesQuery->where('name', 'like', "%$filterByName%");
        }
        $allcategories = $categoriesQuery->orderBy('name')->paginate(20)->withQueryString();;

        return view('categories.index')->with('categories', $allcategories)->with('filterByName', $filterByName);
    }

    public function show(Category $category): View
    {
        return view('categories.show')->with('category', $category);
    }

    public function create(): View
    {
        $newCategory = new Category();
        return view('categories.create')->with('category', $newCategory);
    }

    public function store(CategoryFormRequest $request): RedirectResponse
    {
        $newCategory = Category::create($request->validated());
        if ($request->image_file) {
            $this->storeCategoryImage($request->image_file, $newCategory);
        }
        $url = route('categories.show', ['category' => $newCategory]);
        $htmlMessage = "Category <a href='$url'><strong>{$newCategory->id}</strong>
                    - '{$newCategory->name}'</a> has been created successfully!";
        return redirect()->route('categories.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }


    public function edit(Category $category): View
    {
        return view('categories.edit')->with('category', $category);
    }

    public function update(CategoryFormRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());
        if ($request->image_file) {
            $this->deleteCategoryImage($category);
            $this->storeCategoryImage($request->image_file, $category);
        }
        $url = route('categories.show', ['category' => $category]);
        $htmlMessage = "Category <a href='$url'><strong>{$category->id}</strong> -
                    '{$category->name}'</a> has been updated successfully!";
        return redirect()->route('categories.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }

    public function destroy(Category $category): RedirectResponse
    {

        $id = $category->id;
        $category->delete();
        $this->deleteImageFile($id);
        $alertType = 'success';
        $alertMsg = "Category {$category->name} ({$category->id}) has been deleted
                            successfully!";
        return redirect()->route('categories.index')
            ->with('alert-type', $alertType)
            ->with('alert-msg', $alertMsg);
    }

    public function destroyImage(Category $category): RedirectResponse
    {
        if ($this->deleteCategoryImage($category)) {
            return redirect()->back()
                ->with('alert-type', 'success')
                ->with('alert-msg', "Image of category {$category->id} has been deleted.");
        } else {
            return redirect()->back()
                ->with('alert-type', 'warning')
                ->with('alert-msg', "Photo of teacher {$category->id} does not exist.");
        }
    }
}
