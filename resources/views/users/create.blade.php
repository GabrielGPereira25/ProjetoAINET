<x-layouts::main-content :title="__('Criar Utilizador')" heading="Novo Utilizador" subheading="Crie uma nova conta de Administrador ou Funcionário.">
    
    <div class="max-w-2xl">
        <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data" class="flex flex-col gap-6">
            @csrf

            {{-- Fotografia de Perfil (Com Preview em Alpine.js) --}}
            <div x-data="{ photoPreview: null }" class="flex items-center gap-6">
                <div class="shrink-0 relative">
                    {{-- Preview da imagem escolhida --}}
                    <img x-show="photoPreview" x-bind:src="photoPreview" class="h-16 w-16 object-cover rounded-full border border-zinc-600" alt="Preview" style="display: none;">
                    
                    {{-- Ícone padrão quando ainda não há foto --}}
                    <div x-show="!photoPreview" class="flex h-16 w-16 items-center justify-center rounded-full bg-zinc-800 border border-zinc-700 text-zinc-400">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
                
                <div class="flex-1">
                    <label class="block text-sm font-medium text-zinc-300 mb-1">Fotografia de Perfil (Opcional)</label>
                    <input 
                        type="file" 
                        name="image_file" 
                        accept="image/*" 
                        x-on:change="
                            const file = $event.target.files[0];
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = (e) => { photoPreview = e.target.result; };
                                reader.readAsDataURL(file);
                            } else {
                                photoPreview = null;
                            }
                        "
                        class="block w-full text-sm text-zinc-400 file:mr-4 file:rounded-full file:border-0 file:bg-zinc-700 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-zinc-600" 
                    />
                    @error('image_file') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Nome --}}
            <flux:input 
                name="name" 
                label="Nome Completo" 
                value="{{ old('name') }}" 
                required 
                autofocus 
            />

            {{-- Email --}}
            <flux:input 
                type="email" 
                name="email" 
                label="Endereço de Email" 
                value="{{ old('email') }}" 
                required 
            />

            {{-- Tipo de Utilizador --}}
            <flux:radio.group name="user_type" label="Tipo de Conta" variant="cards" class="flex-col sm:flex-row" required>
                <flux:radio value="F" label="Funcionário" description="Acesso à gestão de encomendas." checked="{{ old('user_type') === 'F' }}" />
                <flux:radio value="A" label="Administrador" description="Acesso total ao sistema." checked="{{ old('user_type') === 'A' }}" />
            </flux:radio.group>

            {{-- Palavra-passe --}}
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <flux:input 
                    type="password" 
                    name="password" 
                    label="Palavra-passe" 
                    required 
                />
                
                <flux:input 
                    type="password" 
                    name="password_confirmation" 
                    label="Confirmar Palavra-passe" 
                    required 
                />
            </div>

            {{-- Botões de Ação --}}
            <div class="flex items-center gap-4 mt-4">
                <flux:button type="submit" variant="primary">Criar Conta</flux:button>
                <flux:button href="{{ route('users.index') }}" variant="subtle">Cancelar</flux:button>
            </div>
        </form>
    </div>

</x-layouts::main-content>