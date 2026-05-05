<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        return view('customers.index');
    }

    public function list()
    {
        return response()->json(Customer::orderByDesc('points')->get());
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'id'    => 'nullable|exists:customers,id',
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        if (!empty($data['id'])) {
            $customer = Customer::findOrFail($data['id']);
            $customer->update(['name' => $data['name'], 'phone' => $data['phone'] ?? null, 'email' => $data['email'] ?? null]);
        } else {
            $customer = Customer::create([
                'name'  => $data['name'],
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
            ]);
        }

        return response()->json(['success' => true, 'id' => $customer->id]);
    }

    public function delete(Request $request)
    {
        $data = $request->validate(['id' => 'required|exists:customers,id']);
        Customer::findOrFail($data['id'])->delete();
        return response()->json(['success' => true]);
    }

    public function addPoints(Request $request)
    {
        $data = $request->validate([
            'id'     => 'required|exists:customers,id',
            'spent'  => 'required|numeric|min:0',
        ]);

        $customer = Customer::findOrFail($data['id']);
        $earned   = (int) floor($data['spent'] / 10); // 1 point per ₱10

        $customer->points        += $earned;
        $customer->total_visits  += 1;
        $customer->total_spent   += $data['spent'];
        $customer->tier           = Customer::tierFromPoints($customer->points);
        $customer->save();

        return response()->json([
            'success'  => true,
            'points'   => $customer->points,
            'tier'     => $customer->tier,
            'discount' => Customer::discountFromTier($customer->tier),
            'earned'   => $earned,
        ]);
    }

    public function lookup(Request $request)
    {
        $q = $request->query('q', '');
        $customers = Customer::where('name', 'like', "%$q%")
            ->orWhere('phone', 'like', "%$q%")
            ->limit(10)->get()
            ->map(fn($c) => [
                'id'       => $c->id,
                'name'     => $c->name,
                'phone'    => $c->phone,
                'points'   => $c->points,
                'tier'     => $c->tier,
                'discount' => Customer::discountFromTier($c->tier),
            ]);

        return response()->json($customers);
    }
}
