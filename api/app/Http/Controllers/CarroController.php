<?php

namespace App\Http\Controllers;

use App\Models\Carro;
use Illuminate\Http\Request;
use App\Repositories\CarroRepository;

class CarroController extends Controller
{
    public function __construct(Carro $carro) {
        $this->carro = $carro;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $carroRepository = new CarroRepository($this->carro);

        if($request->has('atributos_modelo')) {
            
            $atributos_modelo = 'modelos:id,'.$request->atributos_modelo;            

            $carroRepository->selectAtributosRegistrosRelacionados($atributos_modelo);

        } else {

            $carroRepository->selectAtributosRegistrosRelacionados('modelo');

        }

        if($request->has('filtro')) {
            $carroRepository->filtro($request->filtro);
        }

        if($request->has('atributos')) {

            $carroRepository->selectAtributos($request->atributos);

        }

        
        return response()->json($carroRepository->getResultado(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate($this->carro->rules());

        $carro = $this->marca->create([
            'modelo_id' => $request->modelo_id,
            'placa' => $request->placa,
            'disponivel' => $request->disponivel,
            'km' => $request->km
        ]);

        return response()->json($marca, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $carro = $this->carro->with('modelo')->find($id);

        if($carro === null) {
            return response()->json(['erro' => 'Carro não encontrada!'], 404);
        }

        return response()->json($carro, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $carro = $this->carro->find($id);

        if($carro === null) {
            return response()->json(['erro' => 'Impossivel realizar atualização, o recurso não existe!'], 404);
        }

        if($request->method() === 'PATCH') {

            $regrasDinamicas = array();

            foreach($carro->rules() as $input => $regra) {

                if(array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas);

        } else {

            $request->validate($carro->rules());

        }

        $marca->fill($request->all());
        $marca->save();

        return response()->json($carro, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Carro  $carro
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $carro = $this->carro->find($id);

        if($carro === null) {
            return response()->json(['erro' => 'Impossivel realizar exclusão, o recurso não existe!'], 404);
        }

        $carro->delete();

        return response()->json(['msg' => 'O carro foi removido com sucesso!'], 200);
    }
}