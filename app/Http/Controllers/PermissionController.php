<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        // Check if user has all permissions
        $user = Auth::user();
        $hasCreatePermission = $user->hasPermissionTo('create permissions');
        $hasEditPermission   = $user->hasPermissionTo('edit permissions');
        $hasDeletePermission = $user->hasPermissionTo('delete permissions');

        if ($hasCreatePermission || $hasEditPermission || $hasDeletePermission) {

            $permission = Permission::all();

            $data = $permission;

            if ($request->ajax()) {
                $dataTable = DataTables::of($data)
                    ->addIndexColumn();

                $dataTable->addColumn('action', function ($row) {
                    $user = Auth::user();

                    $hasEditPermission = $user->hasPermissionTo('edit permissions');
                    $hasDeletePermission = $user->hasPermissionTo('delete permissions');

                    $btn = '<div class="d-flex align-items-center">';

                    if ($hasEditPermission && $hasDeletePermission) {
                        $btn .= '<a href="' . route('permissions.edit', [$row->id]) . '" title="Edit" class="btn btn-primary btn-icon-text">
                                      <label class="badge badge-primary">Edit</label>
                                  </a>';
                        $btn .= '<button type="submit" id="' . $row->id . '" title="Delete" class="delete btn btn-danger btn-icon-text">
                                      <label class="badge badge-danger">Delete</label>
                                  </button>';
                    } else {
                        $btn .= '-';
                    }

                    $btn .= '</div>';

                    return $btn;
                });

                return $dataTable->rawColumns(['action'])->make(true);
            }
            return view('role-permission.permission.index');
        } else {
            return abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }
    }

    public function create()
    {
        $user = Auth::user();
        $hasCreatePermission = $user->hasPermissionTo('create permissions');

        if ($hasCreatePermission) {
            return view('role-permission.permission.form', [
                'permission' => new Permission(),
            ]);
        } else {
            return abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }
    }

    public function store(Request $request)
    {
        // dd($request->all());

        $rules = [
            'name' => [
                'required',
                'string',
                'unique:permissions,name'
            ],
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json(['success' => 0, 'text' => $validator->errors()->first()], 422);
        }

        DB::beginTransaction();
        try {
            $permission = new Permission();
            $permission->name = $request->name;
            $permission->save();

            DB::commit();

            return response()->json(['text' => 'Permission Success Created!!'], 200);
        } catch (\Exception $e) {
            // dd($e);
            DB::rollBack();

            return response()->json(['success' => 0, 'text' => 'Terjadi kesalahan saat menyimpan data'], 500);
        }
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $data = Permission::where('id', $id)->first();

        return view('role-permission.permission.form', [
            'permission' => $data,
        ]);
    }

    public function update(Request $request)
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'unique:permissions,name'
            ],
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json(['success' => 0, 'text' => $validator->errors()->first()], 422);
        }

        DB::beginTransaction();
        try {
            $id = $request->id;

            $permission = Permission::findOrFail($id);
            $permission->name = $request->name;
            $permission->update();

            DB::commit();

            return response()->json(['text' => 'Permission Success Edited!!'], 200);
        } catch (\Exception $e) {
            // dd($e);
            DB::rollBack();

            return response()->json(['success' => 0, 'text' => 'Terjadi kesalahan saat menyimpan data'], 500);
        }
    }

    public function destroy(Request $request)
    {
        $id = $request->id;

        $permission = Permission::find($id);

        if ($permission) {
            $permission = Permission::where('id', $id)->first();

            DB::beginTransaction();

            try {
                $permission->delete();

                DB::commit();

                return response()->json(['text' => 'Data Deleted Successfully!!!'], 200);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['text' => 'Failed To Delete Data'], 500);
            }
        } else {
            return response()->json(['text' => 'Data Not Found'], 404);
        }
    }
}
