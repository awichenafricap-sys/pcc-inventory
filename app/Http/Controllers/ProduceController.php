<?php

namespace App\Http\Controllers;

use App\Models\Produce;
use Illuminate\Http\Request;

class ProduceController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (! $request->user() || ! $request->user()->is_admin) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index()
    {
        $produce = Produce::paginate(15);
        return view('produce.index', compact('produce'));
    }

    public function create()
    {
        return view('produce.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Produce::create($data);

        return redirect()->route('produce.index')->with('success', 'Produce created successfully.');
    }

    public function edit(Produce $produce)
    {
        return view('produce.edit', compact('produce'));
    }

    public function update(Request $request, Produce $produce)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $produce->update($data);

        return redirect()->route('produce.index')->with('success', 'Produce updated successfully.');
    }

    public function destroy(Produce $produce)
    {
        $produce->delete();
        return redirect()->route('produce.index')->with('success', 'Produce deleted successfully.');
    }
}
