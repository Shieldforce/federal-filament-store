<x-filament::page class="!max-w-full !p-0">
    <div class="w-full flex flex-col md:flex-row gap-6">

        {{-- SIDEBAR --}}
        <div class="md:w-[480px] lg:w-[520px] xl:w-[560px] md:flex-none pl-6 config-product-ec">
            <x-filament::section class="!max-w-[480px] w-full !rounded-none">
                <h1 style="font-size: 16pt;">
                    Total do Produto: {{ isset($this->totalPrice)
                        ? "R$" . number_format($this->totalPrice, 2, ',', '.')
                        : null }}
                </h1>

                <br>
                <hr>
                <br>

                <form wire:submit="submit">
                    {{ $this->form }}

                    <br>
                    <hr>

                    <div class="flex flex-col mt-4 gap-4 w-full">

                        {{-- ADICIONAR AO CARRINHO --}}
                        <x-filament::button
                            color="primary"
                            icon="heroicon-o-shopping-cart"
                            class="w-full py-3 text-center"
                            type="submit"
                            wire:click="$set('action', 'addCart')"
                        >
                            Adicionar
                        </x-filament::button>

                        {{-- FINALIZAR COMPRA --}}
                        <x-filament::button
                            color="success"
                            icon="heroicon-o-check-circle"
                            class="w-full py-3 text-center"
                            type="submit"
                            wire:click="$set('action', 'finish')"
                        >
                            Adicionar e Finalizar
                        </x-filament::button>

                    </div>
                </form>
            </x-filament::section>
        </div>

        {{-- CONTEÚDO PRINCIPAL --}}
        <div class="flex-1 pr-6">
            <div class="gap-6">

                {{-- SLIDER --}}
                <div
                    x-data="{
                        selected: 0,
                        images: @js($this->images),
                        interval: null,
                        init() {
                            this.interval = setInterval(() => {
                                this.selected = this.selected < this.images.length - 1
                                    ? this.selected + 1
                                    : 0
                            }, 10000);
                        }
                    }"
                    class="relative w-full"
                >

                    {{-- CONTAINER DA IMAGEM (ALTURA MÁX 400px, SEM CORTE) --}}
                    <div
                        class="relative w-full h-[300px] sm:h-[350px] lg:h-[400px]
                               max-h-[400px]
                               flex items-center justify-center
                               bg-gray-100 dark:bg-gray-900
                               overflow-hidden rounded-xl shadow-lg"
                        style="height: 350px; background: linear-gradient(
                            to top,
                            rgba(0,0,0,0.45),
                            rgba(0,0,0,0),
                            rgba(0,0,0,0.35)
                        );"
                    >
                        <img
                            :src="images[selected]"
                            alt=""
                            class="max-w-full max-h-full object-contain
                                   transition-opacity duration-500"
                            style="height: auto"
                        />
                    </div>

                    {{-- BOTÃO ESQUERDA --}}
                    <button
                        @click="selected = selected > 0 ? selected - 1 : images.length - 1"
                        class="absolute inset-y-0 left-0 px-2 py-[25%] w-10
                               group hover:bg-gray-900/50 cursor-pointer"
                    >
                        <span class="group-hover:block text-white text-3xl">
                            &larr;
                        </span>
                    </button>

                    {{-- BOTÃO DIREITA --}}
                    <button
                        @click="selected = selected < images.length - 1 ? selected + 1 : 0"
                        class="absolute inset-y-0 right-0 px-2 py-[25%] w-10
                               group hover:bg-gray-900/50 cursor-pointer"
                    >
                        <span class="group-hover:block text-white text-3xl">
                            &rarr;
                        </span>
                    </button>

                    {{-- INDICADORES --}}
                    <div class="absolute bottom-3 w-full flex justify-center gap-2">
                        <template x-for="(image, index) in images" :key="index">
                            <button
                                @click="selected = index"
                                :class="{
                                    'bg-white': selected === index,
                                    'bg-gray-400': selected !== index
                                }"
                                class="h-3 w-3 rounded-full ring-2 ring-white"
                            ></button>
                        </template>
                    </div>

                    <br><br>
                    <hr>
                    <br><br>

                    {!! $this->product['description'] ?? '' !!}
                </div>

            </div>
        </div>
    </div>
</x-filament::page>

@push('scripts')
    <script>
        window.addEventListener('cart-updated', updateCartBadge);
        async function updateCartBadge() {
            const res = await fetch('/admin/cart-count');
            const count = await res.json();
            const badge = document.querySelector(
                '.fi-topbar-item a[href="/admin/ffs-cart"] .fi-badge .truncate'
            );
            if (badge) badge.textContent = count;
        }

        window.addEventListener('redirect-after-delay', redirectAfterDelay);
        function redirectAfterDelay() {
            setTimeout(() => {
                window.location.href = "/admin/ffs-store";
            }, 5000);
        }
    </script>
@endpush
