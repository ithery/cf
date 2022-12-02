@php
if (!isset($icon)) {
    $icon = 'lnr lnr-cart';
}
if (!isset($label)) {
    $label = '';
}
if (!isset($amount)) {
    $amount = '0';
}
if (!isset($diffAmount)) {
    $diffAmount = null;
}
if(!isset($actionLink)) {
    $actionLink = '';
}
if (isset($action_link)) {
    $actionLink = $action_link;
}
if(!isset($isUp)) {
    $isUp=false;
    if($diffAmount!==null) {
        $isUp = $diffAmount>=0;
    }
}

@endphp

<div class="card card-small mb-3 w-100">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <div class="{{ $icon }}"></div>
            <div class="ml-3">
                @if($label)
                <div class="text-muted small">{{ $label }}</div>
                @endif
                <div class="text-large d-flex align-items-center">
                    {{ $amount }}
                    @if($diffAmount!=null)
                    <span class="d-flex align-items-center ml-2 small {{ $isUp ? 'text-success' : 'text-danger'}}">
                        <i class="{{ $isUp ? 'ti ti-arrow-up' : 'ti ti-arrow-down'}} mr-1"></i>
                        {{ $diffAmount }}
                    </span>
                    @endif
                </div>
            </div>
            @if (strlen($actionLink) > 0)
                <a href="{{ $actionLink }}" class="btn btn-primary ml-auto">
                    <i class="fa fa-cog"></i>
                </a>
            @endif
        </div>
    </div>
</div>
