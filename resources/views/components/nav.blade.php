<nav class="bg-slate-900 text-white px-6 py-4 flex items-center justify-between">
    <span class="font-bold tracking-wider">Simple POS</span>
    <div class="flex gap-6">
        <a href="{{ route('pos.create') }}" 
           class="pb-1 transition-colors hover:text-slate-300 {{ request()->routeIs('pos.create') ? 'border-b-2 border-white font-bold' : 'text-slate-400' }}">
            Kasir
        </a>
        <a href="{{ route('transactions.index') }}" 
           class="pb-1 transition-colors hover:text-slate-300 {{ request()->routeIs('transactions.index') ? 'border-b-2 border-white font-bold' : 'text-slate-400' }}">
            Transaksi
        </a>
    </div>
</nav>