<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;

class FacturacionService
{
    protected $cliente;

    protected $url;

    protected $headers;

    public function __construct()
    {
        if(empty(config('facturacom.' . env('FACTURACOM_ENVIRONMENT')))){
            throw new \Exception("Debes definr las variables de entorno para facturacion", 1);
        }


        $this->url = config('facturacom.' . env('FACTURACOM_ENVIRONMENT') . '.url') . "";

        $this->headers = array(
            "Content-Type"  => "application/json",
            "F-PLUGIN"      => '9d4095c8f7ed5785cb14c0e3b033eeb8252416ed',
            "F-Api-Key"     => config('facturacom.' . env('FACTURACOM_ENVIRONMENT') . '.api-key'),
            "F-Secret-Key"  => config('facturacom.' . env('FACTURACOM_ENVIRONMENT') . '.secret-key')
        );

        $this->cliente = new Client(['base_uri' => $this->url]);
    }

    public function usosCfdi()
    {

        $response = $this->cliente->get($this->url . '/api/v3/catalogo/UsoCfdi', [
            'headers' => $this->headers,
        ]);

        $collection = $this->buildCollection($response->getBody());

        return $collection;
    }

    public function metodoPago()
    {
        $response = $this->cliente->get($this->url . '/api/v3/catalogo/MetodoPago', [
            'headers' => $this->headers,
        ]);

        $collection = $this->buildCollection($response->getBody());

        return $collection;
    }

    public function formaPago()
    {
        $response = $this->cliente->get($this->url . '/api/v3/catalogo/FormaPago', [
            'headers' => $this->headers,
        ]);

        $collection =  $this->buildCollection($response->getBody());

        return $collection;
    }

    public function claveUnidad()
    {
        $response = $this->cliente->get($this->url . '/api/v3/catalogo/ClaveUnidad', [
            'headers' => $this->headers,
        ]);

        $collection  =  $this->buildCollection($response->getBody());

        return $collection;
    }

    public function getInfoCliente(string $rfc)
    {
        $client = new Client(['base_uri' => $this->url]);

        $response = $client->get("{$this->url}/api/v1/clients/{$rfc}", [
            'headers' => $this->headers,
        ]);

        if(json_decode($response->getBody())->status == 'error' ){
            return [
                'success' => false,
                'message' => 'El cliente no existe'
            ];
        }

        $cliente_rfc = json_decode($response->getBody())->Data;
        $UID = $cliente_rfc->UID;

        return [
            'success' => true,
            'data'    => [
                'uid' => $UID
            ]
        ];
    }

    public function crearCliente(array $fields)
    {
        $ch = curl_init();

        $jsonfield = json_encode($fields);

        curl_setopt($ch, CURLOPT_URL, $this->url . "/api/v1/clients/create");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonfield);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "F-PLUGIN: " . '9d4095c8f7ed5785cb14c0e3b033eeb8252416ed',
            "F-Api-Key: " . config('facturacom.' . env('FACTURACOM_ENVIRONMENT') . '.api-key'),
            "F-Secret-Key: " . config('facturacom.' . env('FACTURACOM_ENVIRONMENT') . '.secret-key'),
        ));


        $curl_crear_cliente = curl_exec($ch);
        $response = json_decode($curl_crear_cliente);

        # CAPTURAR ERROR AL TRATAR DE CREAR EL CLIENTE
        if ($response->status == 'error') {
            if (is_object($response->message)) {
                $error = '';

                foreach ($response->message as $key => $message) {
                    $error .= $message[0];
                }

                return [
                    'success'  => false,
                    'error'    => $error,
                ];
            } else {
                return [
                    'success'  => false,
                    'error'    => $response->message,
                ];
            }
        }

        $UID = json_decode($curl_crear_cliente)->Data->UID;

        curl_close($ch);

        return [
            'success'   => true,
            'data'      => [
                'uid' => $UID
            ]
        ];
    }

    public function timbrar(array $fields)
    {
        $ch = curl_init();

        $jsonfield = json_encode($fields);

        curl_setopt($ch, CURLOPT_URL, $this->url . "/api/v3/cfdi33/create");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonfield);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "F-PLUGIN: " . '9d4095c8f7ed5785cb14c0e3b033eeb8252416ed',
            "F-Api-Key: " . config('facturacom.' . env('FACTURACOM_ENVIRONMENT') . '.api-key'),
            "F-Secret-Key: " . config('facturacom.' . env('FACTURACOM_ENVIRONMENT') . '.secret-key'),
        ));

        $request_timbrado = curl_exec($ch);
        $response = json_decode($request_timbrado);

        $uuids = [
            'uid'   => '',
            'uuid'  => '',
        ];

        if ($response->response == 'error') {
            if (is_object($response->message)) {
                $error = '';

                foreach ($response->message as $key => $message) {
                    $error .= $message;
                }

                return [
                    'success' => false,
                    'message' => $error
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $response->message
                ];
            }
        } else {
            $uuids = [
                'uid'   => $response->uid,
                'uuid'  => $response->UUID,
            ];
        }

        curl_close($ch);

        return [
            'success'   => true,
            'data'      => $uuids
        ];
    }

    public function descargar(string $uid,string $filename, string $tipo)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->url . "/api/v3/cfdi33/{$uid}/{$tipo}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "F-PLUGIN: " . '9d4095c8f7ed5785cb14c0e3b033eeb8252416ed',
            "F-Api-Key: " . config('facturacom.' . env('FACTURACOM_ENVIRONMENT') . '.api-key'),
            "F-Secret-Key: " . config('facturacom.' . env('FACTURACOM_ENVIRONMENT') . '.secret-key'),
        ));

        $response = curl_exec($ch);

        curl_close($ch);

        if ($tipo == 'pdf') {
            header('Content-Type: application/pdf');
            header("Content-Transfer-Encoding: Binary");
            header('Content-disposition: attachment; filename="' . $filename . '.pdf"');
        } else {
            header('Content-disposition: attachment; filename="' . $filename . '.xml"');
            header('Content-type: "text/xml"; charset="utf8"');
        }

        return $response;
    }

    public function cancelacion(string $uid)
    {
        $client = new Client(['base_uri' => $this->url]);

        $request = $client->get("{$this->url}/api/v3/cfdi33/{$uid}/cancel", [
            'headers' => array(
                "Content-Type"  => "application/json",
                "F-PLUGIN"      => '9d4095c8f7ed5785cb14c0e3b033eeb8252416ed',
                "F-Api-Key"     => config('facturacom.' . env('FACTURACOM_ENVIRONMENT') . '.api-key'),
                "F-Secret-Key"  => config('facturacom.' . env('FACTURACOM_ENVIRONMENT') . '.secret-key')
            )
        ]);

        $result = json_decode($request->getBody());

        return [
            'success' => true,
            'message' => 'Factura cancelada correctamente',
            'result'  => $result
        ];
    }

    private function buildCollection($response)
    {
        $data = json_decode($response)->data;
        $collection = new Collection($data);

        $collection = $collection->mapWithKeys(function ($item) {
            return [$item->key => $item->key . ' - ' . $item->name];
        });

        return $collection;
    }
}
