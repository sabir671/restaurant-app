<?php

namespace App\Http\Controllers\backend;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("backend.categories.index");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.categories.modal');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'image' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
        $imagePath = saveResizeImage($request->file('image'), 'categories_images');
        try {
            DB::beginTransaction();
            $Category = Category::create([
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->status,
                'image' => $imagePath,
            ]);

            DB::commit();
            return response()->json([
                'success' => JsonResponse::HTTP_OK,
                'message' => 'category created successfully.',
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
        $categories = Category::findOrFail($id);
        return view('backend.categories.modal', compact('categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'image' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            DB::beginTransaction();
            $category = Category::findOrFail($id);
            $category->update([
                'name' => $request->name,
                'price' => $request->price,
                'description' => $request->description,
                'status' => $request->status,
            ]);
            if ($request->hasFile('image')) {
                $imagePath = saveResizeImage($request->file('image'), 'categories_images');
                $category->update(['image' => $imagePath]);
            }

            DB::commit();
            return response()->json([
                'success' => JsonResponse::HTTP_OK,
                'message' => 'Category updated successfully.',
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
            $category = Category::findOrFail($id);
            $category->delete();
            return response()->json([
                'success' => JsonResponse::HTTP_OK,
                'message' => 'Category deleted successfully'
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function dataTable(Request $request)
    {
        $categories = Category::all();
        return Datatables::of($categories)
            ->addColumn('actions', function ($record) {
                $actions = '';
                if (auth()->user()->hasPermissionTo('edit_category')) {
                    $actions = '<div class="btn-list">';
                    if (auth()->user()->hasPermissionTo('edit_category')) {
                        $actions .= '<a data-act="ajax-modal" data-action-url="' . route('categories.edit', $record->id) . '" data-title="Edit category" class="btn btn-sm btn-primary">
                                        <span class="fe fe-edit"> </span>
                                    </a>';
                    }
                    if (auth()->user()->hasPermissionTo('delete_category')) {
                        $actions .= '<button type="button" class="btn btn-sm btn-danger delete" data-url="' . route('categories.destroy', $record->id) . '" data-method="get" data-table="#categories_datatable">
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
            ->addColumn('description', function ($record) {
                return isValue($record->description);

            })
            ->addColumn('image', function ($record) {
                return '<img src="' . getImage($record->image, ) . '" width="100" height="80" alt="category Image">';
            })
            ->addColumn('status', function ($record) {
                return '<span class="badge bg-' . statusClasses($record->status) . '">' . ucfirst($record->status) . '</span>';
            })
            ->rawColumns(['actions', 'name', 'status', 'description', 'image'])
            ->addIndexColumn()->make(true);
    }
}
