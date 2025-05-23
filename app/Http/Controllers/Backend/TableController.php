<?php

namespace App\Http\Controllers\backend;

use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class TableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("backend.tables.index");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("backend.tables.modal");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'guest_number' => 'required',
            'location' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
        try {
            DB::beginTransaction();
            $table = Table::create([
                'name' => $request->name,
                'guest_number' => $request->guest_number,
                'location' => $request->location,
                'status' => $request->status,
            ]);

            DB::commit();
            return response()->json([
                'success' => JsonResponse::HTTP_OK,
                'message' => 'Table created successfully.',
            ], JsonResponse::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $table = Table::findOrFail($id);

        return view('backend.tables.modal', compact('table'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'guest_number' => 'required',
            'location' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            DB::beginTransaction();
            $table = Table::findOrFail($id);
            $table->update([
                'name' => $request->name,
                'guest_number' => $request->guest_number,
                'location' => $request->location,
                'status' => $request->status,
            ]);
            DB::commit();
            return response()->json([
                'success' => JsonResponse::HTTP_OK,
                'message' => 'Table updated successfully.',
            ], JsonResponse::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $table = Table::findOrFail($id);
            $table->delete();
            return response()->json([
                'success' => JsonResponse::HTTP_OK,
                'message' => 'Table deleted successfully'
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function dataTable(Request $request)
    {
        $tables = Table::all();
        return Datatables::of($tables)
            ->addColumn('actions', function ($record) {
                $actions = '';
                if (auth()->user()->hasPermissionTo('edit_table')) {
                    $actions = '<div class="btn-list">';
                    if (auth()->user()->hasPermissionTo('edit_table')) {
                        $actions .= '<a data-act="ajax-modal" data-action-url="' . route('tables.edit', $record->id) . '" data-title="Edit table" class="btn btn-sm btn-primary">
                                        <span class="fe fe-edit"> </span>
                                    </a>';
                    }
                    if (auth()->user()->hasPermissionTo('delete_table')) {
                        $actions .= '<button type="button" class="btn btn-sm btn-danger delete" data-url="' . route('tables.destroy', $record->id) . '" data-method="get" data-table="#tables_datatable">
                                        <span class="fe fe-trash-2"> </span>
                                    </button>';
                    }
                    $actions .= '</div>';
                }
                return $actions;
            })
            ->addColumn('name', function ($record) {
                return $record->name;
            })
            ->addColumn('guest_number', function ($record) {
                return isValue($record->guest_number);

            })
            ->addColumn('location', function ($record) {
                return isValue($record->location);

            })
            ->addColumn('status', function ($record) {
                return '<span class="badge bg-' . statusClasses($record->status) . '">' . ucfirst($record->status) . '</span>';
            })
            ->rawColumns(['actions', 'name', 'status', 'description', 'location'])
            ->addIndexColumn()->make(true);
    }
}
