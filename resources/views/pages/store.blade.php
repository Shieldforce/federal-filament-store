<x-filament::page>
    <div class="w-full mx-auto max-w-7xl">

        {{-- Filtros --}}
        <div class="flex flex-col md:flex-row justify-between gap-4 mb-6">

            <input
                wire:model.debounce.300ms="search"
                type="text"
                placeholder="Buscar produto..."
                class="w-full md:w-1/3 filament-input rounded-xl"
            >

            <select
                wire:model="category"
                class="filament-input rounded-xl"
            >
                <option value="">Todas categorias</option>
                @foreach ($this->categories as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
            </select>
        </div>

        {{-- Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mt-3">

            @forelse ($this->result_paginated as $product)

                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700
                            rounded-2xl shadow-sm overflow-hidden hover:shadow-lg transition group">

                    {{-- Imagem --}}
                    <div class="relative rounded-t-2xl overflow-hidden">
                        <img
                            src="{{ $product['image'] ?? asset('vendor/federal-filament-store/files/not-products-image.png') }}"
                            class="w-full h-48 object-cover transition group-hover:scale-105 duration-300"
                        />
                    </div>

                    {{-- Conteúdo --}}
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

        {{-- Paginação --}}
        <div class="mt-8">
            {{ $this->result_paginated->links() }}
        </div>

    </div>
</x-filament::page>
