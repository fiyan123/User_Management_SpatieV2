<?php

namespace App\Http\Controllers;

use App\Models\Posts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PostsController extends Controller
{
    public function index(Request $request)
    {
        // Check if user has all permissions
        $user = Auth::user();
        $hasCreatePermission = $user->hasPermissionTo('create posts');
        $hasEditPermission   = $user->hasPermissionTo('edit posts');
        $hasShowPermission   = $user->hasPermissionTo('show posts');
        $hasDeletePermission = $user->hasPermissionTo('delete posts');

        if ($hasCreatePermission || $hasEditPermission || $hasShowPermission || $hasDeletePermission) {

            $posts = Posts::all();

            $data = $posts;

            if ($request->ajax()) {
                $dataTable = DataTables::of($data)
                    ->addIndexColumn();

                $dataTable->addColumn('action', function ($row) {
                    $user = Auth::user();

                    $hasEditPermission = $user->hasPermissionTo('edit posts');
                    $hasShowPermission = $user->hasPermissionTo('show posts');
                    $hasDeletePermission = $user->hasPermissionTo('delete posts');

                    $btn = '<div class="d-flex align-items-center">';

                    if ($hasEditPermission && $hasShowPermission && $hasDeletePermission) {
                        $btn .= '<a href="' . route('posts.edit', [$row->id]) . '" title="Edit" class="btn btn-primary btn-icon-text">
                                    <label class="badge badge-primary">Edit</label>
                                </a>';
                        $btn .= '<a href="' . route('posts.show', [$row->id]) . '" title="show" class="btn btn-info btn-icon-text">
                                    <label class="badge badge-info">Show</label>
                                </a>';
                        $btn .= '<button type="submit" id="' . $row->id . '" title="Delete" class="delete btn btn-danger btn-icon-text">
                                    <label class="badge badge-danger">Delete</label>
                                </button>';
                    } elseif ($hasShowPermission) {
                        $btn .= '<div class="justify-content-end">
                                    <a href="' . route('posts.show', [$row->id]) . '" title="show" class="btn btn-info btn-icon-text">
                                        <label class="badge badge-info">Show</label>
                                    </a>
                                </div>';
                    } else {
                        $btn .= '-';
                    }

                    $btn .= '</div>';

                    return $btn;
                });

                return $dataTable->rawColumns(['action'])->make(true);
            }
            return view('posts.index', compact('hasCreatePermission'));
        } else {
            return abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }
    }

    public function create()
    {
        $user = Auth::user();
        $hasCreatePermission = $user->hasPermissionTo('create posts');

        if ($hasCreatePermission) {
            return view('posts.form', [
                'posts' => new Posts(),
            ]);
        } else {
            return abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }
    }

    public function store(Request $request)
    {
        // dd($request->all());

        $rules = [
            'judul' => ['required', 'string'],
            'isi_posts' => ['required', 'string'],
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json(['success' => 0, 'text' => $validator->errors()->first()], 422);
        }

        DB::beginTransaction();
        try {
            $posts = new Posts();
            $posts->judul = $request->judul;
            $posts->nama_pembuat = $request->nama_pembuat;
            $posts->isi_posts = $request->isi_posts;
            $posts->save();

            DB::commit();

            return response()->json(['text' => 'Posts Success Created!!!'], 200);
        } catch (\Exception $e) {
            // dd($e);
            DB::rollBack();

            return response()->json(['success' => 0, 'text' => 'An Error Occurred While Saving Data!!!'], 500);
        }
    }

    public function show($id)
    {
        $user = Auth::user();
        $hasShowPermission = $user->hasPermissionTo('show posts');

        $data = Posts::where('id', $id)->first();

        if ($hasShowPermission) {
            return view('posts.show', [
                'posts' => $data,
            ]);
        } else {
            return abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }
    }

    public function edit($id)
    {
        $user = Auth::user();
        $hasEditPermission = $user->hasPermissionTo('edit posts');

        $data = Posts::where('id', $id)->first();

        if ($hasEditPermission) {
            return view('posts.form', [
                'posts' => $data,
            ]);
        } else {
            return abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }
    }

    public function update(Request $request)
    {
        // dd($request->all());
        $rules = [
            'judul' => ['required', 'string'],
            'isi_posts' => ['required', 'string'],
        ];

        $data = $request->all();

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json(['success' => 0, 'text' => $validator->errors()->first()], 422);
        }

        DB::beginTransaction();
        try {
            $id = $request->id;

            $posts = Posts::findOrFail($id);
            $posts->judul = $request->judul;
            $posts->nama_pembuat = $request->nama_pembuat;
            $posts->isi_posts = $request->isi_posts;
            $posts->update();

            DB::commit();

            return response()->json(['text' => 'Posts Success Edited!!!'], 200);
        } catch (\Exception $e) {
            // dd($e);
            DB::rollBack();

            return response()->json(['success' => 0, 'text' => 'An Error Occurred While Saving Data!!!'], 500);
        }
    }

    public function destroy(Request $request)
    {
        $id = $request->id;

        $user = Auth::user();
        $hasDeletePermission = $user->hasPermissionTo('delete posts');

        $posts = Posts::where('id', $id)->first();

        if ($hasDeletePermission) {
            if ($posts) {
                $posts = Posts::where('id', $id)->first();

                DB::beginTransaction();

                try {
                    $posts->delete();

                    DB::commit();

                    return response()->json(['text' => 'Data Deleted Successfully!!!'], 200);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json(['text' => 'Failed To Delete Data'], 500);
                }
            } else {
                return response()->json(['text' => 'Data Not Found'], 404);
            }
        } else {
            return abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }
    }
}
