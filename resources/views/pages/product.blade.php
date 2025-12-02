<x-filament::page class="!max-w-full !p-0">

    <div class="w-full grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-10 px-6 py-6">

        {{-- =========================================
            COLUNA ESQUERDA - FOTOS + NOME + DESCRIÇÃO
        ========================================== --}}
        <div class="space-y-6">

            {{-- SLIDER DE FOTOS --}}
            <div class="w-full">
                <div class="relative overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="swiper mySwiper">
                        <div class="swiper-wrapper">

                            @foreach($product->images ?? [] as $img)
                                <div class="swiper-slide">
                                    <img
                                        class="w-full h-[380px] object-contain bg-white dark:bg-gray-900"
                                        src="{{ asset('storage/' . $img->path) }}"
                                    >
                                </div>
                            @endforeach

                        </div>

                        {{-- NEXT / PREV --}}
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>

                        {{-- PAGINATION --}}
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            </div>

            {{-- NOME DO PRODUTO --}}
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
                {{ $product->name ?? '' }}
            </h1>

            {{-- DESCRIÇÃO CURTA --}}
            @if(isset($product->short_description))
                <p class="text-gray-600 dark:text-gray-400 text-lg leading-relaxed">
                    {{ $product->short_description }}
                </p>
            @endif

            {{-- DESCRIÇÃO LONGA --}}
            @if(isset($product->long_description))
                <div class="prose dark:prose-invert max-w-full text-gray-700 dark:text-gray-300">
                    {!! nl2br(e($product->long_description)) !!}
                </div>
            @endif

        </div>


        {{-- =========================================
            COLUNA DIREITA - OPÇÕES DE COMPRA
        ========================================== --}}
        <div>

            <x-filament::section class="rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">

                <div class="space-y-6">

                    {{-- PREÇO --}}
                    <div>
                        <span class="text-2xl font-bold" style="color: darkgreen;">
                            R$ {{ isset($product->price) ? number_format($product->price, 2, ',', '.') : '' }}
                        </span>
                    </div>

                    {{-- QUANTIDADE --}}
                    <div>
                        <x-filament::input.wrapper>
                            <x-filament::input>
                                Quantidade
                            </x-filament::input>

                            <x-filament::input
                                type="number"
                                min="1"
                                wire:model="quantity"
                                class="w-32"
                            />
                        </x-filament::input.wrapper>
                    </div>

                    {{-- COR --}}
                    <div>
                        <x-filament::input.wrapper>
                            <x-filament::input>
                                Cor
                            </x-filament::input>

                            <select
                                wire:model="color"
                                class="w-full dark:bg-gray-800 dark:text-white border-gray-300 dark:border-gray-700 rounded-lg"
                            >
                                <option value="">Selecione</option>

                                @foreach($product->colors ?? [] as $color)
                                    <option value="{{ $color }}">{{ $color }}</option>
                                @endforeach
                            </select>
                        </x-filament::input.wrapper>
                    </div>

                    {{-- UPLOAD DE IMAGEM --}}
                    <div>
                        <x-filament::input.wrapper>
                            <x-filament::input>
                                Enviar Imagem (opcional)
                            </x-filament::input>

                            <input
                                type="file"
                                wire:model="uploaded_image"
                                class="w-full text-sm dark:bg-gray-800 dark:text-white border-gray-300 dark:border-gray-700 rounded-lg"
                            />
                        </x-filament::input.wrapper>
                    </div>

                    {{-- BOTÃO DE COMPRA --}}
                    <div class="pt-4">
                        <x-filament::button
                            wire:click="addToCart"
                            color="primary"
                            icon="heroicon-o-shopping-cart"
                            class="w-full py-4 text-lg font-semibold"
                        >
                            Adicionar ao carrinho
                        </x-filament::button>
                    </div>

                </div>

            </x-filament::section>

        </div>

    </div>

</x-filament::page>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new Swiper(".mySwiper", {
                loop: true,
                spaceBetween: 10,
                slidesPerView: 1,
                pagination: { el: ".swiper-pagination", clickable: true },
                navigation: { nextEl: ".swiper-button-next", prevEl: ".swiper-button-prev" },
            });
        });
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
@endpush


