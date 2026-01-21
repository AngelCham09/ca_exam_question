<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Investor: ') }} {{ $investor->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @error('api')
                <div class="mb-4 p-4 text-sm text-red-800 border border-red-300 rounded-lg bg-red-50" role="alert">
                    <span class="font-medium">Update Failed</span> {{ $message }}
                </div>
            @enderror

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6"  x-data="{ loading: false }">
                    <form action="{{ route('investors.update', $investor->id) }}" method="POST" @submit="loading = true">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" name="name" type="text" class="block mt-1 w-full"
                                :value="old('name', $investor->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="email" :value="__('Email Address')" />
                            <x-text-input id="email" name="email" type="email" class="block mt-1 w-full"
                                :value="old('email', $investor->email)" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="mb-6">
                            <x-input-label for="contact_number" :value="__('Contact Number')" />
                            <x-text-input id="contact_number" name="contact_number" type="text"
                                class="block mt-1 w-full" :value="old('contact_number', $investor->contact_number)" required />
                            <x-input-error :messages="$errors->get('contact_number')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end gap-4 border-t pt-6">
                            <a href="{{ route('investors.index') }}"
                                class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition duration-150 ease-in-out">
                                {{ __('Cancel') }}
                            </a>

                            <x-primary-button x-bind:disabled="loading" class="disabled:opacity-50">
                                <span x-show="!loading">{{ __('Update Investor') }}</span>
                                <span x-show="loading" style="display: none;">{{ __('Processing...') }}</span>
                            </x-primary-button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
