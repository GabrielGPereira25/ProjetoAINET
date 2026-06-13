<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Models\Price;
use App\Models\Tshirt_image;
use App\Models\Category;
use App\Models\Customer;
use Illuminate\Http\Request;

class TshirtImageController extends Controller
{
    use \App\Traits\TshirtImageFileStorage;

    public function index(Request $request)
    {
        $filterByCategory = $request->query('category_id');
        $filterByName = $request->query('name');
        $filterByDescription = $request->query('description');

        $tshirtImagesQuery = Tshirt_image::query()->with(['category', 'customer']);

        if ($filterByCategory) {
            $tshirtImagesQuery->where('category_id', $filterByCategory);
        }
        if ($filterByName) {
            $tshirtImagesQuery->where('name', 'like', "%$filterByName%");
        }
        if ($filterByDescription) {
            $tshirtImagesQuery->where('description', 'like', "%$filterByDescription%");
        }

        $tshirt_images = $tshirtImagesQuery->paginate(20)->withQueryString();
        $categories = Category::all();

        return view('tshirt_images.index', compact('tshirt_images', 'filterByCategory', 'filterByName', 'filterByDescription', 'categories'));
    }

    public function create()
    {
        $tshirt_image = new Tshirt_image();
        $categories = Category::all();
        $customers = Customer::with('user')->get();
        return view('tshirt_images.create', compact('tshirt_image', 'categories', 'customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_file' => 'required|image|max:4096',
        ]);

        $newTshirtImage = Tshirt_image::create([
            'customer_id' => $validated['customer_id'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'image_url' => '',
        ]);

        if ($request->image_file) {
            $this->storeTshirtImage($request->image_file, $newTshirtImage);
        }

        $url = route('tshirt_images.show', ['tshirt_image' => $newTshirtImage]);
        $htmlMessage = "T-shirt Image <a href='$url'><strong>{$newTshirtImage->id}</strong> - '{$newTshirtImage->name}'</a> has been created successfully!";
        
        return redirect()->route('tshirt_images.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }

    public function show(Tshirt_image $tshirt_image)
    {
        $tshirt_image->load(['category', 'customer.user']);
        return view('tshirt_images.show', compact('tshirt_image'));
    }

    public function edit(Tshirt_image $tshirt_image)
    {
        $categories = Category::all();
        $customers = Customer::with('user')->get();
        return view('tshirt_images.edit', compact('tshirt_image', 'categories', 'customers'));
    }

    public function update(Request $request, Tshirt_image $tshirt_image)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image_file' => 'nullable|image|max:4096',
        ]);

        $tshirt_image->update([
            'customer_id' => $validated['customer_id'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        if ($request->image_file) {
            $this->deleteTshirtImage($tshirt_image);
            $this->storeTshirtImage($request->image_file, $tshirt_image);
        }

        $url = route('tshirt_images.show', ['tshirt_image' => $tshirt_image]);
        $htmlMessage = "T-shirt Image <a href='$url'><strong>{$tshirt_image->id}</strong> - '{$tshirt_image->name}'</a> has been updated successfully!";
        
        return redirect()->route('tshirt_images.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }

    public function destroy(Tshirt_image $tshirt_image)
    {
        $id = $tshirt_image->id;
        $name = $tshirt_image->name;
        
        $this->deleteTshirtImage($tshirt_image);
        $tshirt_image->delete();

        $alertType = 'success';
        $alertMsg = "T-shirt Image '{$name}' ({$id}) has been deleted successfully!";
        
        return redirect()->route('tshirt_images.index')
            ->with('alert-type', $alertType)
            ->with('alert-msg', $alertMsg);
    }

    public function getImage(Tshirt_image $tshirt_image)
    {
        if ($tshirt_image->customer_id != null) {
            $path = storage_path("app/private/tshirt_images_private/{$tshirt_image->image_url}");
            if (file_exists($path)) {
                return response()->file($path);
            }
        }
        abort(404);
    }

    public function showTshirts(Request $request)
    {
        $filterByCategory = $request->query('category_id');
        $filterByName = $request->query('name');
        $filterByDescription = $request->query('description');

        $tshirt_images = Tshirt_image::query();
        if (auth()->check() && auth()->user()->user_type == 'C') {
            $tshirt_images->where(function($query){
                $query->where('customer_id', auth()->user()->id)->orWhereNull('customer_id');
            });
        } else {
            $tshirt_images->whereNull('customer_id');
        }

        if ($filterByCategory) {
            $tshirt_images->where('category_id', $filterByCategory);
        }
        if ($filterByName) {
            $tshirt_images->where('name', 'like', "%$filterByName%");
        }
        if ($filterByDescription) {
            $tshirt_images->where('description', 'like', "%$filterByDescription%");
        }

        $tshirt_images = $tshirt_images->with('category')->paginate(12)->withQueryString();

        $categories = Category::all();
        $price = Price::first();

        return view('tshirt_images.show_tshirts', compact(
            'tshirt_images', 'price', 'categories', 'filterByCategory', 'filterByName', 'filterByDescription'
        ));
    }

    public function showTshirt(Tshirt_image $tshirt_image) {
        $colors = Color::all();
        return view('tshirt_images.show_tshirt', compact('tshirt_image', 'colors'));
    }
}
