<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DivisionController extends Controller
{
    public function index(Request $request)
    {
        try {
            
            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string|max:255',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid input',
                    'errors' => $validator->errors(),
                ], 400);
            }

            // Ambil parameter pencarian nama dari body request
            $search = $request->input('name');

            // Query untuk mendapatkan data divisi
            $query = Division::query();

            if ($search) {
                $query->where('name', 'like', "%{$search}%");
            }

            // Ambil data dengan pagination
            $divisions = $query->select('id', 'name')->paginate(6);

            return response()->json([
                'status' => 'success',
                'message' => 'Data retrieved successfully',
                'data' => [
                    'divisions' => $divisions->items(), // Items on the current page
                ],
                'pagination' => [
                    'total' => $divisions->total(),
                    'count' => $divisions->count(),
                    'per_page' => $divisions->perPage(),
                    'current_page' => $divisions->currentPage(),
                    'total_pages' => $divisions->lastPage(),
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while retrieving data: '.$e->getMessage(),
            ], 500);
        }
    }
}
