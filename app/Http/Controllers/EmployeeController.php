<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Http\Resources\EmployeeResource;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // Ambil parameter pencarian nama dan divisi dari body request
            $searchName = $request->input('name');
            $searchDivision = $request->input('division');

            // Query untuk mendapatkan data karyawan
            $query = Employee::with('division');

            if ($searchName) {
                $query->where('name', 'like', "%{$searchName}%");
            }

            if ($searchDivision) {
                $query->where('division_id', $searchDivision);
            }

            // Ambil data dengan pagination
            $employees = $query->paginate(5);

            return response()->json([
                'status' => 'success',
                'message' => 'Data retrieved successfully',
                'data' => [
                    'employees' => EmployeeResource::collection($employees), // Resource collection
                ],
                'pagination' => [
                    'total' => $employees->total(),
                    'count' => $employees->count(),
                    'per_page' => $employees->perPage(),
                    'current_page' => $employees->currentPage(),
                    'total_pages' => $employees->lastPage(),
                    'first_page_url' => $employees->url(1),
                    'last_page_url' => $employees->url($employees->lastPage()),
                    'next_page_url' => $employees->nextPageUrl(),
                    'prev_page_url' => $employees->previousPageUrl(),
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while retrieving data'.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:15',
                'division' => 'required|exists:divisions,id',
                'position' => 'required|string|max:255',
            ]);
    
            // Simpan foto ke storage
            $imagePath = $request->file('image')->store('employees', 'public');
    
            // Buat data karyawan baru
            $employee = new Employee();
            $employee->image = $imagePath;
            $employee->name = $request->name;
            $employee->phone = $request->phone;
            $employee->id_division = $request->division;
            $employee->position = $request->position;
            $employee->save();
    
            return response()->json([
                'status' => 'success',
                'message' => 'Employee created successfully',
            ], 201);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while creating the employee:'.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:15',
                'division' => 'required|exists:divisions,id',
                'position' => 'required|string|max:255',
            ]);

            // Temukan data karyawan berdasarkan ID
            $employee = Employee::findOrFail($id);

            // Update foto jika ada yang baru
            if ($request->hasFile('image')) {
                // Hapus foto lama dari storage jika perlu
                if ($employee->image) {
                    Storage::disk('public')->delete($employee->image);
                }
                // Simpan foto baru ke storage
                $imagePath = $request->file('image')->store('employees', 'public');
                $employee->image = $imagePath;
            }

            // Pembaruan atribut lainnya
            $employee->name = $request->name;
            $employee->phone = $request->phone;
            $employee->id_division = $request->division;
            $employee->position = $request->position;
            $employee->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Employee updated successfully',
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Tangani error validasi
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed'.$e->getMessage(),
                
            ], 422);
            
        } catch (\Exception $e) {
            // Tangani error umum
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating the employee'.$e->getMessage(),
                
            ], 500);
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Temukan data karyawan berdasarkan ID
            $employee = Employee::findOrFail($id);

            // Hapus foto dari storage jika ada
            if ($employee->image) {
                Storage::disk('public')->delete($employee->image);
            }

            // Hapus data karyawan
            $employee->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Employee deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting the employee: '.$e->getMessage(),
            ], 500);
        }
    }

}
