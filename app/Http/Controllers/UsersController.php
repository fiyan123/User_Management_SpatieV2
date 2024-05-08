<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UsersController extends Controller
{

    public function index(Request $request)
    {
        // Check if user has all permissions
        $user = Auth::user();
        $hasCreatePermission = $user->hasPermissionTo('create users');
        $hasEditPermission   = $user->hasPermissionTo('edit users');
        $hasDeletePermission = $user->hasPermissionTo('delete users');

        if ($hasCreatePermission || $hasEditPermission || $hasDeletePermission) {

            $users = User::all();

            $data = $users;

            if ($request->ajax()) {
                $dataTable = DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('roles', function ($row) {
                        $btn = '';
                        $data = DB::select("SELECT b.name FROM model_has_roles a
                            LEFT JOIN roles b ON a.role_id = b.id WHERE model_id = ?", [$row->id]);
                        foreach ($data as $val) {
                            $btn .= '<span class="btn btn-info btn-sm">' . $val->name . '</span>';
                        }
                        return $btn;
                        dd($data);
                    });

                $dataTable->addColumn('action', function ($row) {
                    $user = Auth::user();

                    $hasEditPermission   = $user->hasPermissionTo('edit users');
                    $hasDeletePermission = $user->hasPermissionTo('delete users');

                    $btn = '<div class="d-flex align-items-center">';

                    if ($hasEditPermission && $hasDeletePermission) {
                        $btn .= '<a href="' . route('users.edit', [$row->id]) . '" title="Edit" class="btn btn-primary btn-icon-text">
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

                return $dataTable->rawColumns(['action', 'roles'])->make(true);
            }
            return view('role-permission.users.index');
        } else {
            return abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }
    }

    public function create()
    {
        $user = Auth::user();
        $hasCreatePermission = $user->hasPermissionTo('create users');

        if ($hasCreatePermission) {
            return view('role-permission.users.form', [
                'users' => new User(),
                'roles' => Role::all(),
            ]);
        } else {
            return abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }
    }

    public function store(Request $request)
    {
        // dd($request->all());

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:20'],
            'roles' => ['required'],
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json(['success' => 0, 'text' => $validator->errors()->first()], 422);
        }

        DB::beginTransaction();
        try {
            $users = new User();
            $users->name = $request->name;
            $users->email = $request->email;
            $users->password = Hash::make($request->password);

            $users->syncRoles($request->roles);

            $users->save();

            DB::commit();

            return response()->json(['text' => 'Users Success Created!!!'], 200);
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
        $user = User::findOrFail($id);
        $userRoles = $user->roles->pluck('name', 'name')->all();

        return view('role-permission.users.form', [
            'users' => $user,
            'roles' => Role::all(),
            'userRoles' => $userRoles,
        ]);
    }

    public function update(Request $request)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required'],
            'password' => ['required', 'string', 'min:8', 'max:20'],
            'roles' => ['required'],
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json(['success' => 0, 'text' => $validator->errors()->first()], 422);
        }

        DB::beginTransaction();
        try {
            $id = $request->id;

            $users = User::findOrFail($id);
            $users->name = $request->name;
            $users->email = $request->email;
            $users->password = Hash::make($request->password);

            $users->syncRoles($request->roles);

            $users->update();

            DB::commit();

            return response()->json(['text' => 'Users Success Edited!!'], 200);
        } catch (\Exception $e) {
            // dd($e);
            DB::rollBack();

            return response()->json(['success' => 0, 'text' => 'Terjadi kesalahan saat menyimpan data'], 500);
        }
    }

    public function destroy(Request $request)
    {
        $id = $request->id;

        $users = User::find($id);

        if ($users) {
            $users = User::where('id', $id)->first();

            DB::beginTransaction();

            try {
                $users->delete();

                DB::commit();

                return response()->json(['message' => 'Data berhasil dihapus'], 200);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['message' => 'Gagal menghapus data'], 500);
            }
        } else {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }
    }
}
