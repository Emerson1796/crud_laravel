<?php

namespace App\Http\Controllers;

use App\Cadastro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;


class CadastroController extends Controller
{
    public function index()
    {
        $curriculos = Cadastro::get();
        return response()->json($curriculos);
    }

    public function show($id)
    {
        $curriculos = Cadastro::find($id);

        if(!$curriculos) {
            return response()->json([
                'message'   => 'Currículo não encontrado',
            ], 404);
        }

        return response()->json($curriculos);
    }

    Public function destroy($id)
    {
        $curriculos = Cadastro::find($id);
        if(isset($curriculos->id)){
            $curriculos->delete();
            return response()->json($curriculos, 200);
        }
        else{
            return response()->json([
                'message'   => 'Currículo não encontrado',
            ], 404);
        }
    }

    protected function curriculoValidator($request) {
        $validator = validator()->make($request->all(), [
            'nome' => 'required',
            'email' => 'required',
            'telefone' => 'required',
            'endereco' => 'required',
            'curriculo' => 'required|mimes:pdf,doc,docx,txt|max:50000'

        ]);

        return $validator;
    }

    public function store(Request $request)
    {
        $validator = $this->curriculoValidator($request);
        $email = request('email');

        if (filter_var($email, FILTER_VALIDATE_EMAIL) || $validator->fails()) {
            return response()->json([
                'message' => 'Verifique todos os campos',
                'errors' => $validator->errors()
            ], 422);
        }

        if($request->hasFile('curriculo') && $request->file('curriculo')->isValid()) {

            $name = uniqid(date('Y-m-d_H-i-s_'));
            $nameFileCurriculo = public_path('curriculos\\') . $name . "-" . $request->curriculo->getClientOriginalName();
            $request->file('curriculo')->storeAs('curriculos', $name . "-" . $request->curriculo->getClientOriginalName(), 'public');

        }else{
            $nameFileCurriculo = "null";
        }

        $cadastro = new Cadastro();
        $cadastro->nome = request('nome');
        $cadastro->email = $email;
        $cadastro->telefone = request('telefone');
        $cadastro->endereco = request('endereco');
        $cadastro->curriculo = $nameFileCurriculo;
        $cadastro->IP = $_SERVER['REMOTE_ADDR'];
        $cadastro->save();

        return response()->json($cadastro, 201);
    }

    public function update(Request $request, $id)
    {
        $validator = $this->curriculoValidator($request);

        $curriculos = Cadastro::find($id);
        $email = request('email');

        if(!$curriculos) {
            return response()->json([
                'message'   => 'Currículo não encontrado',
            ], 404);
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL) || $validator->fails()) {
            return response()->json([
                'message' => 'Verifique todos os campos',
                'errors' => $validator->errors()
            ], 422);
        }

        if($request->hasFile('curriculo') && $request->file('curriculo')->isValid()) {
            $nameFileCurriculo = storage_path() . $request->curriculo->getClientOriginalName();
            $request->file('photo')->store('curriculos', $request->curriculo->getClientOriginalName());
        }else{
            $nameFileCurriculo = "null";
        }

        $curriculos->nome = request('nome');
        $curriculos->email = $email;
        $curriculos->telefone = request('telefone');
        $curriculos->endereco = request('endereco');
        $curriculos->curriculo = $nameFileCurriculo;
        $curriculos->IP = $_SERVER['REMOTE_ADDR'];

        $curriculos->save();

        return response()->json($curriculos);
    }
}
