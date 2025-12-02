<x-filament::page class="!max-w-full !p-0">
    <div class="w-full flex flex-col md:flex-row gap-6">

        {{-- SIDEBAR --}}
        <div class="md:w-[480px] lg:w-[520px] xl:w-[560px] md:flex-none pl-6 filtros-store-ec">
            <x-filament::section class="!max-w-[480px] w-full !rounded-none">
                {{ $this->form }}

                <div class="flex mb-3" style="margin-top: 20px !important;">
                    <x-filament::button
                        color="danger"
                        icon="heroicon-o-x-circle"
                        wire:click="clearFilters"
                        size="sm"
                    >
                        Limpar filtros
                    </x-filament::button>
                </div>
            </x-filament::section>
        </div>

        {{-- PRODUTOS --}}
        <div class="flex-1 pr-6">

            {{-- PAGINAÇÃO SUPERIOR --}}
            <div class="flex justify-between items-center mb-4">
                <div style="height: 38px;"></div>
                <div>{{ $this->paginatedProducts->links() }}</div>
            </div>

            {{-- GRID DE PRODUTOS --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                @forelse($this->paginatedProducts as $product)
                    <div
                        class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700
                                rounded-2xl shadow-sm overflow-hidden hover:shadow-lg transition group"
                        style="border-radius: 10px !important;"
                    >

                        <div class="relative overflow-hidden">
                            <img
                                height="170"
                                style="height: 170px !important;"
                                src="{{ $product['image'] ? asset("storage/{$product['image']}") : asset('vendor/federal-filament-store/files/not-products-image.png') }}"
                                class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-105"
                            />
                        </div>

                        <div class="p-4 flex flex-col gap-3">
                            <h3 class="font-semibold text-lg">{{ Str::limit($product['name'], 17) }}</h3>
                            <p class="text-gray-500 text-sm">{{ $product['code'] ? Str::limit($product['code'], 17) : '' }}</p>

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
                                    Ver detalhes
                                </x-filament::button>
                            </div>
                        </div>

                    </div>
                @empty
                    <div
                        class="col-span-full flex items-center p-6 bg-white dark:bg-gray-800 border
                                border-gray-200 dark:border-gray-700 rounded-2xl shadow-md"
                        {{--style="margin-top: 38px !important;"--}}
                    >

                        <div class="flex-shrink-0 mr-6">
                            <img
                                width="150"
                                src="{{ asset('vendor/federal-filament-store/files/not-products-image.png') }}"
                                alt="Nenhum produto encontrado"
                                class="w-24 h-24 object-contain"
                            />
                        </div>

                        <div class="flex flex-col justify-center ml-5" style="margin-left: 30px !important;">
                            <h3 class="text-gray-600 dark:text-gray-300 text-lg font-semibold mb-1">
                                Nenhum produto encontrado
                            </h3>
                            <p class="text-gray-400 dark:text-gray-500 text-sm">
                                Tente ajustar os filtros ou buscar por outro termo.
                            </p>
                        </div>

                    </div>
                @endforelse
            </div>

            {{-- PAGINAÇÃO INFERIOR --}}
            <div class="flex justify-between items-center mt-6">
                <div style="height: 38px;"></div>
                <div>{{ $this->paginatedProducts->links() }}</div>
            </div>

        </div>
    </div>
</x-filament::page>
