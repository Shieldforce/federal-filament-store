<x-filament::page class="!max-w-full !p-0">

    {{-- REMOVE O MAX-WIDTH PADRÃO DO FILAMENT --}}
    {{--<style>
        .fi-body, .fi-main {
            max-width: 100% !important;
        }
    </style>--}}

    <div class="w-full flex flex-col md:flex-row gap-6">

        {{-- SIDEBAR --}}
        <div class="md:w-[260px] md:flex-none pl-6">

            <x-filament::section class="!max-w-[260px] w-full">
                <h3 class="text-lg font-semibold mb-2">Filtros</h3>

                <x-filament-panels::form wire:submit="filtrar">
                    {{ $this->form }}

                    <x-filament::button
                        type="submit"
                        icon="heroicon-o-funnel"
                        color="primary"
                        class="w-full mt-4"
                    >
                        Aplicar filtros
                    </x-filament::button>
                </x-filament-panels::form>
            </x-filament::section>

        </div>

        {{-- PRODUTOS --}}
        <div class="flex-1 pr-6">

            {{-- PAGINAÇÃO SUPERIOR --}}
            <div class="flex justify-between items-center mb-4">
                <div></div>
                <div>
                    {{ $this->paginatedProducts->links() }}
                </div>
            </div>

            {{-- GRID DE PRODUTOS --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                @foreach ($this->paginatedProducts as $product)

                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700
                                rounded-2xl shadow-sm overflow-hidden hover:shadow-lg transition group">

                        <div class="relative overflow-hidden">
                            <img
                                src="{{ $product['image'] ?? asset('vendor/federal-filament-store/files/not-products-image.png') }}"
                                class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-105"
                            />
                        </div>

                        <div class="p-4 flex flex-col gap-3">
                            <h3 class="font-semibold text-lg">{{ $product['name'] }}</h3>
                            <p class="text-gray-500 text-sm">{{ $product['short'] ?? '' }}</p>

                            <div class="flex items-center justify-between mt-auto pt-2">
                                <span class="text-xl font-bold text-primary-600">
                                    R$ {{ number_format($product['price'], 2, ',', '.') }}
                                </span>

                                <x-filament::button
                                    wire:click="addToCart({{ $product['id'] }})"
                                    color="primary"
                                    icon="heroicon-o-shopping-cart"
                                    size="sm"
                                >
                                    Adicionar
                                </x-filament::button>
                            </div>
                        </div>

                    </div>

                @endforeach
            </div>

            {{-- PAGINAÇÃO INFERIOR --}}
            <div class="flex justify-between items-center mt-6">
                <div></div>
                <div>
                    {{ $this->paginatedProducts->links() }}
                </div>
            </div>

        </div>

    </div>

</x-filament::page>
