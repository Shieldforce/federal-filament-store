<x-filament::page>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

        {{-- COLUNA DE FILTROS --}}
        <div class="md:col-span-1 space-y-6">

            <x-filament::section>
                <h3 class="text-lg font-semibold mb-2">Filtros</h3>

                <x-filament-panels::form wire:submit="filtrar">
                    {{ $this->form }}

                    {{-- BOTÃO FILTRAR --}}
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

        {{-- COLUNA DA LISTA + PAGINAÇÃO --}}
        <div class="md:col-span-3">

            {{-- PAGINAÇÃO SUPERIOR --}}
            <div class="flex justify-between items-center mb-4">
                <div class="text-sm text-gray-600 dark:text-gray-300"></div>
                <div>
                    {{ $this->paginatedProducts->links() }}
                </div>
            </div>

            {{-- GRID DE PRODUTOS --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse ($this->paginatedProducts as $product)

                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700
                                rounded-2xl shadow-sm overflow-hidden hover:shadow-lg transition">

                        <img
                            src="{{ $product['image'] ?? asset('vendor/federal-filament-store/files/not-products-image.png') }}"
                            class="w-full h-48 object-cover transition group-hover:scale-105 duration-300"
                        />

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

    </div>

</x-filament::page>
