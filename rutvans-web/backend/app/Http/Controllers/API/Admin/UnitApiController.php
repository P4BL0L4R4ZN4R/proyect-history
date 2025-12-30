<?php

namespace App\Http\Controllers\API\admin;

use App\Models\Unit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class UnitApiController extends Controller
{
    protected function addFullPhotoUrl($unit)
    {
        if ($unit->photo) {
            $unit->photo = asset('storage/' . $unit->photo);
        }
        return $unit;
    }

    protected function addFullPhotoUrlCollection($units)
    {
        return $units->map(function ($unit) {
            return $this->addFullPhotoUrl($unit);
        });
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $companyIds = $user->companies
            ->filter(fn($company) => $company->pivot->status === 'active')
            ->pluck('id')
            ->toArray();

        $units = Unit::with('site')
            ->whereHas('site', function ($query) use ($companyIds) {
                $query->whereIn('company_id', $companyIds);
            })
            ->get();

        $units = $this->addFullPhotoUrlCollection($units);

        return response()->json($units);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plate' => 'required|string|max:255',
            'capacity' => 'required|integer',
            'photo' => 'nullable|image|max:2048',
            'site_id' => 'required|exists:sites,id',
        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $path = $file->store('units_photos', 'public');
            $validated['photo'] = $path;
        }

        $unit = Unit::create($validated);
        $unit = $this->addFullPhotoUrl($unit);

        return response()->json($unit, 201);
    }

    public function show($id)
    {
        $unit = Unit::with('site')->findOrFail($id);
        $unit = $this->addFullPhotoUrl($unit);
        return response()->json($unit);
    }

    public function update(Request $request, $id)
    {
        $unit = Unit::findOrFail($id);

        $validated = $request->validate([
            'plate' => 'sometimes|string|max:255',
            'capacity' => 'sometimes|integer',
            'photo' => 'nullable|image|max:2048',
            'site_id' => 'sometimes|exists:sites,id',
        ]);

        if ($request->hasFile('photo')) {
            if ($unit->photo && Storage::disk('public')->exists($unit->photo)) {
                Storage::disk('public')->delete($unit->photo);
            }

            $file = $request->file('photo');
            $path = $file->store('units_photos', 'public');
            $validated['photo'] = $path;
        }

        $unit->update($validated);
        $unit = $this->addFullPhotoUrl($unit);

        return response()->json($unit);
    }

    public function destroy($id)
    {
        $unit = Unit::findOrFail($id);
        $unit->delete();
        return response()->json(['message' => 'Unidad eliminada']);
    }
}
