<?php

namespace App\Http\Controllers;

use App\Models\Promocion;
use App\Models\ProductoPromocion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PromocionController extends Controller
{
    public function index()
    {
        $promociones = Promocion::orderBy('id', 'desc')->paginate(15, ['*'], 'promo_page');
        $productosPromociones = ProductoPromocion::with(['producto:id,nombre,sku', 'promocion:id,nombre'])->orderBy('id', 'desc')->paginate(50, ['*'], 'prod_page');
        
        // Needed for dropdowns
        $allPromotions = Promocion::select('id', 'nombre')->orderBy('id', 'desc')->get();

        return Inertia::render('Promociones/Index', [
            'promociones' => $promociones,
            'productosPromociones' => $productosPromociones,
            'allPromotions' => $allPromotions
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'tipo' => 'required|string|max:50',
            'descripcion' => 'nullable|string|max:255',
            'promo_price' => 'nullable|numeric',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date',
            'foto' => 'nullable|string|max:255',
            'foto_banner' => 'nullable|string|max:255',
        ]);

        Promocion::create($validated);

        return back()->with('success', 'Promoción creada exitosamente');
    }

    public function update(Request $request, Promocion $promocion)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'tipo' => 'required|string|max:50',
            'descripcion' => 'nullable|string|max:255',
            'promo_price' => 'nullable|numeric',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date',
            'foto' => 'nullable|string|max:255',
            'foto_banner' => 'nullable|string|max:255',
        ]);

        $promocion->update($validated);

        return back()->with('success', 'Promoción actualizada exitosamente');
    }

    public function destroy(Promocion $promocion)
    {
        $promocion->delete();
        return back()->with('success', 'Promoción eliminada exitosamente');
    }
}
