<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{

    public function index(Request $request)
    {
        // Check if user has all permissions
        $user = Auth::user();
        $hasCreatePermission = $user->hasPermissionTo('create roles');
        $hasEditPermission   = $user->hasPermissionTo('edit roles');
        $hasShowPermission   = $user->hasPermissionTo('show roles');
        $hasDeletePermission = $user->hasPermissionTo('delete roles');

        if ($hasCreatePermission || $hasEditPermission || $hasShowPermission || $hasDeletePermission) {

            $role = Role::all();

            $data = $role;

            if ($request->ajax()) {
                $dataTable = DataTables::of($data)
                    ->addIndexColumn();

                $dataTable->addColumn('action', function ($row) {
                    $user = Auth::user();

                    $hasEditPermission = $user->hasPermissionTo('edit roles');
                    $hasShowPermission = $user->hasPermissionTo('show roles');
                    $hasDeletePermission = $user->hasPermissionTo('delete roles');

                    $btn = '<div class="d-flex align-items-center">';

                    if ($hasEditPermission && $hasShowPermission && $hasDeletePermission) {
                        $btn .= '<a href="' . route('roles.edit', [$row->id]) . '" title="Edit" class="btn btn-primary btn-icon-text">
                                    <label class="badge badge-primary">Edit</label>
                                </a>';
                        $btn .= '<a href="' . route('roles.givePermissions', [$row->id]) . '" class="btn btn-info btn-icon-text">
                                    <label class="badge badge-info">Sycrons</label>
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
            return view('role-permission.role.index',  compact('hasCreatePermission'));
        } else {
            return abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }
    }

    public function create()
    {
        $user = Auth::user();
        $hasCreatePermission = $user->hasPermissionTo('create roles');

        if ($hasCreatePermission) {
            return view('role-permission.role.form', [
                'roles' => new Role(),
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
                'unique:roles,name'
            ],
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json(['success' => 0, 'text' => $validator->errors()->first()], 422);
        }

        DB::beginTransaction();
        try {
            $roles = new Role();
            $roles->name = $request->name;
            $roles->save();

            DB::commit();

            return response()->json(['text' => 'Role Success Created!!!'], 200);
        } catch (\Exception $e) {
            // dd($e);
            DB::rollBack();

            return response()->json(['success' => 0, 'text' => 'An Error Occurred While Saving Data!!!'], 500);
        }
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $data = Role::where('id', $id)->first();

        $user = Auth::user();
        $hasEditPermission = $user->hasPermissionTo('edit roles');

        if ($hasEditPermission) {
            return view('role-permission.role.form', [
                'roles' => $data,
            ]);
        } else {
            return abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }
    }

    public function update(Request $request)
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'unique:roles,name'
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

            $roles = Role::findOrFail($id);
            $roles->name = $request->name;
            $roles->update();

            DB::commit();

            return response()->json(['text' => 'Role Success Updated!!!'], 200);
        } catch (\Exception $e) {
            // dd($e);
            DB::rollBack();

            return response()->json(['success' => 0, 'text' => 'An Error Occurred While Saving Data!!!'], 500);
        }
    }

    public function destroy(Request $request)
    {
        $id = $request->id;

        $role = Role::find($id);

        if ($role) {
            $role = Role::where('id', $id)->first();

            DB::beginTransaction();

            try {
                $role->delete();

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

    public function addPermissionToRole($id)
    {
        $permission = Permission::all();
        $role = Role::findOrFail($id);
        $rolePermission = DB::table('role_has_permissions')->where('role_has_permissions.role_id', $role->id)->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')->all();

        return view('role-permission.role.add-permissions', [
            'role' => $role,
            'permission' => $permission,
            'rolePermission' => $rolePermission
        ]);
    }

    public function givePermissionToRole(Request $request, $id)
    {
        $request->validate([
            'permission' => 'required'
        ]);

        $role = Role::findOrFail($id);
        $role->syncPermissions($request->permission);

        return response()->json(['text' => 'Permission Granted Successfully!!!'], 200);
    }
}
