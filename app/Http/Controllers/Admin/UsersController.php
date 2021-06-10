<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\DatosAcceso;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\User\EditUserRequest;
use App\Http\Requests\Admin\User\CreateUserRequest;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $users = User::with('roles')->get();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $roles = Role::query()->get();

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(CreateUserRequest $request)
    {
        $user = new User();
        $user->nombres = $request['nombres'];
        $user->apellido_paterno = $request['apellido_paterno'];
        $user->apellido_materno = $request['apellido_materno'];
        $user->email = $request['email'];
        $user->celular = $request['celular'];
        $user->password = $request['password'];
        $user->email_verified_at = date('Y-m-d');
        $user->save();

        if (isset($request->role)) {
            $user->assignRole($request['role']);
        }

        $file = $request->file('foto');

        if (isset($file)) {

            $image = \Image::make($file);

            $nombre_foto = $file->getClientOriginalName();

            if (!Storage::exists('usuarios_foto')) {
                Storage::makeDirectory('usuarios_foto');
            }

            if (!Storage::exists("usuarios_foto/{$user->id}")) {
                Storage::makeDirectory("usuarios_foto/{$user->id}");
            }

            $path = storage_path() . "/app/usuarios_foto/{$user->id}/";
            $image->save($path . $nombre_foto);

            $user->foto = $nombre_foto;
            $user->save();
        }

        if (isset($request->enviar_datos)) {
            try{
                $user->notify(new DatosAcceso($user, $request->password));
            }catch(\Throwable $th){
                Log::error('No se pudo enviar datos de acceso.  Error: '.$th->getMessage());
            }
        }

        return redirect()->route('admin.usuarios.index')->with([
            'message' => "El usuario {$user->nombres} {$user->apellido_paterno} se guardó con éxito"
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show(User $usuario)
    {
        $usuario->load(['roles']);

        return view('admin.users.show', [
            'user' => $usuario
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */

    public function edit(User $usuario)
    {
        return view('admin.users.edit', [
            'roles' => Role::query()->get(),
            'user'  => $usuario->load(['roles']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  User  $user
     * @return Response
     */
    public function update(User $usuario, EditUserRequest $request)
    {
        $usuario->nombres = $request['nombres'];
        $usuario->apellido_paterno = $request['apellido_paterno'];
        $usuario->apellido_materno = $request['apellido_materno'];
        $usuario->email = $request['email'];
        $usuario->celular = $request['celular'];
        $usuario->password = $request['password'];
        $usuario->save();

        $usuario->syncRoles($request->role);

        if (isset($request->enviar_datos)) {
            try {
                $usuario->notify(new DatosAcceso($usuario, $request->password));
            } catch(\Throwable $th){
                Log::error('No se pudo enviar datos de acceso.  Error: '.$th->getMessage());
            }
        }

        $file = $request->file('foto');

        if (isset($file)) {

            $image = \Image::make($file);

            $nombre_foto = $file->getClientOriginalName();

            if (!Storage::exists('usuarios_foto')) {
                Storage::makeDirectory('usuarios_foto');
            }

            if (!Storage::exists("usuarios_foto/{$usuario->id}")) {
                Storage::makeDirectory("usuarios_foto/{$usuario->id}");
            }

            $path = storage_path() . "/app/usuarios_foto/{$usuario->id}/";
            $image->save($path . $nombre_foto);

            $usuario->foto = $nombre_foto;
            $usuario->save();
        }

        return redirect()->back()->with([
            'message' => "El usuario se {$usuario->nombres} {$usuario->apellido_paterno} se actualizó con éxito"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  User $user
     * @return Response
     */
    public function destroy(Request $request, User $usuario)
    {
        $usuario->email = $usuario->email . 'D' . date('dmYHis') . 'U' . Auth::user()->id;
        $usuario->save();
        $usuario->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'msg'     => "Usuario {$usuario->nombres} eliminado",
                'id'      => $usuario->id
            ]);
        }

        return redirect()
            ->route('admin.usuarios.index')
            ->with([
                'message' => "El usuario se {$usuario->nombres} {$usuario->apellido_paterno} se eliminó con éxito"
            ]);
    }

    public function traer_usuarios_select2(Request $request)
    {
        $term  = $request->input('term');
        $page = $request->input('page', 1);

        $resultCount = 10;
        $offset = ($page - 1) * $resultCount;

        $results = User::query()
            ->where('nombres', 'like', "%{$term}%")
            ->orWhere('apellido_paterno', 'like', "%{$term}%")
            ->orWhere('apellido_materno', 'like', "%{$term}%")
            ->orderBy('nombres', 'asc')
            ->skip($offset)
            ->take($resultCount)
            ->get();

        $count = User::query()
            ->where('nombres', 'like', "%{$term}%")
            ->orWhere('apellido_paterno', 'like', "%{$term}%")
            ->orWhere('apellido_materno', 'like', "%{$term}%")
            ->count();

        $endCount = $offset + $resultCount;
        $morePages = $count > $endCount;

        if ($request->ajax()) {
            return response()->json([
                'results'       => $results,
                'pagination'    => [
                    'more' => $morePages
                ]
            ]);
        }

        return redirect()->back();
    }
}
