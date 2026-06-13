<x-layouts::main-content :title="__('Criar Utilizador')" heading="Novo Utilizador" subheading="Crie uma nova conta de Administrador ou Funcionário.">
    
    <div class="max-w-2xl">
        <form method="POST" action="{{ route('users.store') }}" class="flex flex-col gap-6">
            @csrf

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
                <flux:radio value="F" label="Funcionário" description="" checked="{{ old('user_type') === 'F' }}" />
                <flux:radio value="A" label="Administrador" description="" checked="{{ old('user_type') === 'A' }}" />
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