<?php

namespace App\Http\Controllers\backend;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;


class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("backend.menus.index");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("backend.menus.modal");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'status' => 'required',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
                   $imagePath = saveResizeImage($request->file('image'), 'menu _images');


        try {
            DB::beginTransaction();
            $menu = Menu::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'status' => $request->status,
                'image' => $imagePath,
            ]);

            DB::commit();
            return response()->json([
                'success' => JsonResponse::HTTP_OK,
                'message' => 'Menu created successfully.',
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
        $menu = Menu::findOrFail($id);
        return view('backend.menus.modal', compact('menu'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'image' => 'required',
            'price' => 'required',
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
            $menu = Menu::findOrFail($id);
            $menu->update([
                'name' => $request->name,
                'price' => $request->price,
                'description' => $request->description,
                'status' => $request->status,
            ]);
            if ($request->hasFile('image')) {
                $imagePath = saveResizeImage($request->file('image'), 'menu_images');
                $menu->update([ 'image' => $imagePath ]);
            }

            DB::commit();
            return response()->json([
                'success' => JsonResponse::HTTP_OK,
                'message' => 'Menu updated successfully.',
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
            $menu = Menu::findOrFail($id);
            $menu->delete();
            return response()->json([
                'success' => JsonResponse::HTTP_OK,
                'message' => 'menu deleted successfully'
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function dataTable(Request $request)
    {
        $menus = Menu::all();
        return Datatables::of($menus)
            ->addColumn('actions', function ($record) {
                $actions = '';
                if (auth()->user()->hasPermissionTo('edit_menu')) {
                    $actions = '<div class="btn-list">';
                    if (auth()->user()->hasPermissionTo('edit_menu')) {
                        $actions .= '<a data-act="ajax-modal" data-action-url="' . route('menus.edit', $record->id) . '" data-title="Edit Menu" class="btn btn-sm btn-primary">
                                        <span class="fe fe-edit"> </span>
                                    </a>';
                    }
                    if (auth()->user()->hasPermissionTo('delete_menu')) {
                        $actions .= '<button type="button" class="btn btn-sm btn-danger delete" data-url="' . route('menus.destroy', $record->id) . '" data-method="get" data-table="#menus_datatable">
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
                return '<img src="' . getImage($record->image, ) . '" width="100" height="80" alt="Menu Image">';
            })



            ->addColumn('price', function ($record) {
                return isValue($record->price);
            })
            ->addColumn('status', function ($record) {
                return '<span class="badge bg-' . statusClasses($record->status) . '">' . ucfirst($record->status) . '</span>';
            })
            ->rawColumns(['actions', 'price', 'name', 'status', 'description', 'image'])
            ->addIndexColumn()->make(true);
    }
}
