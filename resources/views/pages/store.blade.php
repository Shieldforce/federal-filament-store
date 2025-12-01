<x-filament::page>

    {{-- FILTROS --}}
    <x-filament::section>
        <x-filament-panels::form wire:submit="filtrar">
            {{ $this->form }}

            <div class="flex justify-between mt-4 gap-4">
                <x-filament::button color="primary" icon="heroicon-o-funnel" type="submit" class="w-1/5">
                    Filtrar
                </x-filament::button>
            </div>
        </x-filament-panels::form>
    </x-filament::section>

    {{-- PAGINAÇÃO SUPERIOR --}}
    <div class="flex justify-between items-center mt-6 mb-3">
        <div class="text-sm text-gray-600 dark:text-gray-300">
           {{-- {{ $this->paginatedProducts->firstItem() }} até {{ $this->paginatedProducts->lastItem() }}
            de {{ $this->paginatedProducts->total() }} resultados--}}
        </div>
        <div>
            {{ $this->paginatedProducts->links() }}
        </div>
    </div>

    {{-- GRID --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mt-3">
        @forelse ($this->paginatedProducts as $product)
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden hover:shadow-lg transition group">
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
    <div class="flex justify-between items-center mt-6 mb-2">
        <div class="text-sm text-gray-600 dark:text-gray-300">
            {{--{{ $this->paginatedProducts->firstItem() }} até {{ $this->paginatedProducts->lastItem() }}
            de {{ $this->paginatedProducts->total() }} resultados--}}
        </div>
        <div>
            {{ $this->paginatedProducts->links() }}
        </div>
    </div>
</x-filament::page>
