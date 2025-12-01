<x-filament::page class="!max-w-full px-0">

    <!-- GRID: produtos à esquerda / filtros à direita -->
    <div class="w-full grid grid-cols-1 md:grid-cols-[1fr_280px] gap-6">

        {{-- LISTA DE PRODUTOS (agora primeiro!) --}}
        <div class="pr-4 produtos">

            {{-- PAGINAÇÃO SUPERIOR --}}
            <div class="flex justify-between items-center mb-4">
                <div></div>
                <div>
                    {{ $this->paginatedProducts->links() }}
                </div>
            </div>

            {{-- GRID DE PRODUTOS (3 colunas) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                @forelse ($this->paginatedProducts as $product)

                    <div style="border-radius: 10px !important;" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden hover:shadow-lg transition group">
                        <div class="relative rounded-t-2xl overflow-hidden">
                            <img
                                src="{{ $product['image'] ?? asset('vendor/federal-filament-store/files/not-products-image.png') }}"
                                class="w-full h-48 object-cover transition group-hover:scale-105 duration-300"
                            />
                        </div>

                        <div class="p-4 flex flex-col gap-3">
                            <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 line-clamp-2">
                                {{ $product['name'] }}
                            </h3>

                            <p class="text-gray-500 dark:text-gray-400 text-sm line-clamp-2">
                                {{ $product['short'] ?? 'Descrição breve do produto...' }}
                            </p>

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

                @empty
                    <div class="col-span-full text-center text-gray-400 py-16">
                        Nenhum produto encontrado.
                    </div>
                @endforelse
            </div>

            {{-- PAGINAÇÃO INFERIOR --}}
            <div class="flex justify-between items-center mt-6">
                <div></div>
                <div>
                    {{ $this->paginatedProducts->links() }}
                </div>
            </div>

        </div>

        {{-- SIDEBAR DE FILTROS (à direita!) --}}
        <div class="space-y-6 pl-4 filtros">

            <x-filament::section>
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

    </div>

</x-filament::page>
