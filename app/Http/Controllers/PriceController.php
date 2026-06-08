<?php

namespace App\Http\Controllers;

use App\Http\Requests\PriceFormRequest;
use App\Models\Price;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PriceController extends Controller
{
    public function edit(): View
    {
        $price = Price::first();
        return view('prices.edit')->with('price', $price);
    }

    public function update(PriceFormRequest $request): RedirectResponse
    {
        $price = Price::first();
        $price->update($request->validated());
        $htmlMessage = "Prices have been updated successfully!";
        return redirect()->route('prices.edit')
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }
}
