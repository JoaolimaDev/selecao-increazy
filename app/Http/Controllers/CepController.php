<?php

namespace App\Http\Controllers;


class CepController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/search/local/{cep}",
     *     summary="Consulta de múltiplos endereços.",
     *     description="Retorna informações de endereço para os CEP(s) fornecidos. Aceita vários CEPs separados por vírgulas.",
     *     tags={"Cep-API"},
     *     @OA\Parameter(
     *         name="cep",
     *         in="path",
     *         description="Um único ou mútiplos ceps.",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="01001000,17560-246"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sucesso!",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="cep", type="string", example="01001-000"),
     *                 @OA\Property(property="logradouro", type="string", example="Praça da Sé"),
     *                 @OA\Property(property="bairro", type="string", example="Sé"),
     *                 @OA\Property(property="localidade", type="string", example="São Paulo"),
     *                 @OA\Property(property="uf", type="string", example="SP"),
     *                 @OA\Property(property="unidade", type="string", example=""),
     *                 @OA\Property(property="ibge", type="string", example="3550308"),
     *                 @OA\Property(property="gia", type="string", example=""),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Formato de CEP inválido!",
     *         @OA\JsonContent(
     *             @OA\Property(property="Mensagem", type="string", example="Formato de CEP inválido! Os CEPs devem conter 8 dígitos contínuos ou cinco dígitos contínuos separados por - seguidos de três outros dígitos.")
     *         )
     *     ),
     *     security={
     *         {"jwt": {}}
     *     }
     * )
     */
    public function index(...$cep)
    {

        $cepArray = explode(",", implode($cep));
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 

        $resultArray = array();
        foreach ($cepArray as $value) {

            if (!preg_match('/^(\d{8}|\d{5}-\d{3})$/', trim($value))) {
                return response()->json(["Mensagem" =>
                "Formato de CEP inválido! Os CEPs devem conter 8 dígitos contínuos 
                ou cinco dígitos contínuos separados por - seguidos de três outros dígitos."],
                400);
                break;
            }

            $valByCep = trim($value);
            $url = "https://viacep.com.br/ws/{$valByCep}/json/";
            curl_setopt($curl, CURLOPT_URL, $url);
            $response = curl_exec($curl);
            $decodedResponse = json_decode($response, true);

            if (empty($decodedResponse) || isset($decodedResponse['erro']) && $decodedResponse['erro']) {
                $resultArray[] = ["mensagem" => 
                "Não foi encontrado nenhum registro para o CEP: {$valByCep} fornecido."];
                continue;
            }

            $resultArray[] = $decodedResponse;
        }

        curl_close($curl);
        return response()->json($resultArray, 200);
    }

}
