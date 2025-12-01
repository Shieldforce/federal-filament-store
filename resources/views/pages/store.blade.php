<x-filament::page class="!max-w-full px-0">

    <div class="w-full grid grid-cols-1 md:grid-cols-[1fr_280px] gap-6">

        {{-- LISTA DE PRODUTOS (ESQUERDA) --}}
        <div class="order-2 md:order-1">

            {{-- PAGINAÇÃO SUPERIOR --}}
            <div class="flex justify-between items-center mb-4">
                {{ $products->links() }}
            </div>

            {{-- GRID DE PRODUTOS --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($products as $product)
                    <div class="p-4 border rounded-xl shadow-sm bg-white">
                        <img src="{{ $product->image }}" class="w-full h-32 object-contain mb-2" />
                        <div class="font-bold">{{ $product->name }}</div>
                        <div class="text-primary-600 font-semibold">
                            R$ {{ number_format($product->price, 2, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- PAGINAÇÃO INFERIOR --}}
            <div class="mt-4">
                {{ $products->links() }}
            </div>

        </div>

        {{-- SIDEBAR DE FILTROS (DIREITA) --}}
        <div class="order-1 md:order-2">

            <x-filament::section>
                <h3 class="text-lg font-semibold mb-3">Filtros</h3>

                {{-- BUSCA --}}
                <input
                    wire:model.debounce.300ms="search"
                    type="text"
                    placeholder="Buscar produto..."
                    class="w-full filament-input rounded-xl mb-3"
                />

                {{-- CATEGORIAS --}}
                <select
                    wire:model.live="category"
                    class="w-full filament-input rounded-xl mb-3"
                >
                    <option value="">Todas categorias</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>

                {{-- BOTÃO FILTRAR --}}
                <button
                    wire:click="applyFilters"
                    class="w-full py-3 bg-primary-700 text-white rounded-xl"
                >
                    Aplicar filtros
                </button>

            </x-filament::section>

        </div>

    </div>

</x-filament::page>
