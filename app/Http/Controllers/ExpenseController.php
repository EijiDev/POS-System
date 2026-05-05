<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        return view('expenses.index');
    }

    public function list()
    {
        return response()->json(Expense::orderByDesc('date')->orderByDesc('id')->get());
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'id'          => 'nullable|exists:expenses,id',
            'description' => 'required|string|max:255',
            'category'    => 'required|string|max:100',
            'amount'      => 'required|numeric|min:0',
            'date'        => 'required|date',
            'status'      => 'required|in:Paid,Pending',
        ]);

        if (!empty($data['id'])) {
            $expense = Expense::findOrFail($data['id']);
            $expense->update($data);
        } else {
            $expense = Expense::create($data);
        }

        return response()->json(['success' => true, 'id' => $expense->id]);
    }

    public function delete(Request $request)
    {
        $data = $request->validate(['id' => 'required|exists:expenses,id']);
        Expense::findOrFail($data['id'])->delete();
        return response()->json(['success' => true]);
    }
}
