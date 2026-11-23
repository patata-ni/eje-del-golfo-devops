@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem;">
        
        <!-- Botones para móviles (Siguiente/Anterior) -->
        <div style="display: flex; justify-content: space-between; flex-grow: 1; sm:hidden;">
            @if ($paginator->onFirstPage())
                <span style="color: #ccc; padding: 0.5rem 1rem; border: 1px solid #eee; border-radius: 6px;">Anterior</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" style="color: var(--verde-bosque); padding: 0.5rem 1rem; border: 1px solid #ddd; border-radius: 6px; text-decoration: none;">Anterior</a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" style="color: var(--verde-bosque); padding: 0.5rem 1rem; border: 1px solid #ddd; border-radius: 6px; text-decoration: none;">Siguiente</a>
            @else
                <span style="color: #ccc; padding: 0.5rem 1rem; border: 1px solid #eee; border-radius: 6px;">Siguiente</span>
            @endif
        </div>

        <!-- Escritorio: Texto y botones -->
        <div style="display: none; sm:flex; align-items: center; justify-content: space-between; width: 100%;">
            <div>
                <p style="color: #666; font-size: 0.85rem; margin: 0;">
                    Mostrando <span style="font-weight: 600;">{{ $paginator->firstItem() }}</span> 
                    a <span style="font-weight: 600;">{{ $paginator->lastItem() }}</span> 
                    de <span style="font-weight: 600;">{{ $paginator->total() }}</span> resultados
                </p>
            </div>

            <div>
                <span style="display: inline-flex; box-shadow: 0 1px 2px rgba(0,0,0,0.05); border-radius: 8px;">
                    
                    <!-- Botón Anterior -->
                    @if ($paginator->onFirstPage())
                        <span style="padding: 0.4rem 0.8rem; background: #f9f9f9; color: #ccc; border: 1px solid #eee; border-top-left-radius: 8px; border-bottom-left-radius: 8px;">&lsaquo;</span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" style="padding: 0.4rem 0.8rem; background: white; color: var(--verde-bosque); border: 1px solid #eee; border-top-left-radius: 8px; border-bottom-left-radius: 8px; text-decoration: none;">&lsaquo;</a>
                    @endif

                    <!-- Números de página -->
                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span style="padding: 0.4rem 0.8rem; background: white; color: #999; border: 1px solid #eee; border-left: none;">{{ $element }}</span>
                        @endif

                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span style="padding: 0.4rem 0.8rem; background: var(--verde-esmeralda); color: white; border: 1px solid var(--verde-esmeralda); font-weight: bold;">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" style="padding: 0.4rem 0.8rem; background: white; color: var(--verde-bosque); border: 1px solid #eee; border-left: none; text-decoration: none;">{{ $page }}</a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    <!-- Botón Siguiente -->
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" style="padding: 0.4rem 0.8rem; background: white; color: var(--verde-bosque); border: 1px solid #eee; border-top-right-radius: 8px; border-bottom-right-radius: 8px; text-decoration: none;">&rsaquo;</a>
                    @else
                        <span style="padding: 0.4rem 0.8rem; background: #f9f9f9; color: #ccc; border: 1px solid #eee; border-top-right-radius: 8px; border-bottom-right-radius: 8px;">&rsaquo;</span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif