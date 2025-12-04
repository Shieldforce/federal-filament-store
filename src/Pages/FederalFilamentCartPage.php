<?php

namespace Shieldforce\FederalFilamentStore\Pages;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Shieldforce\FederalFilamentStore\Models\Cart;
use Shieldforce\FederalFilamentStore\Services\BuscarViaCepService;

class FederalFilamentCartPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string  $view                  = 'federal-filament-store::pages.cart';
    protected static ?string $navigationIcon        = 'heroicon-o-list-bullet';
    protected static ?string $navigationGroup       = 'Loja';
    protected static ?string $label                 = 'Carrinho';
    protected static ?string $navigationLabel       = 'Carrinho';
    protected static ?string $title                 = 'Aqui estão seus produtos do carrinho!';
    protected array          $result                = [];
    public string            $name                  = "";
    public string            $email                 = "";
    public string            $password              = "";
    public string            $password_confirmation = "";
    public string            $cellphone             = "";
    public string            $zipcode               = "";
    public string            $street                = "";
    public string            $number                = "";
    public ?string           $complement            = null;
    public string            $district              = "";
    public string            $city                  = "";
    public string            $state                 = "";
    public bool              $is_user               = false;
    protected array          $items                 = [];
    protected Cart           $cart;
    public float             $totalPrice;

    public function getLayout(): string
    {
        if (request()->query('external') === '1') {
            return 'federal-filament-store::layouts.external';
        }

        return parent::getLayout();
    }

    public static function getSlug(): string
    {
        return 'external-ffs-cart';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getNavigationGroup(): ?string
    {
        return config()->get('federal-filament-store.sidebar_group');
    }

    public function mount(): void
    {
        if (!Auth::check()) {
            filament()
                ->getCurrentPanel()
                ->topNavigation()/*
                ->topbar(false)*/
            ;
        }

        $this->loadData();
    }

    public function loadData()
    {
        $this->cart = Cart::where("identifier", request()->cookie("ffs_identifier"))
                          ->first();

        $this->items = json_decode($this->cart->items ?? [], true);

        $this->totalPrice = collect($this->items)->sum(function ($item) {
            return $item['price'] * $item['amount'];
        });
    }

    public function updated($property)
    {
        $this->loadData();
    }

    public function submit()
    {
        $data = $this->form->getState();

        $userCallback = config('federal-filament-store.user_callback');
        $useModel = new $userCallback();

        if (!$data["is_user"]) {
            $this->notAccount($useModel);
        }

        $credentials = Auth::attempt([
            "email"    => $data["email"],
            "password" => $data["password"],
        ]);

        if (!$credentials && $data["is_user"]) {
            return Notification::make()
                               ->danger()
                               ->title('Credenciais Incorretas!')
                               ->body("E-mail ou senha incorretos, por favor verifique e tente novamente.")
                               ->send();
        }

        //$user = $user->find(Auth::id());

        /*$transactionCallback = config('federal-filament-store.transaction_callback');
        $transaction = new $transactionCallback();
        $transactionModel = $transaction->updateOrCreate([], [
            'creator_id'         => "",
            'order_id'           => "",
            'employee_id'        => "",
            'supplier_id'        => "",
            'origin_payment'     => "",
            'name'               => "",
            'necessary'          => "",
            'type'               => "",
            'value'              => "",
            'monthly'            => "",
            'date_monthly_start' => "",
            'date_monthly_end'   => "",
            'booklet'            => "",
            'not_start_end'      => "",
            'reference'          => "",
            'due_day'            => "",
            'payment_day'        => "",
            'paid'               => "",
            'recipient'          => "",
            'status'             => "",
        ]);*/

        /*
            $orderCallback = config('federal-filament-store.client_callback');
            $order = new $orderCallback();
            $orderModel = $order->updateOrCreate([], []);
        */

        /*$due_date = Carbon::createFromFormat(
            "d/m/Y",
            "{$transactionModel->due_day}/{$transactionModel->reference}"
        )->format("Y-m-d");

        $mountCheckout = new MountCheckoutStepsService(
            model   : $transactionModel, requiredMethods: [
            MethodPaymentEnum::credit_card->value,
            MethodPaymentEnum::pix->value,
            MethodPaymentEnum::billet->value,
        ],  due_date: $due_date,
        );*/
        /*$mountCheckout->handle()->configureButtonSubmit(
            text       : "Dashboard",
            color      : "info",
            urlRedirect: route("filament.admin.pages.dashboard")
        )->step1(
            items  : array_map(callback: function (
                $product
            ) {
                return (new DtoStep1(
                    name       : $product["name"],
                    price      : $product["pivot"]["price"],
                    price_2    : $product["pivot"]["price"],
                    price_3    : $product["pivot"]["price"],
                    description: "Venda de produto: ".$product["name"],
                    img        : $product["picture"],
                    quantity   : $product["pivot"]["quantity"],
                ))->toArray();
            },
                               array   : $transactionModel->order->products->toArray()),
            visible: true,
        );*/
    }

    public function notAccount(Model $model)
    {
        $data = $this->form->getState();

        dd($data);
        /*$model->updateOrCreate(["email" => $data["email"]], [
            "name"     => $data["name"],
            "password" => bcrypt($data["password"]),
        ]);*/
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(1)
                ->schema([
                    Toggle::make("is_user")
                          ->label("Já tenho conta")
                          ->default(false)
                          ->live(),

                    Fieldset::make("is_user_not")
                            ->label("Dados de cadastro")
                            ->visible(fn(Get $get) => !$get("is_user"))
                            ->schema([
                                TextInput::make('name')
                                         ->label('Nome completo')
                                         ->rule(function (Get $get) {
                                             return function (string $attribute, $value, $fail) use ($get) {
                                                 $explode = explode(" ", $value);
                                                 if (count($explode) < 2) {
                                                     $this->loadData();

                                                     $fail("Nome completo deve ter pelo menos 2 palavras!");
                                                 }
                                             };
                                         })
                                         ->required(),

                                TextInput::make('email')
                                         ->label('E-mail')
                                         ->email()
                                         ->required(),

                                TextInput::make('password')
                                         ->label('Senha')
                                         ->password()
                                         ->minLength(4)
                                         ->maxLength(50)
                                         ->revealable()
                                         ->required(),

                                TextInput::make('password_confirmation')
                                         ->label('Confirme a Senha')
                                         ->password()
                                         ->minLength(4)
                                         ->maxLength(50)
                                         ->revealable()
                                         ->rule(function (Get $get) {
                                             return function (string $attribute, $value, $fail) use ($get) {
                                                 $password = $get("password");
                                                 if ($password != $value) {
                                                     $this->loadData();
                                                     $fail("Confirmação de Senha Incorreta!");
                                                 }
                                             };
                                         })
                                         ->required(),

                                TextInput::make('cellphone')
                                         ->label('Celular/Whatsapp')
                                         ->prefixIcon("heroicon-o-phone")
                                         ->mask("(99) 9999-9999")
                                         ->required(),

                                TextInput::make('zipcode')
                                         ->label("Digite o CEP")
                                         ->dehydrateStateUsing(fn($state) => preg_replace('/\D/', '', $state))
                                         ->suffixAction(Action::make('viaCep')
                                                              ->label("Buscar CEP")
                                                              ->icon('heroicon-m-map-pin')
                                                              ->action(function (
                                                                  Set       $set,
                                                                            $state,
                                                                  Get       $get,
                                                                  Component $livewire
                                                              ) {
                                                                  $data = BuscarViaCepService::getData((string)$state);

                                                                  if (isset($data["cep"])) {
                                                                      $set('street', $data["logradouro"]);
                                                                      $set('complement', $data["complemento"]);
                                                                      $set('district', $data["bairro"]);
                                                                      $set('city', $data["localidade"]);
                                                                      $set('state', $data["uf"]);
                                                                  }
                                                              }))
                                         ->hint("Busca de CEP")
                                         ->afterStateUpdated(function (Set $set, Get $get, Component $livewire) {
                                             $data = BuscarViaCepService::getData((string)$get("zipcode"));

                                             if (isset($data["cep"])) {
                                                 $set('street', $data["logradouro"]);
                                                 $set('complement', $data["complemento"]);
                                                 $set('district', $data["bairro"]);
                                                 $set('city', $data["localidade"]);
                                                 $set('state', $data["uf"]);
                                             }
                                         })
                                         ->mask(function (Get $get) {
                                             return "99999-999";
                                         })
                                         ->debounce(1000)
                                         ->required(),

                            ]),

                    Fieldset::make("address")
                            ->label("Dados de endereço")
                            ->visible(function (Get $get) {
                                return Str::length($get("zipcode")) == 9 && !$get("is_user");
                            })
                            ->schema([

                                TextInput::make('street')
                                         ->label('Logradouro')
                                         ->required()
                                         ->maxLength(255),

                                TextInput::make('number')
                                         ->label('Número')
                                         ->maxLength(20),

                                TextInput::make('complement')
                                         ->label('Complemento')
                                         ->maxLength(255),

                                TextInput::make('district')
                                         ->label('Bairro')
                                         ->maxLength(255),

                                TextInput::make('city')
                                         ->label('Cidade')
                                         ->required()
                                         ->maxLength(255),

                                TextInput::make('state')
                                         ->label('UF')
                                         ->required()
                                         ->maxLength(2),

                            ]),

                    Fieldset::make("is_user_yes")
                            ->label("Dados de acesso")
                            ->visible(fn(Get $get) => $get("is_user"))
                            ->schema([
                                TextInput::make('email')
                                         ->label('E-mail')
                                         ->email()
                                         ->required(),

                                TextInput::make('password')
                                         ->label('Senha')
                                         ->revealable()
                                         ->password()
                                         ->required(),

                            ]),
                ]),
        ];
    }

}
