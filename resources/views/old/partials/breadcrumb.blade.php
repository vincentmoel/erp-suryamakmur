<div class="card bg-light-info shadow-none position-relative overflow-hidden">
    <div class="card-body px-4 py-3" style="padding: 40px !important">
        <div class="row align-items-center">
            <div class="col-9">
                <h4 class="fw-semibold mb-8">{{ $title }}</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a class="text-muted" href="{{ route('dashboard') }}">Home</a>
                        </li>
                        @foreach ($breadcrumbs as $breadcrumb)
                            @if(isset($breadcrumb['link']))
                                <li class="breadcrumb-item">
                                    <a class="text-muted" href="{{ $breadcrumb['link'] }}">{{ $breadcrumb['title'] }}</a>
                                </li>
                            @else
                                <li class="breadcrumb-item">
                                    {{ $breadcrumb['title'] }}
                                </li>
                            @endif
                        @endforeach
                    </ol>
                </nav>
            </div>
            <div class="col-3">
                <div class="text-center mb-n5">
                    {{-- <img src="{{ asset('src/images/logos/traffic-light.png') }}" alt=""
                        class="img-fluid mb-n4" /> --}}
                </div>
            </div>
        </div>
    </div>
</div>