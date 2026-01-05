<?php

namespace Shieldforce\FederalFilamentStore\Pages;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Get;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Livewire\WithPagination;
use Shieldforce\FederalFilamentStore\Models\Cart;

class FederalFilamentProductPage extends Page implements HasForms
{

    use InteractsWithForms;
    use WithPagination;

    protected static string  $view            = 'federal-filament-store::pages.product';
    protected static ?string $label           = 'Produto';
    protected static ?string $navigationLabel = 'Produto';
    protected static ?string $title           = 'Produto';
    public array             $result          = [];
    public array             $categories      = [];
    public array             $images          = [];
    public array             $files           = [];
    public array             $colors          = [];
    public string            $action          = '';
    public int               $amount          = 1;
    public array             $product;
    public                   $productConfig;
    public float             $totalPrice;
    public                   $uuid;
    public bool              $image_all;
    public bool              $publish_social_network;

    public
    function getTitle(): string|Htmlable
    {
        return $this->product['name'] ?? parent::getTitle();
    }

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
        return 'external-ffs-product';
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

        $this->result = config('federal-filament-store.products_callback');
        $this->categories = config('federal-filament-store.categories_callback');
        $this->uuid = explode("/", $_SERVER["REQUEST_URI"])[3] ?? null;

        $productFilter = array_filter(
            $this->result,
            function ($product) {
                return $product['uuid'] == $this->uuid;
            }
        );

        $this->product = reset($productFilter) ?: [];
        $this->images[] = isset($this->product['image']) ? env("APP_URL")."/storage/".$this->product['image']
            : 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=2070&q=80';

        foreach ($this->product['images'] ?? [] as $image) {
            $this->images[] = env("APP_URL")."/storage/".$image['path'];
        }

        $product = DB::table("products")
                     ->where("uuid", $this->product['uuid'])
                     ->first();
        $this->productConfig = DB::table("config_products")
                                 ->where("product_id", $product->id)
                                 ->first();

        $this->amount = $this?->productConfig?->limit_min_amount ?? 1;
        $this->image_all = false;
        $this->totalPrice = $this->amount * $this->product['price'];

        $colors = DB::table('colors_products')
                    ->where('product_id', $product->id)
                    ->get(['id', 'name', 'color']);

        $this->colors = $colors
            ->mapWithKeys(
                fn($color) => [
                    $color->color => $color->name,
                ]
            )
            ->toArray();
    }

    public
    function updated(
        $property
    ) {
        if ($property == 'amount' && isset($this->amount)) {
            $this->totalPrice = $this->amount * $this->product['price'];
        }

        if ($property == 'amount' && !isset($this->amount)) {
            $this->totalPrice = 0.00;
        }
    }

    protected
    function getFormSchema(): array
    {
        $minAmount = $this?->productConfig?->limit_min_amount ?? 1;

        return [
            Grid::make(1)
                ->schema(
                    [
                        TextInput::make('amount')
                                 ->label('Quantidade')
                                 ->numeric()
                                 ->reactive()
                                 ->live()
                                 ->debounce(3)
                                 ->required()
                                 ->default($minAmount)
                                 ->rule(
                                     function (Get $get) use ($minAmount) {
                                         return function (string $attribute, $value, $fail) use ($get, $minAmount) {
                                             if ($minAmount > $value) {
                                                 $fail(
                                                     "Quantidade mínima {$minAmount} é obrigatório."
                                                 );
                                             }
                                         };
                                     }
                                 ),

                        Radio::make('color')
                             ->label('Escolha a cor')
                             ->required()
                             ->options($this->colors)
                             ->descriptions(
                                 collect($this->colors)
                                     ->map(
                                         fn($name, $hex) => new HtmlString(
                                             '<div style="
                                            display:flex;
                                            align-items:center;
                                            gap:8px;
                                        ">
                                            <span style="
                                                width:20px;
                                                height:20px;
                                                border-radius:4px;
                                                background:'.$hex.';
                                                border:1px solid #ccc;
                                            "></span>
                                        </div>'
                                         )
                                     )
                                     ->toArray()
                             )
                             ->visible(fn() => count($this->colors) > 0),

                        Toggle::make("image_all")
                              ->label("Usar a mesma imagem")
                              ->default(false)
                              ->reactive()
                              ->visible(fn() => $this?->productConfig?->image_all ?? true)
                              ->live(),

                        Toggle::make("publish_social_network")
                              ->label("Podemos publicar nas redes sociais?")
                              ->default(false)
                              ->reactive()
                              ->visible(fn() => $this?->productConfig?->publish_social_network ?? true)
                              ->live(),

                        FileUpload::make('files')
                                  ->directory('files_products')
                                  ->columnSpanFull()
                                  ->required()
                                  ->multiple()
                                  ->reactive()
                                  ->visible(fn() => $this?->productConfig?->files ?? true)
                                  ->live()
                                  ->image()
                                  ->imageEditor()
                                  ->imageEditorAspectRatios(['1:1'])
                                  ->openable()
                                  ->previewable(true)
                                  ->label('Imagens Necessárias')
                                  ->rule(
                                      function (Get $get) {
                                          return function (string $attribute, $value, $fail) use ($get) {
                                              $image_all = $get("image_all");
                                              $amountImages = count($get("files"));
                                              $amount = (int)$get('amount');

                                              if ($image_all && $amountImages !== 1) {
                                                  $fail(
                                                      "Você enviou {$amountImages} imagens, mas precisa enviar exatamente 1."
                                                  );
                                              }

                                              if (!$image_all && $amountImages !== $amount) {
                                                  $fail(
                                                      "Você enviou {$amountImages} imagens, mas precisa enviar exatamente {$amount}."
                                                  );
                                              }
                                          };
                                      }
                                  ),

                    ]
                ),
        ];
    }

    public
    function addCart()
    {
        Notification::make()
                    ->success()
                    ->title('Item adicionado ao carrinho!')
                    ->body("Redirecionando para Loja em 30 segundos.... Ou Clique em Ir para Loja!")
                    ->seconds(30)
                    ->actions(
                        [
                            Action::make('Ir para Loja')
                                  ->button()
                                  ->color('primary')
                                  ->icon('heroicon-o-shopping-bag')
                                  ->url('/admin/ffs-store'),
                            Action::make('Ir para o Carrinho')
                                  ->button()
                                  ->color('primary')
                                  ->icon('heroicon-o-shopping-cart')
                                  ->url('/admin/ffs-cart'),
                        ]
                    )
                    ->send();

        $this->dispatch('redirect-after-delay');
    }

    public
    function finish()
    {
        $this->redirect("/admin/ffs-cart");

        Notification::make()
                    ->success()
                    ->title('Agora finalize sua compra no carrinho!')
                    ->send();
    }

    public
    function submit()
    {
        $this->validate();

        $this->cartUpdate();

        $this->dispatch('cart-updated');

        if ($this->action === 'addCart') {
            $this->addCart();
        }

        if ($this->action === 'finish') {
            $this->finish();
        }
    }

    public
    function cartUpdate()
    {
        $identifier = request()->cookie('ffs_identifier');

        $cartModel = Cart::where("identifier", $identifier)
                         ->first();

        $exists = false;

        $cart = isset($cartModel->items) ? json_decode($cartModel->items, true) : [];

        foreach ($cart as &$item) {
            if ($item['uuid'] === $this->product['uuid']) {
                $item['amount'] += (int)$this->amount;
                $exists = true;
                break;
            }
        }

        $data = $this->form->getState();

        if (!$exists) {
            $cart[] = [
                'uuid'         => $this->product['uuid'],
                'name'         => $this->product['name'],
                'amount'       => (int)$this->amount,
                'price'        => $this->product['price'],
                'data_product' => [
                    "amount"                 => $data["amount"],
                    "image"                  => $this->product["image"],
                    "images"                 => $this->product["images"],
                    "image_all"              => $data["image_all"] ?? null,
                    "publish_social_network" => $data["publish_social_network"] ?? null,
                    "color"                  => $data["color"] ?? null,
                ],
            ];
        }

        $cartModel->update(["items" => json_encode($cart)]);
    }
}

