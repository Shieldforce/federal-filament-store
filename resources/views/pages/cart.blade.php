<x-filament::page class="!max-w-full !p-0">
    <div class="w-full flex flex-col md:flex-row gap-6">

        {{-- SIDEBAR --}}
        <div class="md:w-[480px] lg:w-[520px] xl:w-[560px] md:flex-none pl-6 config-cart-ec">
            <x-filament::section class="!max-w-[480px] w-full !rounded-none">
                <h1 style="font-size: 16pt;">
                    Total do carrinho: R$ {{ number_format($this->totalPrice, 2, ",", ".") }}
                </h1>
                <br>
                <hr>
                <br>
                <form wire:submit="submit">

                    {{ $this->form }}

                    <br>
                    <hr>

                    <div class="flex flex-col mt-4 gap-4 w-full">
                        {{-- FINALIZAR COMPRA --}}
                        {{--<x-filament::button
                            color="success"
                            icon="heroicon-o-check-circle"
                            class="w-full py-3 text-center"
                            type="submit"
                        >
                            Ir Para checkout
                        </x-filament::button>--}}

                        <x-filament::button
                            color="success"
                            icon="heroicon-o-check-circle"
                            class="w-full py-3 text-center"
                            type="submit"

                            wire:loading.class="opacity-50 cursor-not-allowed"
                            wire:loading.attr="disabled"
                            wire:target="submit"
                        >
                            <span wire:loading wire:target="submit">
                                <x-filament::loading-indicator class="w-5 h-5" />
                            </span>

                            <span wire:loading.remove wire:target="submit">
                                Ir Para checkout
                            </span>
                        </x-filament::button>

                    </div>

                </form>

            </x-filament::section>

        </div>

        <div class="flex-1 pr-6">
            <div class="gap-6">

                <div class="space-y-4">
                    @forelse($this->items as $item)
                        <div class="flex gap-4 p-4 rounded-xl border bg-white shadow-sm">

                            {{-- IMAGEM --}}
                            <div class="w-24 h-24 flex-none bg-gray-100 rounded-xl overflow-hidden">
                                <img
                                    src="{{ asset("storage/{$item['data_product']['files'][0]}") }}" alt="{{ $item['name'] }}"
                                     class="w-full h-full object-cover"
                                    style="width: 200px;height: 130px;"
                                >
                            </div>

                            {{-- DETALHES --}}
                            <div class="flex-1 flex flex-col justify-between">

                                <div>
                                    <h3 class="text-lg font-semibold">
                                        {{ $item['name'] }}
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        {{ count($item['data_product']['files'] ?? []) }}
                                        imagens upadas para esse produto
                                    </p>
                                </div>

                                {{-- QUANTIDADE + PREÇOS --}}
                                <div class="flex items-center justify-between mt-2">

                                    {{-- CONTROLES --}}
                                    <div class="flex items-center gap-3">
                                        <button
                                            wire:click="decreaseQty('{{ $item['uuid'] }}')"
                                            class="w-8 h-8 flex items-center justify-center border rounded-full hover:bg-gray-100"
                                        >–</button>

                                        <span class="font-semibold text-lg">{{ $item['amount'] }}</span>

                                        <button
                                            wire:click="increaseQty('{{ $item['uuid'] }}')"
                                            class="w-8 h-8 flex items-center justify-center border rounded-full hover:bg-gray-100"
                                        >+</button>
                                    </div>

                                    {{-- SUBTOTAL --}}
                                    <div class="text-right">
                                        <p class="font-semibold text-lg">
                                            R$ {{ number_format($item['price'] * $item['amount'], 2, ',', '.') }}
                                        </p>
                                        <button
                                            wire:click="removeItem('{{ $item['uuid'] }}')"
                                            class="text-red-500 text-sm hover:underline"
                                        >
                                            Remover
                                        </button>
                                    </div>

                                </div>

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
    </div>

</x-filament::page>

@push('scripts')

@endpush
