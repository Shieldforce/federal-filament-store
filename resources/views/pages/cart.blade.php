<x-filament::page class="!max-w-full !p-0">
    <div class="w-full flex flex-col md:flex-row gap-6">

        {{-- SIDEBAR --}}
        <div class="md:w-[480px] lg:w-[520px] xl:w-[560px] md:flex-none pl-6 config-cart-ec">
            <x-filament::section class="!max-w-[480px] w-full !rounded-none">
                <h1 style="font-size: 16pt;">
                    @dd($this->cart)
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
                        <x-filament::button
                            color="success"
                            icon="heroicon-o-check-circle"
                            class="w-full py-3 text-center"
                            type="submit"
                            wire:click="$set('action', 'finish')"
                        >
                            Finalizar Compra
                        </x-filament::button>

                    </div>

                </form>

            </x-filament::section>

        </div>

        <div class="flex-1 pr-6">
            <div class="gap-6">

                gfdgdgggdfg

            </div>
        </div>
    </div>

</x-filament::page>

@push('scripts')

@endpush
