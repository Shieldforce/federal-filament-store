<?php

namespace Shieldforce\FederalFilamentStore\Services;

use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;
use Shieldforce\FederalFilamentStore\Enums\TypePeopleEnum;

class ApiCpfCnpjService
{
    public function __construct(
        public string  $cpfOrCnpj,
        public ?string $birthday = null
    ) {}

    public function search()
    {
        $this->clearCpfCnpj();

        $apiBaseUrl = env('API_CPF_CNPJ_URL');
        $apiToken   = env('API_CPF_CNPJ_TOKEN');

        $endpoint = "/api/cnpj/search?cnpj={$this->cpfOrCnpj}";

        if (strlen($this->cpfOrCnpj) == 11) {
            $birthday = Carbon::parse($this->birthday)->format('d-m-Y');
            $endpoint = "/api/cpf/search?numeroDeCpf={$this->cpfOrCnpj}&dataNascimento={$birthday}";
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL            => "{$apiBaseUrl}{$endpoint}&token={$apiToken}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_HTTPHEADER     => array(
                'Content-Type: application/json',
                'Accept: application/json'
            ),
        ));

        $response = curl_exec($curl);

        if ($response === false) {
            logger()->error("Erro cURL: " . curl_error($curl));
        }

        curl_close($curl);
        return json_decode($response, true);
    }

    public function clearCpfCnpj()
    {
        $this->cpfOrCnpj = preg_replace('/\D/', '', $this->cpfOrCnpj);
        return $this->cpfOrCnpj;
    }

    public function setValues(Get $get, Set $set)
    {
        $apiCpfCnpj = new ApiCpfCnpjService($get("document"), $get("birthday"));
        $cpfCnpj    = $apiCpfCnpj->clearCpfCnpj();

        if (
            $get("people_type") &&
            $get("people_type") == TypePeopleEnum::F->value &&
            $cpfCnpj &&
            strlen($cpfCnpj) == 11
        ) {
            $data = $apiCpfCnpj->search();
        }

        if (
            $get("people_type") &&
            $get("people_type") == TypePeopleEnum::J->value &&
            $cpfCnpj &&
            strlen($cpfCnpj) == 14
        ) {
            $data = $apiCpfCnpj->search();
        }

        if (isset($data["data"]) && isset($data["data"]["nome_da_pf"])) {
            $set('name', $data["data"]["nome_da_pf"]);
        }

        if (isset($data["data"]) && isset($data["data"]["fantasia"])) {
            $set('name', $data["data"]["fantasia"]);
            $set('email', $data["data"]["email"] ?? null);
        }

        if (isset($data["data"]) && isset($data["data"]["cep"])) {
            Notification::make()
                ->title('Achamos mais dados deste cliente!')
                ->body('Achamos dados de endereço, cadastraremos assim que clicar em criar!')
                ->success()
                ->seconds(30)
                ->send();

        }

        if (isset($data["data"]) && isset($data["data"]["telefone"])) {
            Notification::make()
                ->title('Achamos mais dados deste cliente!')
                ->body('Achamos dados de contato, cadastraremos assim que clicar em criar!')
                ->success()
                ->seconds(40)
                ->send();

        }
    }

}
