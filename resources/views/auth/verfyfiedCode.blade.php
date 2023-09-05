<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        {{ __('Please confirm your Code before continuing.') }}
    </div>

    <form method="POST" action="{{ route('VerifyCode.store') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="code" :value="__('code')" />

            <x-text-input id="code" class="block mt-1 w-full"
                            type="text"
                            name="code"/>

            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
