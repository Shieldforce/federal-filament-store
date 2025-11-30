<x-filament::page>
    <div class="w-full mx-auto max-w-7xl">

        {{-- Barra de filtros --}}
        <div class="flex flex-col md:flex-row justify-between gap-4 mb-6">
            <input
                wire:model.debounce.500ms="search"
                type="text"
                placeholder="Buscar produto..."
                class="w-full md:w-1/3 filament-input rounded-lg"
            >

            <select
                wire:model="category"
                class="filament-input rounded-lg"
            >
                <option value="">Todas categorias</option>
                @foreach ($this->categories as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
            </select>
        </div>

        {{-- Grid de produtos --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mt-3">
            @forelse ($this->result as $product)
                <div
                    class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition group">

                    {{-- Imagem --}}
                    <div class="relative rounded-t-xl overflow-hidden">
                        <img
                            src="{{ $product['image'] ?? asset('vendor/federal-filament-store/files/not-products-image.png') }}"
                            class="w-full h-48 object-cover transition duration-300 group-hover:scale-105"
                        />
                    </div>

                    {{-- Conteúdo --}}
                    <div class="p-4 flex flex-col gap-3">

                        {{-- Nome --}}
                        <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 leading-tight line-clamp-2 min-h-[48px]">
                            {{ $product['name'] }}
                        </h3>

                        {{-- Descrição curta --}}
                        <p class="text-gray-500 dark:text-gray-400 text-sm line-clamp-2 min-h-[36px]">
                            {{ $product['short'] ?? 'Descrição breve do produto...' }}
                        </p>

                        {{-- Preço + Botão --}}
                        <div class="flex items-center justify-between pt-2 mt-auto">
                            <span class="text-xl font-bold text-primary-600">
                                R$ {{ number_format($product['price'], 2, ',', '.') }}
                            </span>

                            <x-filament::button
                                wire:click="addToCart({{ $product['id'] }})"
                                color="primary"
                                icon="heroicon-o-shopping-cart"
                                size="sm"
                                class="shadow-sm"
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
