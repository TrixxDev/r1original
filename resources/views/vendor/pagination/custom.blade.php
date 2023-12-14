@if ($paginator->hasPages())
    <div class="col-sm-12 col-md-12">
        <div class="dataTables_paginate paging_simple_numbers" id="DataTables_Table_0_paginate">
            <ul class="pagination">

            @if ($paginator->onFirstPage())
                <li class="paginate_button page-item previous disabled" id="DataTables_Table_0_previous">
                    <a href="#" aria-controls="DataTables_Table_0" data-dt-idx="0" tabindex="0" class="page-link">Atpakaļ</a>
                </li>
            @else
                    <li class="paginate_button page-item previous" id="DataTables_Table_0_previous">
                        <a href="{{ $paginator->previousPageUrl() }}" aria-controls="DataTables_Table_0" data-dt-idx="0" tabindex="0" class="page-link">Atpakaļ</a>
                    </li>
            @endif



{{--            @foreach ($elements as $element)--}}

{{--                @if (is_array($element))--}}
{{--                    @foreach ($element as $page => $url)--}}
{{--                        @if ($page == $paginator->currentPage())--}}
{{--                            <li class="paginate_button page-item active">--}}
{{--                                <span aria-controls="DataTables_Table_0" style="pointer-events: none;" data-dt-idx="{{ $page }}" tabindex="0" class="page-link">{{ $page }}</span>--}}
{{--                            </li>--}}
{{--                        @else--}}
{{--                            <li class="paginate_button page-item">--}}
{{--                                <a href="{{ $url }}" aria-controls="DataTables_Table_0" data-dt-idx="{{ $page }}" tabindex="0" class="page-link">{{ $page }}</a>--}}
{{--                            </li>--}}
{{--                        @endif--}}
{{--                    @endforeach--}}
{{--                @endif--}}
{{--            @endforeach--}}



            @if ($paginator->hasMorePages())
                <li class="paginate_button page-item next" id="DataTables_Table_0_next">
                    <a href="{{ $paginator->nextPageUrl() }}" aria-controls="DataTables_Table_0" data-dt-idx="5" tabindex="0" class="page-link">Uz priekšu</a>
                </li>
            @else
                <li class="paginate_button page-item next disabled" id="DataTables_Table_0_next">
                    <a href="#" aria-controls="DataTables_Table_0" data-dt-idx="5" tabindex="0" class="page-link">Uz priekšu</a>
                </li>
            @endif
            </ul>
        </div>
    </div>
@endif
