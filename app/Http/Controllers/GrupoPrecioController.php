<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\ClientePromocion;
use App\Models\GrupoPrecioRegla;
use App\Models\Promocion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class GrupoPrecioController extends Controller
{
    public function index(Request $request)
    {
        // Grupos GP1-GP6 con su % de descuento
        $grupos = Promocion::where('tipo', 'GRUPO_PRECIO')
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'promo_price', 'descripcion']);

        // Reglas por grupo (lineas, sublineas, skus)
        $reglas = GrupoPrecioRegla::orderBy('grupo')
            ->get(['id','grupo','lineas','sublineas_inc','sublineas_exc','skus_inc','skus_exc','sublineas_rates','lineas_rates']);

        // Clientes con grupo activo hoy
        $clientesConGrupo = ClientePromocion::with([
                'cliente:id,cliente,razon_social,email',
                'promocion:id,nombre,promo_price',
            ])
            ->whereHas('promocion', fn($q) => $q->where('tipo', 'GRUPO_PRECIO'))
            ->whereDate('fecha_inicio', '<=', now())
            ->whereDate('fecha_fin', '>=', now())
            ->paginate(20, ['*'], 'asignaciones_page')
            ->withQueryString();

        // Todos los clientes para el dropdown de asignación
        $search = $request->get('buscar_cliente', '');
        $todosClientes = Cliente::when($search, function ($q) use ($search) {
                $q->where('cliente', 'like', "%{$search}%")
                  ->orWhere('razon_social', 'like', "%{$search}%");
            })
            ->select('id', 'cliente', 'razon_social')
            ->limit(30)
            ->get();

        return Inertia::render('GruposPrecio/Index', [
            'grupos'          => $grupos,
            'reglas'          => $reglas,
            'clientesConGrupo'=> $clientesConGrupo,
            'todosClientes'   => $todosClientes,
            'filters'         => ['buscar_cliente' => $search],
        ]);
    }

    private function invalidarCacheExpress(): void
    {
        try {
            Http::timeout(3)->post(env('EXPRESS_API_URL') . '/grupo-precio/cache/invalidar');
        } catch (\Throwable) {
            // No bloquea si el API Express no responde
        }
    }

    // PUT /grupos-precio/reglas/{grupo}
    public function updateRegla(Request $request, string $grupo)
    {
        $data = $request->validate([
            'lineas'           => 'nullable|array',
            'lineas.*'         => 'string',
            'sublineas_inc'    => 'nullable|array',
            'sublineas_inc.*'  => 'string',
            'sublineas_exc'    => 'nullable|array',
            'sublineas_exc.*'  => 'string',
            'skus_inc'         => 'nullable|array',
            'skus_inc.*'       => 'string',
            'skus_exc'         => 'nullable|array',
            'skus_exc.*'       => 'string',
            'sublineas_rates'  => 'nullable|array',
            'sublineas_rates.*'=> 'numeric|min:0|max:100',
            'lineas_rates'     => 'nullable|array',
            'lineas_rates.*'   => 'numeric|min:0|max:100',
        ]);

        GrupoPrecioRegla::updateOrCreate(
            ['grupo' => $grupo],
            [
                'lineas'          => $data['lineas']          ?? [],
                'sublineas_inc'   => $data['sublineas_inc']   ?? [],
                'sublineas_exc'   => $data['sublineas_exc']   ?? [],
                'skus_inc'        => $data['skus_inc']        ?? [],
                'skus_exc'        => $data['skus_exc']        ?? [],
                'sublineas_rates' => $data['sublineas_rates'] ?? [],
                'lineas_rates'    => $data['lineas_rates']    ?? [],
            ]
        );

        $this->invalidarCacheExpress();

        return back()->with('success', "Reglas de {$grupo} actualizadas correctamente.");
    }

    // POST /grupos-precio/asignar
    public function asignarGrupo(Request $request)
    {
        $data = $request->validate([
            'id_cliente'   => 'required|integer|exists:cliente,id',
            'grupo'        => 'required|string',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin'    => 'nullable|date',
        ]);

        $promocion = Promocion::where('nombre', $data['grupo'])
            ->where('tipo', 'GRUPO_PRECIO')
            ->firstOrFail();

        // Eliminar asignación GP anterior
        ClientePromocion::where('id_cliente', $data['id_cliente'])
            ->whereHas('promocion', fn($q) => $q->where('tipo', 'GRUPO_PRECIO'))
            ->delete();

        ClientePromocion::create([
            'id_cliente'   => $data['id_cliente'],
            'id_promocion' => $promocion->id,
            'fecha_inicio' => $data['fecha_inicio'] ?? now()->toDateString(),
            'fecha_fin'    => $data['fecha_fin']    ?? '2030-12-31',
        ]);

        $this->invalidarCacheExpress();

        return back()->with('success', "Cliente asignado a {$data['grupo']} correctamente.");
    }

    // DELETE /grupos-precio/remover/{id_cliente}
    public function removerGrupo(int $idCliente)
    {
        ClientePromocion::where('id_cliente', $idCliente)
            ->whereHas('promocion', fn($q) => $q->where('tipo', 'GRUPO_PRECIO'))
            ->delete();

        $this->invalidarCacheExpress();

        return back()->with('success', 'Grupo de precio removido correctamente.');
    }

    // POST /grupos-precio/crear
    public function crearGrupo(Request $request)
    {
        $data = $request->validate([
            'nombre'          => 'required|string|max:50|unique:promociones,nombre',
            'descripcion'     => 'nullable|string|max:255',
            'promo_price'     => 'required|numeric|min:0|max:100',
            'sublineas_inc'   => 'nullable|array',
            'sublineas_inc.*' => 'string|max:100',
        ]);

        $nombre = strtoupper(trim($data['nombre']));

        $promocion = Promocion::create([
            'nombre'      => $nombre,
            'descripcion' => $data['descripcion'] ?? null,
            'promo_price' => $data['promo_price'],
            'tipo'        => 'GRUPO_PRECIO',
            'fecha_inicio' => now()->startOfDay(),
            'fecha_fin'    => '2030-12-31 23:59:59',
        ]);

        if (!empty($data['sublineas_inc'])) {
            GrupoPrecioRegla::updateOrCreate(
                ['grupo' => $nombre],
                [
                    'lineas'        => [],
                    'sublineas_inc' => $data['sublineas_inc'],
                    'sublineas_exc' => [],
                    'skus_inc'      => [],
                    'skus_exc'      => [],
                ]
            );
        }

        $this->invalidarCacheExpress();

        return back()->with('success', "Grupo {$nombre} creado correctamente.");
    }

    // PATCH /grupos-precio/porcentaje/{id}
    public function updatePorcentaje(Request $request, Promocion $promocion)
    {
        $data = $request->validate([
            'promo_price' => 'required|numeric|min:0|max:100',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $promocion->update($data);

        return back()->with('success', "Descuento de {$promocion->nombre} actualizado.");
    }
}
