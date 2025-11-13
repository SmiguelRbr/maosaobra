<?php

namespace App\Http\Controllers\api\auth;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Orcamento;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string|unique:users',
            'password' => 'required|string|confirmed|min:8',
            'birthDate' => 'required|date',
            'cpf' => 'nullable|string|unique:users,cpf',
            'cnpj' => 'nullable|string|unique:users,cnpj',
            'password_confirmation' => 'required|string'
        ], [
            'name.required' => 'Nome é obrigatório',
            'email.required' => 'Email é obrigatório',
            'phone.required' => 'Telefone é obrigatório',
            'password.required' => 'Senha é obrigatório',
            'password_confirmation.required' => 'Confirme sua senha',
            'birthDate.required' => 'Coloque sua data de nascimento',

            'email.unique' => 'Email já cadastrado',
            'phone.unique' => 'Telefone já cadastrado',
            'cpf.unique' => 'CPF já cadastrado',
            'cnpj.unique' => 'CNPJ já cadastrado',

            'confirmed' => 'Senhas não conferem',
            'password.min' => 'A senha deve conter no mínimo 8 carácteres',
            'date' => 'formato de data errado',
            'email.email' => 'Email inválido'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'password' => bcrypt($request->password),
            'email' => $request->email,
            'phone' => $request->phone,
            'cpf' => $request->cpf ?? null,
            'birthDate' => $request->birthDate,
            'cnpj' => $request->cnpj ?? null,
            'role' => $request->role ?? 'cliente', // caso role não venha, define 'user' como padrão
            
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Usuario cadastrado com sucesso',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'Email é obrigatório',
            'password.required' => 'Senha é obrigatório',
            'email.email' => 'Email inválido'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $credenciais = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credenciais)) {
            return response()->json([
                'error' => 'credenciais inválidas'
            ], 401);
        }

        return response()->json([
            'message' => 'Usuario logado com sucesso',
            'token' => $token
        ]);
    }

    public function updateRole(Request $request, User $user)
    {
        $user->update([
            'role' => $request->role,
        ]);

        return response()->json([
            'message' => 'Role editada com sucesso',
            'role' => $user->role
        ], 200);
    }

    public function cadastroPrestador(Request $request, User $user)
    {
    
        $validator = Validator::make($request->all(), [
            'experiencia' => 'required|string|max:1000',
            'imagePath'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'experiencia.required' => 'Você precisa informar a experiência.',
            'experiencia.string'   => 'A experiência deve ser um texto.',
            'experiencia.max'      => 'A experiência deve ter no máximo :max caracteres.',

            'imagePath.image'      => 'O arquivo enviado deve ser uma imagem.',
            'imagePath.mimes'      => 'A imagem deve estar no formato: jpeg, png, jpg, gif ou webp.',
            'imagePath.max'        => 'A imagem não pode ter mais que :max kilobytes.',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user->experiencia = $request->experiencia;

        if ($request->hasFile('imagePath')) {
            $path = $request->file('imagePath')->store('users', 'public');
            $user->imagePath = $path;
        }

        $user->save();

        return response()->json([
            'message' => 'Imagem e experiência atualizadas com sucesso',
            'user'    => $user,
        ], 200);
    }


    public function logout()
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json([
                'message' => 'Usuaro não encontrado',
            ], 403);
        }

        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'message' => 'Usuario deslogado com sucesso',
        ], 200);
    }

    public function refresh()
    {
        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json([
                'message' => 'Usuaro não encontrado',
            ], 403);
        }

        $newToken = JWTAuth::refresh(JWTAuth::getToken());

        return response()->json([
            'token' => $newToken
        ], 200);
    }

    public function cadastrarEndereco(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'zipCode' => 'required|max:8',
            'state' => 'required|max:2',
            'city' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'street' => 'required|string|max:150',
            'number' => 'required|string|max:10',
            'complement' => 'nullable|string|max:100',
        ], [
            'zipCode.required' => 'CEP é obrigatório',
            'state.required' => 'Estado é obrigatório',
            'city.required' => 'Cidade é obrigatória',
            'district.required' => 'Bairro é obrigatório',
            'street.required' => 'Rua é obrigatória',
            'number.required' => 'Número é obrigatório',
            'max' => 'Campo excedeu o tamanho permitido'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }


        $user = Auth::user();

        $address = Address::create([
            'zipCode' => $request->zipCode,
            'state' => $request->state,
            'city' => $request->city,
            'district' => $request->district,
            'street' => $request->street,
            'number' => $request->number,
            'complement' => $request->complement ?? null,
            'user_id' => $user->id
        ]);

        return response()->json([
            'message' => 'Endereço cadastrado com sucesso',
            'address' => $address
        ], 201);
    }

    public function getByUser()
    {
        $user = Auth::user();
        $enderecos = $user->enderecos;

        return response()->json([
            'message' => 'Endereços recuperados com sucesso',
            'enderecos' => $enderecos
        ], 200);
    }


    public function updateEspecialidade(Request $request, User $user)
    {
        $request->validate([
            'especialidade' => 'required|array',
            'especialidade.*' => 'exists:especialidades,id'
        ]);

        $user->especialidades()->sync($request->especialidade);

        return response()->json([
            'message' => 'Especialidades atualizadas com sucesso',
            'specialties' => $user->especialidades
        ]);
    }
}
