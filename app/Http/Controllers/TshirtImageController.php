<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tshirt_imageFormRequest;
use App\Models\Color;
use App\Models\Price;
use App\Models\Tshirt_image;
use App\Models\Category;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TshirtImageController extends Controller
{
    use \App\Traits\TshirtImageFileStorage;

    public function index(Request $request)
    {
        $filterByCategory = $request->query('category_id');
        $filterByName = $request->query('name');
        $filterByDescription = $request->query('description');

        $tshirtImagesQuery = Tshirt_image::query()->with(['category', 'customer']);

        if (auth()->user()->user_type === 'C') {
            $tshirtImagesQuery->where('customer_id', auth()->id());
        } else {
            $tshirtImagesQuery->whereNull('customer_id');
        }

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

    public function store(Tshirt_imageFormRequest $request)
    {
        $validated = $request->validated();
        
        $customer_id = null;
        $category_id = null;
        
        if (auth()->user()->user_type === 'C') {
            $customer_id = auth()->id();
        } else {
            $customer_id = $request->input('customer_id');
            $category_id = $request->input('category_id');
        }

        $newTshirtImage = Tshirt_image::create([
            'customer_id' => $customer_id,
            'category_id' => $category_id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'image_url' => '',
        ]);

        if ($request->hasFile('image_file')) {
            $this->storeTshirtImage($request->file('image_file'), $newTshirtImage);
        }

        $url = route('tshirt_images.show', ['tshirt_image' => $newTshirtImage]);
        $htmlMessage = "T-shirt Image <a href='$url'><strong>{$newTshirtImage->id}</strong> - '{$newTshirtImage->name}'</a> has been created successfully!";
        
        return redirect()->route('tshirt_images.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }

    public function show(Tshirt_image $tshirt_image)
    {
        if ($tshirt_image->customer_id != null && auth()->user()->user_type === 'C' && $tshirt_image->customer_id != auth()->id()) {
            abort(403);
        }
        $tshirt_image->load(['category', 'customer.user']);
        return view('tshirt_images.show', compact('tshirt_image'));
    }

    public function edit(Tshirt_image $tshirt_image)
    {
        if (auth()->user()->user_type === 'C' && $tshirt_image->customer_id != auth()->id()) abort(403);
        if (auth()->user()->user_type !== 'C' && $tshirt_image->customer_id != null) abort(403);

        $categories = Category::all();
        $customers = Customer::with('user')->get();
        return view('tshirt_images.edit', compact('tshirt_image', 'categories', 'customers'));
    }

    public function update(Tshirt_imageFormRequest $request, Tshirt_image $tshirt_image)
    {
        if (auth()->user()->user_type === 'C' && $tshirt_image->customer_id != auth()->id()) abort(403);
        if (auth()->user()->user_type !== 'C' && $tshirt_image->customer_id != null) abort(403);

        $validated = $request->validated();

        $category_id = $tshirt_image->category_id;
        if (auth()->user()->user_type !== 'C') {
            $category_id = $request->input('category_id');
        }

        $tshirt_image->update([
            'category_id' => $category_id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        if ($request->hasFile('image_file')) {
            $this->deleteTshirtImage($tshirt_image);
            $this->storeTshirtImage($request->file('image_file'), $tshirt_image);
        }

        $url = route('tshirt_images.show', ['tshirt_image' => $tshirt_image]);
        $htmlMessage = "T-shirt Image <a href='$url'><strong>{$tshirt_image->id}</strong> - '{$tshirt_image->name}'</a> has been updated successfully!";
        
        return redirect()->route('tshirt_images.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }

    public function destroy(Tshirt_image $tshirt_image)
    {
        if (auth()->user()->user_type === 'C' && $tshirt_image->customer_id != auth()->id()) abort(403);
        if (auth()->user()->user_type !== 'C' && $tshirt_image->customer_id != null) abort(403);

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
