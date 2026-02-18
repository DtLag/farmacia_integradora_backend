<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear Categorías base
        $catAnalgesicos = Category::create(['name' => 'Analgésicos', 'description' => 'Medicamentos para el dolor']);
        $catAntibioticos = Category::create(['name' => 'Antibióticos', 'description' => 'Tratamiento de infecciones']);
        $catGastro = Category::create(['name' => 'Gastrointestinales', 'description' => 'Protección y alivio estomacal']);
        $catCuidado = Category::create(['name' => 'Cuidado Personal', 'description' => 'Higiene y aseo diario']);

        // 2. Crear Proveedores base
        $supGlobal = Supplier::create([
            'name' => 'Farmacéutica Global S.A.',
            'contact' => 'Juan Pérez',
            'phone_number' => '555-1234',
            'email' => 'ventas@f-global.com'
        ]);
        $supMedica = Supplier::create([
            'name' => 'Distribuidora Médica del Norte',
            'contact' => 'María López',
            'phone_number' => '555-5678',
            'email' => 'contacto@medinorte.com'
        ]);

        // 3. Crear 10 Productos de ejemplo
        $products = [
            [
                'codigo' => '75010001',
                'name' => 'Paracetamol 500mg',
                'presentation' => 'Caja con 20 tabletas',
                'purchase_price' => 15.00,
                'sale_price' => 25.50,
                'stock' => 50,
                'min_stock' => 10,
                'location' => 'Estante A1',
                'description' => 'Alivio del dolor y la fiebre',
                'category_id' => $catAnalgesicos->id,
                'supplier_id' => $supGlobal->id,
            ],
            [
                'codigo' => '75010002',
                'name' => 'Ibuprofeno 400mg',
                'presentation' => 'Caja con 10 cápsulas',
                'purchase_price' => 20.00,
                'sale_price' => 35.00,
                'stock' => 30,
                'min_stock' => 5,
                'location' => 'Estante A1',
                'description' => 'Antiinflamatorio y analgésico',
                'category_id' => $catAnalgesicos->id,
                'supplier_id' => $supGlobal->id,
            ],
            [
                'codigo' => '75010003',
                'name' => 'Amoxicilina 500mg',
                'presentation' => 'Frasco con 15 cápsulas',
                'purchase_price' => 45.00,
                'sale_price' => 85.00,
                'stock' => 15,
                'min_stock' => 5,
                'location' => 'Estante B2 (Antibióticos)',
                'description' => 'Antibiótico de amplio espectro',
                'category_id' => $catAntibioticos->id,
                'supplier_id' => $supMedica->id,
            ],
            [
                'codigo' => '75010004',
                'name' => 'Omeprazol 20mg',
                'presentation' => 'Caja con 14 cápsulas',
                'purchase_price' => 12.00,
                'sale_price' => 30.00,
                'stock' => 100,
                'min_stock' => 20,
                'location' => 'Estante C3',
                'description' => 'Protector gástrico',
                'category_id' => $catGastro->id,
                'supplier_id' => $supGlobal->id,
            ],
            [
                'codigo' => '75010005',
                'name' => 'Loratadina 10mg',
                'presentation' => 'Caja con 10 tabletas',
                'purchase_price' => 8.00,
                'sale_price' => 18.00,
                'stock' => 40,
                'min_stock' => 10,
                'location' => 'Estante A2',
                'description' => 'Antihistamínico para alergias',
                'category_id' => $catAnalgesicos->id,
                'supplier_id' => $supMedica->id,
            ],
            [
                'codigo' => '75010006',
                'name' => 'Alcohol Etílico 70%',
                'presentation' => 'Botella 500ml',
                'purchase_price' => 18.00,
                'sale_price' => 32.00,
                'stock' => 25,
                'min_stock' => 5,
                'location' => 'Área de curación G1',
                'description' => 'Antiséptico de uso externo',
                'category_id' => $catCuidado->id,
                'supplier_id' => $supMedica->id,
            ],
            [
                'codigo' => '75010007',
                'name' => 'Gasas Estériles',
                'presentation' => 'Paquete con 10 piezas',
                'purchase_price' => 5.50,
                'sale_price' => 12.00,
                'stock' => 60,
                'min_stock' => 15,
                'location' => 'Área de curación G1',
                'description' => 'Material de curación',
                'category_id' => $catCuidado->id,
                'supplier_id' => $supMedica->id,
            ],
            [
                'codigo' => '75010008',
                'name' => 'Pasta Dental Triple Acción',
                'presentation' => 'Tubo 75ml',
                'purchase_price' => 22.00,
                'sale_price' => 45.00,
                'stock' => 20,
                'min_stock' => 5,
                'location' => 'Pasillo Higiene',
                'description' => 'Cuidado bucal diario',
                'category_id' => $catCuidado->id,
                'supplier_id' => $supGlobal->id,
            ],
            [
                'codigo' => '75010009',
                'name' => 'Vitamina C Efervescente',
                'presentation' => 'Tubo con 10 tabletas',
                'purchase_price' => 35.00,
                'sale_price' => 65.00,
                'stock' => 15,
                'min_stock' => 5,
                'location' => 'Mostrador Vitrinas',
                'description' => 'Suplemento alimenticio',
                'category_id' => $catAnalgesicos->id, // Podría ser suplementos
                'supplier_id' => $supGlobal->id,
            ],
            [
                'codigo' => '75010010',
                'name' => 'Naproxeno Sódico 550mg',
                'presentation' => 'Caja con 12 tabletas',
                'purchase_price' => 28.00,
                'sale_price' => 52.00,
                'stock' => 8, // Bajo stock para probar alertas
                'min_stock' => 10,
                'location' => 'Estante A1',
                'description' => 'Analgésico fuerte',
                'category_id' => $catAnalgesicos->id,
                'supplier_id' => $supMedica->id,
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
}