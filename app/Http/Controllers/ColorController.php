<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Color;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\ColorFormRequest;

class ColorController extends Controller
{
    public function index(Request $request): View
    {
        $filterByName = $request->query('name');
        $colorsQuery = Color::query();
        if ($filterByName) {
            $colorsQuery->where('name', 'like', "%$filterByName%");
        }
        $allcolors = $colorsQuery->orderBy('name')->paginate(20)->withQueryString();;

        return view('colors.index')->with('colors', $allcolors)->with('filterByName', $filterByName);
    }

    public function show(Color $color): View
    {
        return view('colors.show')->with('color', $color);
    }

    public function create(): View
    {
        $newColor = new Color();
        return view('colors.create')->with('color', $newColor);
    }

    public function store(ColorFormRequest $request): RedirectResponse
    {
        $newColor = Color::create($request->validated());
        $url = route('colors.show', ['color' => $newColor]);
        $htmlMessage = "Color <a href='$url'><strong>{$newColor->code}</strong>
                    - '{$newColor->name}'</a> has been created successfully!";
        return redirect()->route('colors.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }


    public function edit(Color $color): View
    {
        return view('colors.edit')->with('color', $color);
    }

    public function update(ColorFormRequest $request, Color $color): RedirectResponse
    {
        $color->update($request->validated());
        $url = route('colors.show', ['color' => $color]);
        $htmlMessage = "Color <a href='$url'><strong>{$color->code}</strong> -
                    '{$color->name}'</a> has been updated successfully!";
        return redirect()->route('colors.index')
            ->with('alert-type', 'success')
            ->with('alert-msg', $htmlMessage);
    }

    public function destroy(Color $color): RedirectResponse
    {
        $color->delete();
        $alertType = 'success';
        $alertMsg = "Color {$color->name} ({$color->code}) has been deleted
                            successfully!";
        return redirect()->route('colors.index')
            ->with('alert-type', $alertType)
            ->with('alert-msg', $alertMsg);
    }
}
