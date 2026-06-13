<x-layouts::main-content :title="__('User Management')" heading="Gestão de Utilizadores" subheading="">
    <div class="flex w-full flex-col gap-6">

        {{-- TOPO: Filtros e Botão Criar --}}
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
            
            {{-- Formulário de Filtros --}}
            <form method="GET" action="{{ route('users.index') }}" class="flex w-full flex-wrap items-end gap-3 md:w-auto">
                <div class="w-full sm:w-auto">
                    <flux:input name="name" value="{{ $filterByName }}" placeholder="Filtrar por nome..." class="w-full sm:w-48" />
                </div>
                
                <div class="w-full sm:w-auto">
                    <flux:input name="email" value="{{ $filterByEmail }}" placeholder="Filtrar por email..." class="w-full sm:w-48" />
                </div>

                <div class="w-full sm:w-auto">
                    <flux:select name="user_type" class="w-full sm:w-40">
                        <option value="">Todos os Tipos</option>
                        <option value="A" {{ $filterByUserType === 'A' ? 'selected' : '' }}>Administrador</option>
                        <option value="F" {{ $filterByUserType === 'F' ? 'selected' : '' }}>Funcionário</option>
                        <option value="C" {{ $filterByUserType === 'C' ? 'selected' : '' }}>Cliente</option>
                    </flux:select>
                </div>

                <div class="w-full sm:w-auto">
                    <flux:select name="blocked" class="w-full sm:w-36">
                        <option value="">Qualquer Estado</option>
                        <option value="0" {{ $filterByBlocked === '0' ? 'selected' : '' }}>Ativos</option>
                        <option value="1" {{ $filterByBlocked === '1' ? 'selected' : '' }}>Bloqueados</option>
                    </flux:select>
                </div>

                <div class="flex gap-2">
                    <flux:button type="submit" variant="primary">Filtrar</flux:button>
                    <flux:button href="{{ route('users.index') }}" variant="subtle">Limpar</flux:button>
                </div>
            </form>

            {{-- Botão Criar Novo (Só para Admins/Funcionários, como o admin não gere perfis de clientes diretamente aqui) --}}
            <flux:button href="{{ route('users.create') }}" variant="primary" icon="plus">
                Criar Utilizador
            </flux:button>
        </div>

        {{-- TABELA DE UTILIZADORES --}}
        <div class="rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-800/50">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-zinc-600 dark:text-zinc-400">
                    <thead class="border-b border-zinc-200 bg-zinc-50 text-xs uppercase text-zinc-500 dark:border-zinc-700 dark:bg-zinc-800/80 dark:text-zinc-400">
                        <tr>
                            <th class="px-4 py-3 font-medium">Foto</th>
                            <th class="px-4 py-3 font-medium">Nome / Email</th>
                            <th class="px-4 py-3 font-medium">Tipo</th>
                            <th class="px-4 py-3 font-medium">Estado</th>
                            <th class="px-4 py-3 font-medium text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse ($users as $user)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/20">
                                {{-- Foto --}}
                                <td class="px-4 py-3">
                                    <div class="h-10 w-10 shrink-0">
                                        @if ($user->photo_url)
                                            <img class="h-10 w-10 rounded-full object-cover border border-zinc-300 dark:border-zinc-600" src="{{ asset('storage/photos/' . $user->photo_url) }}" alt="{{ $user->name }}">
                                        @else
                                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-zinc-200 text-sm font-semibold text-zinc-500 dark:bg-zinc-700 dark:text-zinc-300">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                {{-- Nome e Email --}}
                                <td class="px-4 py-3">
                                    <div class="font-medium text-zinc-900 dark:text-white">{{ $user->name }}</div>
                                    <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $user->email }}</div>
                                </td>

                                {{-- Tipo de Conta --}}
                                <td class="px-4 py-3">
                                    @if ($user->user_type === 'A')
                                        <flux:badge size="sm" variant="pill" color="danger">Admin</flux:badge>
                                    @elseif ($user->user_type === 'F')
                                        <flux:badge size="sm" variant="pill" color="warning">Funcionário</flux:badge>
                                    @else
                                        <flux:badge size="sm" variant="pill" color="success">Cliente</flux:badge>
                                    @endif
                                </td>

                                {{-- Estado (Ativo/Bloqueado) --}}
                                <td class="px-4 py-3">
                                    @if ($user->blocked)
                                        <flux:badge size="sm" variant="pill" color="danger">Bloqueado</flux:badge>
                                    @else
                                        <flux:badge size="sm" variant="pill" color="success">Ativo</flux:badge>
                                    @endif
                                </td>

                                {{-- Ações --}}
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        
                                        {{-- Só pode editar se NÃO for Cliente --}}
                                        @if ($user->user_type !== 'C')
                                            <flux:button size="sm" variant="subtle" icon="pencil" href="{{ route('users.edit', $user) }}" title="Editar" />
                                        @endif

                                        {{-- Botão Bloquear/Desbloquear --}}
                                        <form method="POST" action="{{ route('users.block_unblock', $user) }}" class="inline-block">
                                            @csrf
                                            @method('PATCH')
                                            <flux:button type="submit" size="sm" variant="subtle" icon="{{ $user->blocked ? 'lock-open' : 'lock-closed' }}" title="{{ $user->blocked ? 'Desbloquear' : 'Bloquear' }}" />
                                        </form>

                                        {{-- Botão Eliminar --}}
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline-block" onsubmit="return confirm('Tem a certeza que deseja remover esta conta?');">
                                            @csrf
                                            @method('DELETE')
                                            <flux:button type="submit" size="sm" variant="subtle" icon="trash" class="text-red-500 hover:text-red-600" title="Eliminar" />
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                    Nenhum utilizador encontrado com estes filtros.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Paginação --}}
            <div class="border-t border-zinc-200 px-4 py-3 dark:border-zinc-700">
                {{ $users->links() }}
            </div>
        </div>

    </div>
</x-layouts::main-content>