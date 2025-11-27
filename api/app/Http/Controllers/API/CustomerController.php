<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Customer::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('name')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $customers
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'nullable|email|unique:customers,email',
            'phone'    => 'nullable|string|max:20',
            'company'  => 'nullable|string|max:255',
            'address'  => 'nullable|string',
            'city'     => 'nullable|string',
            'state'    => 'nullable|string',
            'postal_code' => 'nullable|string',
            'country'  => 'nullable|string',
            'type'     => 'nullable|in:individual,company',
            'notes'    => 'nullable|string',
        ]);

        // TAMBAHKAN USER_ID SECARA OTOMATIS
        $validated['user_id'] = auth()->id();

        $customer = Customer::create($validated);

        return response()->json([
            'success' => true,
            'data'    => $customer,
            'message' => 'Customer berhasil ditambahkan'
        ], 201);
    }

    public function show(Customer $customer): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $customer->load('user')
        ]);
    }

    public function update(Request $request, Customer $customer): JsonResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['nullable', 'email', Rule::unique('customers')->ignore($customer->id)],
            'phone'    => 'nullable|string|max:20',
            'company'  => 'nullable|string|max:255',
            'address'  => 'nullable|string',
            'city'     => 'nullable|string',
            'state'    => 'nullable|string',
            'postal_code' => 'nullable|string',
            'country'  => 'nullable|string',
            'type'     => 'nullable|in:individual,company',
            'notes'    => 'nullable|string',
        ]);

        $customer->update($validated);

        return response()->json([
            'success' => true,
            'data'    => $customer,
            'message' => 'Customer berhasil diperbarui'
        ]);
    }

    public function destroy(Customer $customer): JsonResponse
    {
        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Customer berhasil dihapus'
        ]);
    }
}