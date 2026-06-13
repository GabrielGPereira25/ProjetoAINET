<x-layouts::main-content :title="__('Editar Utilizador')" heading="Editar Conta" subheading="Altere os dados da conta de Administrador ou Funcionário.">
    
    <div class="max-w-2xl">
        
        <form method="POST" action="{{ route('users.update', $user) }}" enctype="multipart/form-data" class="flex flex-col gap-6">
            @csrf
            @method('PATCH')

            <div x-data="{ photoPreview: null }" class="flex items-center gap-6">
                <div class="shrink-0 relative">
                    
                    <img x-show="photoPreview" x-bind:src="photoPreview" class="h-16 w-16 object-cover rounded-full border border-zinc-600" alt="Preview" style="display: none;">
                    
                    @if ($user->photo_url)
                        <img x-show="!photoPreview" class="h-16 w-16 object-cover rounded-full border border-zinc-600" src="{{ asset('storage/photos/' . $user->photo_url) }}" alt="Avatar">
                    @else
                    
                        <div x-show="!photoPreview" class="flex h-16 w-16 items-center justify-center rounded-full bg-zinc-800 border border-zinc-700 text-zinc-400">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                
                <div class="flex-1">
                    <label class="block text-sm font-medium text-zinc-300 mb-1">Fotografia de Perfil</label>
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
                value="{{ old('name', $user->name) }}" 
                required 
            />

            {{-- Email --}}
            <flux:input 
                type="email" 
                name="email" 
                label="Endereço de Email" 
                value="{{ old('email', $user->email) }}" 
                required 
            />

            {{-- Tipo de Utilizador --}}
            <flux:radio.group name="user_type" label="Tipo de Conta" variant="cards" class="flex-col sm:flex-row" required>
                <flux:radio value="F" label="Funcionário" description="Acesso à gestão de encomendas." checked="{{ old('user_type', $user->user_type) === 'F' }}" />
                <flux:radio value="A" label="Administrador" description="Acesso total ao sistema." checked="{{ old('user_type', $user->user_type) === 'A' }}" />
            </flux:radio.group>

            {{-- Palavra-passe (Opcional) --}}
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <flux:input 
                    type="password" 
                    name="password" 
                    label="Nova Palavra-passe (Opcional)" 
                    description="Deixe em branco se não quiser alterar."
                />
                
                <flux:input 
                    type="password" 
                    name="password_confirmation" 
                    label="Confirmar Nova Palavra-passe" 
                />
            </div>

            {{-- Botões de Ação --}}
            <div class="flex items-center gap-4 mt-4">
                <flux:button type="submit" variant="primary">Guardar Alterações</flux:button>
                <flux:button href="{{ route('users.index') }}" variant="subtle">Cancelar</flux:button>
            </div>
        </form>

        {{-- Botão de Eliminar Imagem --}}
        @if($user->photo_url)
            <hr class="my-8 border-zinc-200 dark:border-zinc-700" />
            <form method="POST" action="{{ route('users.photo.destroy', $user) }}" onsubmit="return confirm('Tem a certeza que deseja remover esta fotografia?');">
                @csrf
                @method('DELETE')
                <flux:button type="submit" variant="danger" icon="trash">
                    Remover Fotografia
                </flux:button>
            </form>
        @endif
    </div>

</x-layouts::main-content>