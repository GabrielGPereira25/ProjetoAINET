<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Models\Price;
use App\Models\Tshirt_image;
use Illuminate\Http\Request;

class TshirtImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Tshirt_image $tshirt_image)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tshirt_image $tshirt_image)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tshirt_image $tshirt_image)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tshirt_image $tshirt_image)
    {
        //
    }

    public function showTshirts()
    {

        $tshirt_images = Tshirt_image::query();
        if (auth()->check() && auth()->user()->user_type == 'C') {
            $tshirt_images->where(function($query){
                $query->where('customer_id', auth()->user()->id)->orWhereNull('customer_id');
            });
        } else {
            $tshirt_images->whereNull('customer_id');
        }

        $tshirt_images = $tshirt_images->with('category')->paginate(12)->withQueryString();


        $price=Price::first();
        return view('tshirt_images.show_tshirts', compact('tshirt_images','price'));
    }

    public function showTshirt(Tshirt_image $tshirt_image) {
        $colors = Color::all();
        return view('tshirt_images.show_tshirt', compact('tshirt_image', 'colors'));
    }
}
