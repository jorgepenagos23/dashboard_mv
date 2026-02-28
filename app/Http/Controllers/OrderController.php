<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::with('detalles.producto')
            ->where('cliente_id', '!=', '10465')
            ->orderBy('fecha_pedido', 'desc')
            ->paginate(15);

        // Ventas por Mes
        $ventasPorMes = \Illuminate\Support\Facades\DB::connection('mysql')
            ->table('pedidos')
            ->selectRaw('DATE_FORMAT(fecha_pedido, "%Y-%m") as mes, SUM(total) as total_ventas, COUNT(id) as total_pedidos')
            ->whereNotNull('fecha_pedido')
            ->where('cliente_id', '!=', '10465')
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->get();

        // Productos más vendidos
        $masVendidos = \Illuminate\Support\Facades\DB::connection('mysql')
            ->table('pedido_producto')
            ->join('pedidos', 'pedido_producto.pedido_id', '=', 'pedidos.id')
            ->join('productos', 'pedido_producto.producto_id', '=', 'productos.id')
            ->select('productos.nombre', 'productos.nombre_corto', 'productos.foto', \Illuminate\Support\Facades\DB::raw('SUM(pedido_producto.cantidad) as total_vendido'))
            ->where('pedidos.cliente_id', '!=', '10465')
            ->groupBy('productos.id', 'productos.nombre', 'productos.nombre_corto', 'productos.foto')
            ->orderByDesc('total_vendido')
            ->limit(5)
            ->get();

        // Productos menos vendidos (con al menos 1 venta)
        $menosVendidos = \Illuminate\Support\Facades\DB::connection('mysql')
            ->table('pedido_producto')
            ->join('pedidos', 'pedido_producto.pedido_id', '=', 'pedidos.id')
            ->join('productos', 'pedido_producto.producto_id', '=', 'productos.id')
            ->select('productos.nombre', 'productos.nombre_corto', 'productos.foto', \Illuminate\Support\Facades\DB::raw('SUM(pedido_producto.cantidad) as total_vendido'))
            ->where('pedidos.cliente_id', '!=', '10465')
            ->groupBy('productos.id', 'productos.nombre', 'productos.nombre_corto', 'productos.foto')
            ->orderBy('total_vendido')
            ->limit(5)
            ->get();

        return Inertia::render('Orders/Index', [
            'pedidos' => $pedidos,
            'ventasPorMes' => $ventasPorMes,
            'masVendidos' => $masVendidos,
            'menosVendidos' => $menosVendidos
        ]);
    }

    public function uploadSystem(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:20480', // 20MB
        ]);

        $file = $request->file('excel_file');
        
        try {
            $extension = $file->getClientOriginalExtension();
            $rows = [];
            
            if ($extension === 'csv') {
                $stream = fopen($file->getRealPath(), 'r');
                while (($data = fgetcsv($stream)) !== false) {
                    $rows[] = $data;
                }
                fclose($stream);
            } else {
                if ($xlsx = \Shuchkin\SimpleXLSX::parse($file->getRealPath())) {
                    $rows = $xlsx->rows();
                } else {
                    return back()->with('error', 'Error leyendo Excel: ' . \Shuchkin\SimpleXLSX::parseError());
                }
            }

            if (count($rows) > 1) {
                $headers = array_map('trim', array_map('strtoupper', $rows[0]));
                
                $idxCanal = array_search('CANAL', $headers);
                $idxEjecutivo = array_search('EJECUTIVO', $headers);
                $idxProducto = array_search('DESCRIPCION (PRODUC_IDE)', $headers);
                if ($idxProducto === false) $idxProducto = array_search('DESCRIPCION', $headers);
                $idxPiezas = array_search('PIEZAS', $headers);
                
                // Regex search for monto column since it can be "MONTO TOTAL" or similar
                $montoMatches = preg_grep('/monto\s?to.*/i', $headers);
                $idxMonto = $montoMatches ? array_key_first($montoMatches) : array_search('MONTO TOTAL', $headers);

                $totalMonto = 0;
                $ventasPorCanal = [];
                $ventasPorEjecutivo = [];
                $topProductos = [];
                // Only count non-empty rows for the valid row count
                $validRowsCount = 0;
                
                for ($i = 1; $i < count($rows); $i++) {
                    $row = $rows[$i];
                    // check if the row has data (at least 1 meaningful column)
                    if (empty(array_filter($row))) continue;
                    
                    $validRowsCount++;

                    $monto = 0;
                    if ($idxMonto !== false && isset($row[$idxMonto])) {
                        $m_val = str_replace(['$', ',', ' '], '', $row[$idxMonto]);
                        $monto = is_numeric($m_val) ? floatval($m_val) : 0;
                        $totalMonto += $monto;
                    }
                    
                    if ($idxCanal !== false && isset($row[$idxCanal])) {
                        $canal = trim($row[$idxCanal]) ?: 'Sin Canal';
                        $ventasPorCanal[$canal] = ($ventasPorCanal[$canal] ?? 0) + $monto;
                    }
                    
                    if ($idxEjecutivo !== false && isset($row[$idxEjecutivo])) {
                        $ejec = trim($row[$idxEjecutivo]) ?: 'Sin Ejecutivo';
                        $ventasPorEjecutivo[$ejec] = ($ventasPorEjecutivo[$ejec] ?? 0) + $monto;
                    }

                    if ($idxProducto !== false && isset($row[$idxProducto])) {
                        // avoid empty product descriptions
                        $prod = trim($row[$idxProducto]);
                        if (!empty($prod)) {
                            $p_val = ($idxPiezas !== false && isset($row[$idxPiezas])) ? floatval($row[$idxPiezas]) : 1;
                            $topProductos[$prod] = ($topProductos[$prod] ?? 0) + $p_val;
                        }
                    }
                }
                
                arsort($ventasPorCanal);
                arsort($ventasPorEjecutivo);
                arsort($topProductos);
                
                $metrics = [
                    'totalRows' => $validRowsCount,
                    'totalMonto' => $totalMonto,
                    'ventasPorCanal' => array_slice($ventasPorCanal, 0, 5, true),
                    'ventasPorEjecutivo' => array_slice($ventasPorEjecutivo, 0, 5, true),
                    'topProductos' => array_slice($topProductos, 0, 10, true),
                ];

                return back()->with('system_analysis', $metrics)->with('success', 'Análisis completado exitosamente.');
            } else {
                 return back()->with('error', 'El archivo parece estar vacío o no tiene la estructura correcta.');
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Fallo al procesar el archivo: ' . $e->getMessage());
        }
    }
}
