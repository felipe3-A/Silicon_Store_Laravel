<?php

namespace App\Http\Controllers;

use App\Models\OsposCustomer;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\OsposPerson;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OsposCustomerController extends Controller
{
    public function index()
    {
        try {
            $customers = OsposCustomer::all();
            Log::info('Clientes obtenidos correctamente.');
            return response()->json($customers, 200);
        } catch (\Exception $e) {
            Log::error('Error al obtener clientes: ' . $e->getMessage());
            return response()->json(['error' => 'Error al obtener clientes'], 500);
        }
    }

    public function show($id)
    {
        try {
            $customer = OsposCustomer::findOrFail($id);
            Log::info("Cliente con ID $id obtenido correctamente.");
            return response()->json($customer, 200);
        } catch (\Exception $e) {
            Log::error("Error al obtener cliente con ID $id: " . $e->getMessage());
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            Log::info('Datos recibidos para registrar cliente:', $request->all());

            // Validaciones
            $validatedData = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:ospos_people,email',
                'phone_number' => 'required|string|max:255',
                'address_1' => 'required|string|max:255',
                'address_2' => 'nullable|string|max:255',
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'zip' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'comments' => 'nullable|string',
                'company_name' => 'nullable|string|max:255',
                'account_number' => 'required|string|max:255|unique:ospos_customers,account_number',
                'discount' => 'required|numeric|min:0',
                'discount_type' => 'required|boolean',
                'package_id' => 'nullable|integer',
                'points' => 'nullable|integer',
                'password' => [
                    'required',
                    'string',
                    'min:6',
                    'max:20',
                    'regex:/^(?=.*[a-zA-ZáéíóúÁÉÍÓÚñÑ])(?=.*\d).{6,}$/u',
                    'confirmed'
                ],
            ]);

            // Crear persona
            $person = OsposPerson::create([
                'first_name' => mb_strtoupper(trim($validatedData['first_name'])),
                'last_name' => mb_strtoupper(trim($validatedData['last_name'])),
                'email' => mb_strtolower(trim($validatedData['email'])),
                'phone_number' => $validatedData['phone_number'],
                'address_1' => mb_strtolower(trim($validatedData['address_1'])),
                'address_2' => mb_strtolower(trim($validatedData['address_2'] ?? '')),
                'city' => mb_strtolower(trim($validatedData['city'])),
                'state' => mb_strtolower(trim($validatedData['state'])),
                'zip' => $validatedData['zip'],
                'country' => mb_strtolower(trim($validatedData['country'])),
                'comments' => $validatedData['comments'] ?? null,
            ]);

            Log::info("Persona creada con ID {$person->person_id}");

            // Crear cliente
            $customer = OsposCustomer::create([
                'person_id' => $person->person_id,
                'account_number' => $validatedData['account_number'],
                'company_name' => mb_strtoupper(trim($validatedData['company_name'] ?? '')),
                'discount' => $validatedData['discount'],
                'discount_type' => $validatedData['discount_type'],
                'package_id' => $validatedData['package_id'] ?? null,
                'points' => $validatedData['points'] ?? 0,
                'taxable' => 1,
                'employee_id' => Auth::id() ?? 1,
                'deleted' => 0,
            ]);

            Log::info("Cliente creado con ID {$customer->person_id}");
            Log::info("Creando usuario con nombre: " . $validatedData['first_name'] . ' ' . $validatedData['last_name']);

            // Crear usuario
            $user = new User();
            $user->name = isset($validatedData['first_name'], $validatedData['last_name'])
                ? mb_strtoupper(trim($validatedData['first_name'] . ' ' . $validatedData['last_name']))
                : 'SIN NOMBRE';

            $user->email = mb_strtolower(trim($validatedData['email']));
            $user->password = Hash::make($validatedData['password']);
            $user->person_id = $person->person_id;
            $user->save();


            Log::info("Usuario creado con ID {$user->id}");

            DB::commit();
            return response()->json(['message' => 'Cliente registrado exitosamente', 'data' => $customer], 201);

        } catch (\Illuminate\Validation\ValidationException $ve) {
            DB::rollBack();
            Log::warning('Errores de validación al registrar cliente', $ve->errors());
            return response()->json(['error' => 'Errores de validación', 'details' => $ve->errors()], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar cliente: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno al registrar el cliente', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $customer = OsposCustomer::findOrFail($id);
            Log::info("Actualizando cliente con ID $id");

            $validatedData = $request->validate([
                'company_name' => 'nullable|string|max:255',
                'account_number' => "nullable|string|max:255|unique:ospos_customers,account_number,$id",
                'taxable' => 'nullable|boolean',
                'tax_id' => 'nullable|string|max:32',
                'sales_tax_code_id' => 'nullable|integer',
                'discount' => 'nullable|numeric',
                'discount_type' => 'nullable|boolean',
                'package_id' => 'nullable|integer',
                'points' => 'nullable|integer',
                'deleted' => 'nullable|boolean',
                'employee_id' => 'nullable|integer',
                'consent' => 'nullable|boolean',
            ]);

            $customer->update($validatedData);

            Log::info("Cliente con ID $id actualizado correctamente");

            return response()->json(['message' => 'Cliente actualizado correctamente', 'data' => $customer], 200);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            Log::warning("Errores de validación al actualizar cliente con ID $id", $ve->errors());
            return response()->json(['error' => 'Errores de validación', 'details' => $ve->errors()], 422);
        } catch (\Exception $e) {
            Log::error("Error al actualizar cliente con ID $id: " . $e->getMessage());
            return response()->json(['error' => 'Error al actualizar el cliente'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $customer = OsposCustomer::findOrFail($id);
            $customer->delete();
            Log::info("Cliente con ID $id eliminado correctamente");
            return response()->json(['message' => 'Cliente eliminado correctamente'], 200);
        } catch (\Exception $e) {
            Log::error("Error al eliminar cliente con ID $id: " . $e->getMessage());
            return response()->json(['error' => 'Error al eliminar el cliente'], 500);
        }
    }
}
