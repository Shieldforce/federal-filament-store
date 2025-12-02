<x-filament::page class="!max-w-full !p-0">
    <div class="w-full flex flex-col md:flex-row gap-6">

        {{-- SIDEBAR --}}
        <div class="md:w-[600px] lg:w-[650px] xl:w-[700px] md:flex-none pl-6 filtros-store-ec">
            <x-filament::section class="!max-w-[600px] w-full !rounded-none">
                {{ $this->form }}
            </x-filament::section>
        </div>

        <div class="flex-2 pr-4">
            <div class="gap-4">
                <div
                    x-data="{
                        selected: 0,
                        images: [
                            'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=2070&q=80',
                            'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=2070&q=80',
                            'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=987&q=80',
                            'https://images.unsplash.com/photo-1486870591958-9b9d0d1dda99?auto=format&fit=crop&w=2070&q=80',
                            'https://images.unsplash.com/photo-1485160497022-3e09382fb310?auto=format&fit=crop&w=2070&q=80',
                            'https://images.unsplash.com/photo-1472791108553-c9405341e398?auto=format&fit=crop&w=2137&q=80',
                        ]
                    }"
                    class="relative w-full"
                >

                    {{-- IMAGEM --}}
                    <img
                        style="width: 100%;height: 300px;"
                        class="h-64 w-full object-cover object-center rounded-xl shadow-lg"
                        :src="images[selected]"
                        alt=""
                    />

                    {{-- BOTÃO ESQUERDA --}}
                    <button
                        @click="selected = selected > 0 ? selected - 1 : images.length - 1"
                        class="absolute inset-y-0 left-0 px-2 py-[25%] w-10 group hover:bg-gray-900/50 cursor-pointer"
                    >
                        <span class="hidden group-hover:block text-white text-2xl">
                            &larr;
                        </span>
                    </button>

                    {{-- BOTÃO DIREITA --}}
                    <button
                        @click="selected = selected < images.length - 1 ? selected + 1 : 0"
                        class="absolute inset-y-0 right-0 px-2 py-[25%] w-10 group hover:bg-gray-900/50 cursor-pointer"
                    >
                        <span class="hidden group-hover:block text-white text-2xl">
                            &rarr;
                        </span>
                    </button>

                    {{-- ÍCONES INDICADORES --}}
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

                </div>

            </div>
        </div>
    </div>

</x-filament::page>
