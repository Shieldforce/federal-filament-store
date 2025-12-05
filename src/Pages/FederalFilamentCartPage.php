<?php

namespace Shieldforce\FederalFilamentStore\Pages;

use App\Enums\StatusOrderEnum;
use App\Enums\StatusTransactionEnum;
use App\Enums\TypeOrderEnum;
use App\Enums\TypeTransactionEnum;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;
use Shieldforce\CheckoutPayment\Enums\MethodPaymentEnum;
use Shieldforce\CheckoutPayment\Services\DtoSteps\DtoStep1;
use Shieldforce\CheckoutPayment\Services\MountCheckoutStepsService;
use Shieldforce\FederalFilamentStore\Enums\StatusClientEnum;
use Shieldforce\FederalFilamentStore\Enums\TypeContractEnum;
use Shieldforce\FederalFilamentStore\Enums\TypePeopleEnum;
use Shieldforce\FederalFilamentStore\Models\Cart;
use Shieldforce\FederalFilamentStore\Services\BuscarViaCepService;
use Throwable;

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
    public int               $people_type           = 1;
    public string            $document              = "";
    public string            $birthday              = "";
    public string            $name                  = "";
    public string            $email                 = "";
    public string            $password              = "";
    public string            $password_confirmation = "";
    public string            $cellphone             = "";
    public string            $zipcode               = "";
    public string            $street                = "";
    public string            $number                = "s/n";
    public ?string           $complement            = null;
    public string            $district              = "";
    public string            $city                  = "";
    public string            $state                 = "";
    public bool              $is_user               = false;
    protected array          $items                 = [];
    public ?int              $cart_id               = null;
    protected Cart           $cart;
    public float             $totalPrice;

    public
    function getLayout(): string
    {
        if (request()->query('external') === '1') {
            return 'federal-filament-store::layouts.external';
        }

        return parent::getLayout();
    }

    public static
    function getSlug(): string
    {
        return 'external-ffs-cart';
    }

    public static
    function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static
    function getNavigationGroup(): ?string
    {
        return config()->get('federal-filament-store.sidebar_group');
    }

    public
    function mount(): void
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

    public
    function loadData()
    {
        $this->cart = Cart::where("identifier", request()->cookie("ffs_identifier"))
                          ->first();

        $this->items = json_decode($this->cart->items ?? [], true);

        $this->totalPrice = collect($this->items)->sum(
            function ($item) {
                return $item['price'] * $item['amount'];
            }
        );

        $this->cart_id = $this->cart->id;
    }

    public
    function updated(
        $property
    ) {
        $this->loadData();
    }

    public
    function submit()
    {
        DB::beginTransaction();

        try {
            $data = $this->form->getState();

            $userCallback = config('federal-filament-store.user_callback');
            $useModel = new $userCallback();
            $isUser = $data["is_user"];

            if ($isUser) {
                $user = $this->isAccount($useModel);
            }

            if (!$isUser) {
                $user = $this->notAccount($useModel);
            }

            if (!isset($user->id)) {
                return Notification::make()
                                   ->danger()
                                   ->title('Erro ao criar usuário!')
                                   ->body("Houve um erro ao criar usuário!")
                                   ->persistent()
                                   ->send();
            }

            $client = $this->createOrExtractClient($user, $isUser);

            if (!isset($client->id)) {
                return Notification::make()
                                   ->danger()
                                   ->title('Conta sem cliente!')
                                   ->body("Esta conta não é do tipo cliente!")
                                   ->persistent()
                                   ->send();
            }

            $address = $this->createOrExtractAddress($client, $isUser);

            if (!isset($address->id)) {
                return Notification::make()
                                   ->danger()
                                   ->title('Erro ao criar Endereço')
                                   ->body("Endereço não foi criado!")
                                   ->persistent()
                                   ->send();
            }

            $contact = $this->createOrExtractContact($client, $isUser);

            if (!isset($contact->id)) {
                return Notification::make()
                                   ->danger()
                                   ->title('Erro ao criar Contato!')
                                   ->body("Contato não foi criado!")
                                   ->persistent()
                                   ->send();
            }

            $order = $this->createOrExtractOrder($client, $isUser);

            if (!isset($order->id)) {
                return Notification::make()
                                   ->danger()
                                   ->title('Pedido não criado!')
                                   ->body("Erro ao criar pedido!")
                                   ->persistent()
                                   ->send();
            }

            $transaction = $this->createOrExtractTransaction($order, $isUser);

            if (!isset($transaction->id)) {
                return Notification::make()
                                   ->danger()
                                   ->title('Transação não criado!')
                                   ->body("Erro ao criar transação!")
                                   ->persistent()
                                   ->send();
            }

            $this->processCheckout($transaction, $isUser);

            /*
            $credentials = Auth::attempt([
                "email"    => $data["email"],
                "password" => $data["password"],
            ]);
            */

            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();

            $this->loadData();

            Notification::make()
                        ->danger()
                        ->title('Erro ao criar conta!')
                        ->body($throwable->getMessage())
                        ->persistent()
                        ->send();

            throw $throwable;
        }
    }

    public
    function notAccount(
        Model $user
    ) {
        $data = $this->form->getState();

        $user = $user
            ->where('email', $data['email'])
            ->first();

        if ($user && !Hash::check($data['password'], $user->password)) {
            Notification::make()
                        ->danger()
                        ->title('Credenciais Incorretas!')
                        ->body("Você já possui uma conta! Mas as credenciais estão incorretas!.")
                        ->send();
            return null;
        }

        dd($user);

        return $user->updateOrCreate(
            ["email" => $data["email"]],
            [
                "name"     => $data["name"],
                "password" => bcrypt($data["password"]),
                "contact"  => $data["cellphone"],
            ]
        );
    }

    public
    function isAccount(
        Model $user
    ) {
        $data = $this->form->getState();

        $user = $user
            ->where('email', $data['email'])
            ->first();

        if (!$user || $user && !Hash::check($data['password'], $user->password)) {
            Notification::make()
                        ->danger()
                        ->title('Credenciais Incorretas!')
                        ->body("E-mail ou senha incorretos, por favor verifique e tente novamente.")
                        ->send();
            return null;
        }

        return $user;
    }

    public
    function createOrExtractClient(
        Model $user,
        bool  $isUser
    ) {
        $data = $this->form->getState();

        $client = $user->clients->first() ?? null;

        if (!isset($client->id) && $isUser) {
            return null;
        }

        if (isset($client->id)) {
            return $client;
        }

        return $user
            ->clients()
            ->updateOrCreate(
                ["email" => $data["email"]],
                [
                    'name'        => $data["name"],
                    'document'    => $data["document"],
                    'email'       => $data["email"],
                    'people_type' => $data["people_type"],
                    'status'      => StatusClientEnum::ativo->value,
                    'birthday'    => $data["birthday"],
                    'obs'         => "Criado pelo checkout da loja!",
                ]
            );
    }

    public
    function createOrExtractAddress(
        Model $client,
        bool  $isUser
    ) {
        $data = $this->form->getState();

        $address = $client
            ->addresses()
            ->where("main", 1)
            ->get()
            ->first() ?? null;

        if (!isset($address->id) && $isUser) {
            return null;
        }

        if (isset($address->id)) {
            return $address;
        }

        return $client
            ->addresses()
            ->updateOrCreate(
                [
                    "zipcode" => $data["zipcode"],
                ],
                [
                    "street"     => $data["street"],
                    "number"     => $data["number"],
                    "complement" => $data["complement"],
                    "district"   => $data["district"],
                    "city"       => $data["city"],
                    "state"      => $data["state"],
                    "main"       => 1
                ]
            );
    }

    public
    function createOrExtractContact(
        Model $client,
        bool  $isUser
    ) {
        $data = $this->form->getState();

        $contact = $client->contacts->first() ?? null;

        if (!isset($contact->id) && $isUser) {
            return null;
        }

        if (isset($contact->id)) {
            return $contact;
        }

        $cellphone = preg_replace('/\D/', '', $data["cellphone"]);
        $prefix = substr($cellphone, 0, 3);
        $number = substr($cellphone, 3);

        return $client
            ->contacts()
            ->updateOrCreate(
                [
                    'number' => $number,
                ],
                [
                    'prefix_international' => "55",
                    'prefix'               => $prefix,
                    'name'                 => "Pessoal",
                    'type'                 => "fixo",
                ]
            );
    }

    public
    function createOrExtractOrder(
        Model $client,
        bool  $isUser
    ) {
        $data = $this->form->getState();

        $date = now()->format("Y-m-d H");

        $order = $client
            ->orders()
            ->where("created_at", "like", "%$date%")
            ->get()
            ->first() ?? null;

        if (!isset($order->id) && $isUser) {
            return null;
        }

        $cart = Cart::find($data["cart_id"]);
        $items = json_decode($cart->items, true);

        $products = [];

        foreach ($items as $item) {
            $product = DB::table("products")
                         ->where("uuid", $item["uuid"])
                         ->first();

            $products[$product->id] = [
                "quantity"     => $item["amount"],
                "price"        => $item["price"],
                "observations" => "Item comprado no carrinho de compras: {$cart->id}",
            ];
        }

        if (isset($order->cart_id) && $order->cart_id == $data["cart_id"]) {
            $order
                ->products()
                ->syncWithoutDetaching($products);
            return $order;
        }

        $order = $client
            ->orders()
            ->updateOrCreate(
                [
                    "cart_id" => $data["cart_id"],
                ],
                [
                    'seller_id'          => null,
                    'reference'          => now()->format("m/Y"),
                    'type'               => TypeOrderEnum::AVULSO->value,
                    'status'             => StatusOrderEnum::APROVADA->value,
                    'total_price'        => $data["totalPrice"],
                    'monthly'            => false,
                    'date_monthly_start' => now()->format("Y-m-d"),
                    'date_monthly_end'   => null,
                    'booklet'            => false,
                    'not_start_end'      => false,
                    'contract_type'      => TypeContractEnum::contrato_3->value,
                ]
            );

        $order
            ->products()
            ->syncWithoutDetaching($products);

        return $order;
    }

    public
    function createOrExtractTransaction(
        Model $order,
        bool  $isUser
    ) {
        $data = $this->form->getState();

        $date = now()->format("Y-m-d H");

        $transaction = $order
            ->transactions()
            ->where("created_at", "like", "%$date%")
            ->get()
            ->first() ?? null;

        if (!isset($transaction->id) && $isUser) {
            return null;
        }

        if (isset($transaction->id)) {
            return $transaction;
        }

        return $order
            ->transactions()
            ->updateOrCreate(
                [
                    "order_id" => $order->id,
                ],
                [
                    'creator_id'         => $order->client->user->id ?? null,
                    'name'               => "Pagamento de carrinho de compras: {$data['cart_id']}",
                    'necessary'          => 1,
                    'type'               => TypeTransactionEnum::input->value,
                    'value'              => $data["totalPrice"],
                    'monthly'            => false,
                    'date_monthly_start' => now()->format("Y-m-d"),
                    'date_monthly_end'   => null,
                    'booklet'            => false,
                    'not_start_end'      => false,
                    'reference'          => now()->format("m/Y"),
                    'due_day'            => now()
                        ->addDays(3)
                        ->format("d"),
                    'paid'               => false,
                    'status'             => StatusTransactionEnum::AGUARDANDO->value,
                ]
            );
    }

    public
    function processCheckout(
        Model $transaction,
        bool  $isUser
    ) {
        $data = $this->form->getState();


        Notification::make()
                    ->success()
                    ->title('fsdfsff')
                    ->body("sdfdsfff")
                    ->send();
    }

    protected
    function getFormSchema(): array
    {
        return [
            Grid::make(1)
                ->schema(
                    [
                        Hidden::make("cart_id")
                              ->default($this->cart->id ?? null),

                        Hidden::make("totalPrice")
                              ->default($this->totalPrice ?? null),

                        Toggle::make("is_user")
                              ->label("Já tenho conta")
                              ->default(false)
                              ->live(),

                        Fieldset::make("is_user_not")
                                ->label("Dados de cadastro")
                                ->visible(fn(Get $get) => !$get("is_user"))
                                ->schema(
                                    [

                                        Select::make('people_type')
                                              ->label("Física/Jurídica")
                                              ->autofocus()
                                              ->live()
                                              ->default(1)
                                              ->options(
                                                  collect(TypePeopleEnum::cases())
                                                      ->mapWithKeys(
                                                          fn(TypePeopleEnum $type) => [
                                                              $type->value => $type->label()
                                                          ]
                                                      )
                                                      ->toArray()
                                              )
                                              ->required(),

                                        TextInput::make('document')
                                                 ->label("CPF/CNPJ")
                                                 ->placeholder(
                                                     function (Get $get) {
                                                         $people_type = $get("people_type");
                                                         return $people_type == 2 ? "99.999.999/9999-99" : "999.999.999-99";
                                                     }
                                                 )
                                                 ->mask(
                                                     function (Get $get) {
                                                         $people_type = $get("people_type");
                                                         return $people_type == 2 ? "99.999.999/9999-99" : "999.999.999-99";
                                                     }
                                                 )
                                                 ->maxLength(50)
                                                 ->required()
                                                 ->dehydrateStateUsing(fn($state) => preg_replace('/\D/', '', $state))
                                                 ->unique('clients'),

                                        DatePicker::make('birthday')
                                                  ->label("Nascimento")
                                                  ->required(
                                                      fn(Get $get) => $get("people_type") == TypePeopleEnum::F->value
                                                  ),

                                        TextInput::make('email')
                                                 ->label('E-mail')
                                                 ->email()
                                                 ->required(),

                                        TextInput::make('name')
                                                 ->label('Nome completo')
                                                 ->rule(
                                                     function (Get $get) {
                                                         return function (string $attribute, $value, $fail) use ($get) {
                                                             $explode = explode(" ", $value);
                                                             if (count($explode) < 2) {
                                                                 $this->loadData();

                                                                 $fail("Digite também o sobrenome!");
                                                             }
                                                         };
                                                     }
                                                 )
                                                 ->required()
                                                 ->columnSpanFull(),

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
                                                 ->rule(
                                                     function (Get $get) {
                                                         return function (string $attribute, $value, $fail) use ($get) {
                                                             $password = $get("password");
                                                             if ($password != $value) {
                                                                 $this->loadData();
                                                                 $fail("Confirmação de Senha Incorreta!");
                                                             }
                                                         };
                                                     }
                                                 )
                                                 ->required(),

                                        TextInput::make('cellphone')
                                                 ->label('Celular/Whatsapp')
                                                 ->prefixIcon("heroicon-o-phone")
                                                 ->mask("(99) 9999-99999")
                                                 ->required(),

                                        TextInput::make('zipcode')
                                                 ->label("Digite o CEP")
                                                 ->dehydrateStateUsing(fn($state) => preg_replace('/\D/', '', $state))
                                                 ->suffixAction(
                                                     Action::make('viaCep')
                                                           ->label("Buscar CEP")
                                                           ->icon('heroicon-m-map-pin')
                                                           ->action(
                                                               function (
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

                                                                   Notification::make()
                                                                               ->info()
                                                                               ->title('Próximo passo!')
                                                                               ->seconds(60)
                                                                               ->body(
                                                                                   "Informar ou validar os dados do seu endereço, que estão logo abaixo!"
                                                                               )
                                                                               ->send();
                                                               }
                                                           )
                                                 )
                                                 ->hint("Busca de CEP")
                                                 ->afterStateUpdated(
                                                     function (Set $set, Get $get, Component $livewire) {
                                                         $data = BuscarViaCepService::getData((string)$get("zipcode"));

                                                         if (isset($data["cep"])) {
                                                             $set('street', $data["logradouro"]);
                                                             $set('complement', $data["complemento"]);
                                                             $set('district', $data["bairro"]);
                                                             $set('city', $data["localidade"]);
                                                             $set('state', $data["uf"]);
                                                         }

                                                         Notification::make()
                                                                     ->info()
                                                                     ->title('Próximo passo!')
                                                                     ->seconds(60)
                                                                     ->body(
                                                                         "Informar ou validar os dados do seu endereço, que estão logo abaixo!"
                                                                     )
                                                                     ->send();
                                                     }
                                                 )
                                                 ->mask(
                                                     function (Get $get) {
                                                         return "99999-999";
                                                     }
                                                 )
                                                 ->debounce(1000)
                                                 ->required(),

                                    ]
                                ),

                        Fieldset::make("address")
                                ->label("Dados de endereço")
                                ->visible(
                                    function (Get $get) {
                                        return Str::length($get("zipcode")) == 9 && !$get("is_user");
                                    }
                                )
                                ->schema(
                                    [

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

                                    ]
                                ),

                        Fieldset::make("is_user_yes")
                                ->label("Dados de acesso")
                                ->visible(fn(Get $get) => $get("is_user"))
                                ->schema(
                                    [
                                        TextInput::make('email')
                                                 ->label('E-mail')
                                                 ->email()
                                                 ->required(),

                                        TextInput::make('password')
                                                 ->label('Senha')
                                                 ->revealable()
                                                 ->password()
                                                 ->required(),

                                    ]
                                ),
                    ]
                ),
        ];
    }

}
