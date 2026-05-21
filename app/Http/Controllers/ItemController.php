<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;

class ItemController extends Controller
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
        $ingredients = Ingredient::with('category')->orderBy('name')->paginate(15);
        
        return view('supplies.index', compact('ingredients'));
    }


}
