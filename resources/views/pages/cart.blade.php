<x-filament::page class="!max-w-full !p-0">
    <div class="w-full flex flex-col md:flex-row gap-6">

        {{-- SIDEBAR --}}
        <div
            class="md:w-[480px] lg:w-[520px] xl:w-[560px] md:flex-none pl-6 config-cart-ec"
            style="display: {{ isset($this->totalPrice) && $this->totalPrice > 0 ? 'block' : 'none' }}"
        >
            <x-filament::section class="!max-w-[480px] w-full !rounded-none">
                <h1 style="font-size: 16pt;">
                    Total do carrinho: R$ {{ number_format($this->totalPrice, 2, ",", ".") }}
                </h1>

                <br><hr><br>

                <form wire:submit="submit">
                    {{ $this->form }}

                    <br><hr>

                    <div class="flex flex-col mt-4 gap-4 w-full">
                        <x-filament::button
                            color="success"
                            icon="heroicon-o-check-circle"
                            class="w-full py-3 text-center"
                            type="submit"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            wire:loading.attr="disabled"
                            wire:target="submit"
                        >
                            <span wire:loading wire:target="submit">Processando...</span>
                            <span wire:loading.remove wire:target="submit">Ir Para checkout</span>
                        </x-filament::button>
                    </div>
                </form>
            </x-filament::section>
        </div>

        {{-- LISTA DE ITENS --}}
        <div class="flex-1 pr-6">
            <div class="space-y-4">
                @forelse($this->items as $item)

                    <div class="flex gap-4 p-4 rounded-xl border bg-white shadow-sm items-start">

                        {{-- IMAGEM COM LIMITE FIXO --}}
                        <div
                            class="w-[140px] h-[140px] max-w-[140px] max-h-[140px]
               flex-none flex items-center justify-center
               bg-gray-100 rounded-xl overflow-hidden"
                        >
                            <img
                                src="{{ isset($item['data_product']['image'])
                ? asset("storage/{$item['data_product']['image']}")
                : asset('vendor/federal-filament-store/files/not-products-image.png') }}"
                                alt="{{ $item['name'] }}"
                                class="max-w-full max-h-full object-contain"
                            >
                        </div>

                        {{-- CONTEÚDO --}}
                        <div class="flex-1 flex flex-col text-left">

                            {{-- TÍTULO --}}
                            <h3 class="text-lg font-semibold leading-tight">
                                {{ $item['name'] }}
                            </h3>

                            {{-- SUBINFO --}}
                            <p class="text-sm text-gray-500 mt-1">
                                {{ count($item['data_product']['images'] ?? []) }}
                                imagens cadastradas
                            </p>

                            {{-- QUANTIDADE --}}
                            <div class="flex items-center gap-3 mt-4">
                                <button
                                    wire:click="decreaseQty('{{ $item['uuid'] }}')"
                                    class="w-8 h-8 flex items-center justify-center border rounded-full hover:bg-gray-100"
                                >–</button>

                                <span class="font-semibold text-lg">
                {{ $item['amount'] }}
            </span>

                                <button
                                    wire:click="increaseQty('{{ $item['uuid'] }}')"
                                    class="w-8 h-8 flex items-center justify-center border rounded-full hover:bg-gray-100"
                                >+</button>
                            </div>

                            {{-- PREÇO --}}
                            <p class="font-semibold text-lg mt-4">
                                R$ {{ number_format($item['price'] * $item['amount'], 2, ',', '.') }}
                            </p>

                            {{-- REMOVER --}}
                            <button
                                wire:click="removeItem('{{ $item['uuid'] }}')"
                                class="text-red-500 text-sm hover:underline mt-1"
                            >
                                Remover
                            </button>

                        </div>

                    </div>

                @empty
                    <div class="text-center text-gray-500 py-20">
                        Seu carrinho está vazio.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</x-filament::page>
