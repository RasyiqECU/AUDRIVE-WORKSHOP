{{--
  x-ui.pagination — AUDrive Pagination Component
  Usage: <x-ui.pagination :paginator="$cars" />
--}}
@props(['paginator'])


@if($paginator->hasPages())
    <nav class="flex items-center justify-between border-t border-gunmetal pt-4" role="navigation">
        <div class="flex items-center gap-2">
            <span class="font-mono text-xs text-silver-dim">
                Menampilkan {{ $paginator->firstItem() }} - {{ $paginator->lastItem() }} dari {{ $paginator->total() }}
            </span>
        </div>


        <div class="flex items-center gap-1">
            {{-- Previous --}}
            @if($paginator->onFirstPage())
                <span class="px-3 py-1.5 font-heading text-xs text-silver-dim/40 cursor-not-allowed">← Prev</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1.5 font-heading text-xs text-silver-dim hover:text-giallo border border-gunmetal hover:border-giallo/30 transition-premium">← Prev</a>
            @endif


            {{-- Pages --}}
            @foreach($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                @if($page == $paginator->currentPage())
                    <span class="px-3 py-1.5 font-heading text-xs bg-giallo text-nero font-bold">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="px-3 py-1.5 font-heading text-xs text-silver-dim hover:text-giallo border border-gunmetal hover:border-giallo/30 transition-premium">{{ $page }}</a>
                @endif
            @endforeach


            {{-- Next --}}
            @if($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1.5 font-heading text-xs text-silver-dim hover:text-giallo border border-gunmetal hover:border-giallo/30 transition-premium">Next →</a>
            @else
                <span class="px-3 py-1.5 font-heading text-xs text-silver-dim/40 cursor-not-allowed">Next →</span>
            @endif
        </div>
    </nav>
@endif



