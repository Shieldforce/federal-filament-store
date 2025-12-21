<?php

namespace Shieldforce\FederalFilamentStore\Pages;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Shieldforce\FederalFilamentStore\Enums\TypePeopleEnum;
use Shieldforce\FederalFilamentStore\Models\Cart;
use Shieldforce\FederalFilamentStore\Services\BuscarViaCepService;

class FederalFilamentCartPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'federal-filament-store::pages.cart';

    protected array $items = [];
    protected ?Cart $cart = null;

    public ?int $cart_id = null;
    public float $totalPrice = 0;
    public bool $is_user = false;

    /* ========================================================= */

    public function mount(): void
    {
        if (!Auth::check()) {
            filament()->getCurrentPanel()->topNavigation();
        }

        $this->loadData(true);
    }

    /**
     * 🔐 loadData seguro (não destrói state)
     */
    public function loadData(bool $force = false): void
    {
        if ($this->cart && !$force) {
            return;
        }

        $identifier = request()->cookie('ffs_identifier');

        if (!$identifier) {
            $this->items = [];
            $this->totalPrice = 0;
            $this->cart_id = null;
            return;
        }

        $cart = Cart::where('identifier', $identifier)->first();

        if (!$cart) {
            $this->items = [];
            $this->totalPrice = 0;
            $this->cart_id = null;
            return;
        }

        $this->cart = $cart;
        $this->cart_id = $cart->id;

        if (empty($this->items)) {
            $this->items = json_decode($cart->items ?? '[]', true);
        }

        $this->totalPrice = collect($this->items)->sum(
            fn ($item) => ($item['price'] ?? 0) * ($item['amount'] ?? 1)
        );
    }

    /* ========================================================= */

    protected function getFormSchema(): array
    {
        return [
            Grid::make(1)->schema([

                Hidden::make('cart_id')->default(fn () => $this->cart_id),
                Hidden::make('totalPrice')->default(fn () => $this->totalPrice),

                Toggle::make('is_user')
                      ->label('Já tenho conta')
                      ->live(),

                /* ================= CADASTRO ================= */

                Fieldset::make('register')
                        ->label('Dados de cadastro')
                        ->visible(fn (Get $get) => !$get('is_user'))
                        ->schema([

                            Select::make('people_type')
                                  ->label('Tipo de Pessoa')
                                  ->options(
                                      collect(TypePeopleEnum::cases())
                                          ->mapWithKeys(fn ($t) => [$t->value => $t->label()])
                                          ->toArray()
                                  )
                                  ->required(),

                            TextInput::make('document')
                                     ->label('CPF/CNPJ')
                                     ->required()
                                     ->dehydrateStateUsing(fn ($state) => preg_replace('/\D/', '', $state))
                                     ->rules([
                                         Rule::unique('clients', 'document'),
                                     ]),

                            DatePicker::make('birthday')
                                      ->label('Nascimento')
                                      ->required(),

                            TextInput::make('register_email')
                                     ->label('E-mail')
                                     ->email()
                                     ->required()
                                     ->rules([
                                         Rule::unique('users', 'email'),
                                     ]),

                            TextInput::make('name')
                                     ->label('Nome completo')
                                     ->required()
                                     ->rule(fn () => function ($attr, $value, $fail) {
                                         if (count(explode(' ', trim($value))) < 2) {
                                             $fail('Digite nome e sobrenome.');
                                         }
                                     })
                                     ->columnSpanFull(),

                            TextInput::make('register_password')
                                     ->label('Senha')
                                     ->password()
                                     ->minLength(4)
                                     ->required(),

                            TextInput::make('password_confirmation')
                                     ->label('Confirmar senha')
                                     ->password()
                                     ->same('register_password')
                                     ->required(),

                            TextInput::make('cellphone')
                                     ->label('Celular')
                                     ->required(),

                            TextInput::make('zipcode')
                                     ->label('CEP')
                                     ->required()
                                     ->suffixAction(
                                         Action::make('viaCep')
                                               ->label('Buscar CEP')
                                               ->action(function (Set $set, $state) {
                                                   $data = BuscarViaCepService::getData($state);
                                                   if ($data) {
                                                       $set('street', $data['logradouro'] ?? null);
                                                       $set('district', $data['bairro'] ?? null);
                                                       $set('city', $data['localidade'] ?? null);
                                                       $set('state', $data['uf'] ?? null);
                                                   }
                                               })
                                     ),

                            TextInput::make('street')->label('Rua')->required(),
                            TextInput::make('number')->label('Número'),
                            TextInput::make('district')->label('Bairro')->required(),
                            TextInput::make('city')->label('Cidade')->required(),
                            TextInput::make('state')->label('UF')->required(),
                        ]),

                /* ================= LOGIN ================= */

                Fieldset::make('login')
                        ->label('Dados de acesso')
                        ->visible(fn (Get $get) => $get('is_user'))
                        ->schema([

                            TextInput::make('login_email')
                                     ->label('E-mail')
                                     ->email()
                                     ->required(),

                            TextInput::make('login_password')
                                     ->label('Senha')
                                     ->password()
                                     ->required(),

                            Placeholder::make('forgot_password')
                                       ->content(
                                           new \Illuminate\Support\HtmlString(
                                               '<a href="/admin/password-reset/request" class="text-sm text-primary-600 hover:underline" target="_blank">
                                        Esqueceu a senha?
                                    </a>'
                                           )
                                       ),
                        ]),
            ]),
        ];
    }
}
