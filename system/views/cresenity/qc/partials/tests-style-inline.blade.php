@php
    $brandGray = CColor::create('#e9ecee')->toCssStyle();
    $brandDarker = CColor::create('#444444')->toCssStyle();
    $brandDarkest = CColor::create('#000000')->toCssStyle();
    $brandClear = CColor::create('#fdfdfd')->toCssStyle();
    $brandSuccess = CColor::create('#6DA34D')->toCssStyle();
    $brandWarning = CColor::create('#EA844D')->toCssStyle();
    $brandDanger = CColor::create('#931621')->toCssStyle();
    $brandInfo = CColor::create('#4d80b2')->toCssStyle();
    $brandPrimary = CColor::create('#2654b2')->toCssStyle();
    $brandSecondary = CColor::create('#e9ecee')->toCssStyle();
    $brandGrayDarker = CColor::create($brandGray)
        ->darken(60)
        ->toCssStyle();
    $brandInfoLight = CColor::create($brandInfo)
        ->lighten(20)
        ->toCssStyle();
    $brandDangerLight = CColor::create($brandDanger)
        ->lighten(20)
        ->toCssStyle();
    $brandSuccessLight = CColor::create($brandSuccess)
        ->lighten(20)
        ->toCssStyle();
    $brandClearLight = CColor::create($brandClear)
        ->lighten(20)
        ->toCssStyle();
    $brandGrayLight = CColor::create($brandGray)
        ->lighten(20)
        ->toCssStyle();
    $brandDarkerLight = CColor::create($brandDarker)
        ->lighten(40)
        ->toCssStyle();
@endphp
<style>
    .capp-testing-container th {
        font-size: 1.3em;
    }

    .capp-testing-container .table>tbody>tr>td {
        vertical-align: middle;
    }

    .capp-testing-container .toolbar {
        background-color: {{ $brandGray }};
        height: 60px;
        padding: 15px;
        vertical-align: middle;
        border: 1px;
        border-color: {{ $brandGrayDarker }};
        border-style: solid;
    }

    .capp-testing-container .btn-square {
        height: 30px;
        width: auto;
        padding: 5px;
        vertical-align: middle;
        padding-left: 10px;
    }

    .capp-testing-container .search-group {
        padding: 5px;
        margin-top: -9px;
        width: 100%;
    }

    .capp-testing-container ::-webkit-input-placeholder,
    .capp-testing-container :-moz-placeholder,
    .capp-testing-container ::-moz-placeholder,
    .capp-testing-container :-ms-input-placeholder {
        color: white;
        opacity: 0.3;
    }

    .capp-testing-container .state {
        color: {{ $brandClear }};
    }

    .capp-testing-container .state-failed {
        background-color: {{ $brandDangerLight }};
    }

    .capp-testing-container .state-running {
        background-color: {{ $brandInfoLight }};
    }

    .capp-testing-container .state-ok {
        background-color: {{ $brandSuccessLight }};
    }

    .capp-testing-container .state-disabled {
        background-color: {{ $brandClearLight }};
        color: {{ $brandDarker }};
    }

    .capp-testing-container .state-idle {
        background-color: {{ $brandDarkerLight }};
    }

    .capp-testing-container .state-queued {
        background-color: {{ $brandGrayLight }};
        color: {{ $brandDarker }};
    }

    .capp-testing-container .badge {
        padding: 0.6em .8em .7em;
    }

    .capp-testing-container .table-test-name {
        font-weight: 700 !important;
        color: {{ $brandDarkest }};
    }

    .capp-testing-container .table-test-path {
        font-size: 0.7rem;
        font-weight: 100 !important;
        color: {{ $brandGrayDarker }};
    }

    .capp-testing-container .table-link {
        cursor: pointer;
    }

    .capp-testing-container .table-header {
        vertical-align: middle;
        margin-bottom: 15px;
        font-weight: 800;
    }

    .capp-testing-container .table-header .title {
        font-size: 1.8em;
    }
    .capp-testing-container .terminal {
        background-color: {{ $brandDarkest }};
    }

    .capp-testing-container .dim {
        filter: alpha(opacity=40);
        /* internet explorer */
        -khtml-opacity: 0.4;
        /* khtml, old safari */
        -moz-opacity: 0.4;
        /* mozilla, netscape */
        opacity: 0.4;
        /* fx, safari, opera */
    }

    .capp-testing-container .pale {
        filter: alpha(opacity=10);
        /* internet explorer */
        -khtml-opacity: 0.1;
        /* khtml, old safari */
        -moz-opacity: 0.1;
        /* mozilla, netscape */
        opacity: 0.1;
        /* fx, safari, opera */
    }

</style>
