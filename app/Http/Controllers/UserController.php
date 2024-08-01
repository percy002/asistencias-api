<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Asistencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        
        $fechaFiltro = Carbon::create(2024, 8, 1);

        $asistenciasDia1 = Asistencia::with('user')
            ->whereDate('created_at', $fechaFiltro)
            ->get();

        $asistenciasDia2 = Asistencia::with('user')
            ->whereDate('created_at', Carbon::create(2023, 8, 2))
            ->get();

        $asistenciasDia3 = Asistencia::with('user')
            ->whereDate('created_at', Carbon::create(2023, 8, 3))
            ->get();

        return response()->json([
            'asistencias1' => $asistenciasDia1,
            'asistencias2' => $asistenciasDia2,
            'asistencias3' => $asistenciasDia3,
        ]);
        
        // $users = User::where('asistencia', '1')->get();
        // return response()->json(['users' => $users]);
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
        //
        try {
            $request->validate([
                'nombres' => 'required|string|max:30',
                'apellidos' => 'required|string|max:30',
                'dni' => 'required|string|min:7|max:15',
                'provincia' => 'required',
                'empresa' => 'required',
                
            ], [
                'nombres.required' => 'El campo nombres es obligatorio.',
                'nombres.max' => 'El campo nombres no debe exceder los 30 caracteres.',
                'apellidos.required' => 'El campo apellidos es obligatorio.',
                'apellidos.max' => 'El campo apellidos no debe exceder los 30 caracteres.',
                'dni.required' => 'El campo DNI es obligatorio.',
                'dni.min' => 'El campo DNI debe tener mínimo 7 caracteres.',
                'dni.max' => 'El campo DNI debe tener máximo 15 caracteres.',
                // 'dni.unique' => 'El DNI ya está en uso.',
                'provincia.required' => 'El campo provincia es obligatorio.',
                'empresa.required' => 'El campo empresa es obligatorio.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }   
        
        $usuarioExistente = User::where('dni', $request->dni)->first();

        if ($usuarioExistente) {
            return response()->json(['message' => 'El DNI ya está en uso', 'id'=> $usuarioExistente->id], 409);
        }

        $user = User::create([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'dni' => $request->dni,
            'provincia' => $request->provincia,
            'empresa' => $request->empresa,
            'rubro' => $request->rubro,
            'cargo' => $request->cargo,
            'rol' => $request->rol ?? 'user',
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Persona registrada correctamente', 'id' => $user->id], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $user = User::find($id);

        return response()->json($user);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function asistencia(string $dni)
    {
        $user = User::where('dni', $dni)->first();

        if ($user) {
            // if ($user->asistencia == 1) {
            //     return response()->json(['message' => 'Usuario ya fue registrado']);
            // }
            $today = Carbon::today();

            // $asistenciaHoy = Asistencia::where('user_id', $user->id)
            // ->whereDate('created_at', $today)
            // ->first();

            // if ($asistenciaHoy) {
            //     return response()->json(['message' => 'Asistencia ya registrada hoy', 'user' => $user]);
            // }

            Asistencia::create([
                'user_id' => $user->id,
            ]);

            $user->update(['asistencia' => 1]);
            return response()->json(['message' => 'Asistencia registrada correctamente', 'user' => $user]);
        } else {
            return response()->json(['message' => 'Persona no encontrada']);
        }
    }
    public function encontrar(string $dni)
    {
        $user = User::where('dni', $dni)->first();

        if ($user) {
            return response()->json(['message' => 'Persona encontrada', 'user' => $user]);
        } else {
            return response()->json(['message' => 'Persona no encontrada']);
        }
    }

    public function list()
    {
        $asistencias = Asistencia::with('user')->get();
        return response()->json(['asistencias' => $asistencias]);
        // $users = User::where('asistencia','1');
        // return response()->json(['users' => $users]);
    }

    
}
