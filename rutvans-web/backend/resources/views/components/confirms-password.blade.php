@props([
    'title' => __('Confirm Password'),
    'content' => __('For your security, please confirm your password to continue.'),
    'button' => __('Confirmar'),
    'color' => 'primary' // Bootstrap color: primary, secondary, danger, etc.
])

@php
    $confirmableId = md5($attributes->wire('then'));
@endphp

<span
    {{ $attributes->wire('then') }}
    x-data
    x-ref="span"
    x-on:click="$wire.startConfirmingPassword('{{ $confirmableId }}')"
    x-on:password-confirmed.window="setTimeout(() => $event.detail.id === '{{ $confirmableId }}' && $refs.span.dispatchEvent(new CustomEvent('then', { bubbles: false })), 250);"
    class="text-{{ $color }} fw-semibold text-decoration-underline"
    style="cursor: pointer;"
>
    {{ $slot }}
</span>

@once
<x-dialog-modal wire:model.live="confirmingPassword" class="modal-dialog-centered">
    <x-slot name="title">
        <h5 class="modal-title">
            {{ $title }}
        </h5>
    </x-slot>

    <x-slot name="content">
        <p class="mb-3">
            {{ $content }}
        </p>

        <div x-data="{}" x-on:confirming-password.window="setTimeout(() => $refs.confirmable_password.focus(), 250)">
            <input
                type="password"
                class="form-control"
                placeholder="{{ __('Contraseña') }}"
                autocomplete="current-password"
                x-ref="confirmable_password"
                wire:model="confirmablePassword"
                wire:keydown.enter="confirmPassword"
            />
            <x-input-error for="confirmable_password" class="text-danger small mt-1" />
        </div>
    </x-slot>

    <x-slot name="footer" class="d-flex justify-content-end gap-2">
        <button
            type="button"
            wire:click="stopConfirmingPassword"
            wire:loading.attr="disabled"
            class="btn btn-danger"
        >
            {{ __('Cancelar') }}
        </button>

        <button
            type="button"
            dusk="confirm-password-button"
            wire:click="confirmPassword"
            wire:loading.attr="disabled"
            class="btn btn-{{ $color }}"
        >
            {{ $button }}
        </button>
    </x-slot>
</x-dialog-modal>
@endonce
