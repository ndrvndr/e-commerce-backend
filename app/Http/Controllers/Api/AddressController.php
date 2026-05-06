<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AddressController extends Controller
{
   public function index(Request $request): JsonResponse
    {
        $addresses = $request->user()->addresses()
            ->orderByDesc('is_default') 
            ->latest()                  
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $addresses,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        if ($request->user()->addresses()->count() >= 4) {
            return response()->json([
                'success' => false,
                'message' => 'Maximum 4 addresses per user.',
            ], 422);
        }

        $validated = $request->validate([
            'country'      => 'required|string|max:255',
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'address'      => 'required|string|max:255',
            'note'         => 'nullable|string|max:255',
            'city'         => 'required|string|max:255',
            'province'     => 'required|string|max:255',
            'postal_code' => 'required|integer|digits_between:3,10',
            'phone_number' => 'required|string|max:20',
            'is_default'   => 'boolean',
        ]);

        if (!empty($validated['is_default']) && $validated['is_default']) {
            $request->user()->addresses()->update(['is_default' => false]);
        }

        if ($request->user()->addresses()->count() === 0) {
            $validated['is_default'] = true;
        }

        $address = $request->user()->addresses()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Address added successfully.',
            'data'    => $address,
        ], 201);
    }

    public function show(Request $request, Address $address): JsonResponse
    {
        if ($address->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $address,
        ]);
    }

    public function update(Request $request, Address $address): JsonResponse
    {
        if ($address->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found.',
            ], 404);
        }

        $validated = $request->validate([
            'country'      => 'sometimes|string|max:255',
            'first_name'    => 'sometimes|string|max:255',
            'last_name'     => 'sometimes|string|max:255',
            'address'      => 'sometimes|string|max:255',
            'note'         => 'nullable|string|max:255',
            'city'         => 'sometimes|string|max:255',
            'province'     => 'sometimes|string|max:255',
            'postal_code' => 'required|integer|digits_between:3,10',
            'phone_number' => 'sometimes|string|max:20',
            'is_default'   => 'sometimes|boolean',
        ]);

        if (!empty($validated['is_default']) && $validated['is_default']) {
            $request->user()->addresses()
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        if (isset($validated['is_default']) && !$validated['is_default'] && $address->is_default) {
            return response()->json([
                'success' => false,
                'message' => 'Must have at least one default address.',
            ], 422);
        }

        $address->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully.',
            'data'    => $address,
        ]);
    }

    public function destroy(Request $request, Address $address): JsonResponse
    {
        if ($address->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Address not found.',
            ], 404);
        }

        if ($address->is_default && $request->user()->addresses()->count() > 1) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete default address. Change to another default address first.',
            ], 422);
        }

        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully.',
        ]);
    }
}