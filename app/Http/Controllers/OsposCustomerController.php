<?php

namespace App\Http\Controllers;

use App\Models\OsposCustomer;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\OsposPerson;
use Illuminate\Support\Facades\DB;
class OsposCustomerController extends Controller
{
    // Mostrar todos los clientes
    public function index()
    {
        $customers = OsposCustomer::all(); // Obtener todos los clientes
        return response()->json($customers);
    }

    // Mostrar un solo cliente por ID
    public function show($id)
    {
        $customer = OsposCustomer::findOrFail($id); // Buscar cliente por ID
        return response()->json($customer);
    }

    // Crear un nuevo cliente
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            \Log::info($request->all()); // Esto imprimirá todos los datos que se están recibiendo en la solicitud

            // Validar la solicitud
            $validatedData = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:ospos_people',
                'phone_number' => 'required|string|max:255',
                'address_1' => 'required|string|max:255',
                'address_2' => 'nullable|string|max:255', // Cambié 'required' por 'nullable'
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'zip' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'comments' => 'nullable|string',
                'company_name' => 'nullable|string|max:255',
                'account_number' => 'required|string|max:255|unique:ospos_customers',
                'discount' => 'required|numeric|min:0',
                'discount_type' => 'required|boolean',
                'package_id' => 'nullable|integer',
                'points' => 'nullable|integer',
                'password' => [
                    'required',
                    'string',
                    'min:6',
                    'max:20',
                    'regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*?&]{6,}$/',
                    'confirmed'
                ],
            ]);

            // Crear la persona
            $person = OsposPerson::create([
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'email' => $validatedData['email'],
                'phone_number' => $validatedData['phone_number'],
                'address_1' => $validatedData['address_1'],
                'city' => $validatedData['city'],
                'state' => $validatedData['state'],
                'zip' => $validatedData['zip'],
                'country' => $validatedData['country'],
                'comments' => $validatedData['comments'] ?? null,
            ]);

            // Crear el cliente
            $customer = OsposCustomer::create([
                'person_id' => $person->person_id,
                'account_number' => $validatedData['account_number'],
                'company_name' => $validatedData['company_name'] ?? null,
                'discount' => $validatedData['discount'],
                'discount_type' => $validatedData['discount_type'],
                'package_id' => $validatedData['package_id'] ?? null,
                'points' => $validatedData['points'] ?? 0,
                'taxable' => 1,
                'deleted' => 0,
            ]);

            // Crear el usuario
            $user = new User();
            $user->name = $validatedData['first_name'] . ' ' . $validatedData['last_name'];
            $user->email = $validatedData['email'];
            $user->password = Hash::make($validatedData['password']);
            $user->person_id = $person->person_id; // 🔥 Asegurar que el usuario tenga relación con la persona
            $user->save();

            DB::commit(); // Confirmar la transacción
            return response()->json($customer, 201);

        } catch (\Exception $e) {
            DB::rollBack(); // Revertir la transacción si hay error
            return response()->json(['error' => 'Error al registrar el cliente', 'message' => $e->getMessage()], 500);
        }
    }


    // Actualizar un cliente existente
    public function update(Request $request, $id)
    {
        $customer = OsposCustomer::findOrFail($id); // Buscar el cliente por ID

        // Validar los datos
        $validatedData = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255|unique:ospos_customers,account_number,' . $id,
            'taxable' => 'nullable|boolean',
            'tax_id' => 'nullable|string|max:32',
            'sales_tax_code_id' => 'nullable|integer',
            'discount' => 'nullable|decimal',
            'discount_type' => 'nullable|boolean',
            'package_id' => 'nullable|integer',
            'points' => 'nullable|integer',
            'deleted' => 'nullable|boolean',
            'employee_id' => 'nullable|integer',
            'consent' => 'nullable|boolean',
        ]);

        // Actualizar el cliente
        $customer->update($validatedData);
        return response()->json($customer);
    }

    // Eliminar un cliente
    public function destroy($id)
    {
        $customer = OsposCustomer::findOrFail($id); // Buscar el cliente por ID
        $customer->delete(); // Eliminar el cliente
        return response()->json(['message' => 'Cliente eliminado']);
    }
}
