<style>
svg.icon {
    width: 1rem;
    height: 1rem;
}


@-webkit-keyframes spin {
    from {
        -ms-transform: rotate(0deg);
        -moz-transform: rotate(0deg);
        -webkit-transform: rotate(0deg);
        -o-transform: rotate(0deg);
        transform: rotate(0deg);
    }
    to {
        -ms-transform: rotate(360deg);
        -moz-transform: rotate(360deg);
        -webkit-transform: rotate(360deg);
        -o-transform: rotate(360deg);
        transform: rotate(360deg);
    }
}

@keyframes spin {
    from {
        -ms-transform: rotate(0deg);
        -moz-transform: rotate(0deg);
        -webkit-transform: rotate(0deg);
        -o-transform: rotate(0deg);
        transform: rotate(0deg);
    }
    to {
        -ms-transform: rotate(360deg);
        -moz-transform: rotate(360deg);
        -webkit-transform: rotate(360deg);
        -o-transform: rotate(360deg);
        transform: rotate(360deg);
    }
}

.spin {
    -webkit-animation: spin 2s linear infinite;
    -moz-animation: spin 2s linear infinite;
    -ms-animation: spin 2s linear infinite;
    -o-animation: spin 2s linear infinite;
    animation: spin 2s linear infinite;
}

.form-control-with-icon {
    position: relative;
}
.form-control-with-icon .icon-wrapper {
    display: flex;
    align-items: center;
    jusify-content: center;
    position: absolute;
    top: 0;
    left: 0.75rem;
    bottom: 0;

}
.form-control-with-icon .icon-wrapper .icon {
    fill: #6b7280;
}
.form-control-with-icon .form-control {
    padding-left: 2.25rem!important;
    font-size: 0.875rem;
    border-radius: 9999px;
}

.card-bg-secondary {
    background: #f3f4f6;
}


.control-action svg {
    fill: #d1d5db;
    width: 1.2rem;
    height: 1.2rem;
}
.control-action svg:hover {
    fill: #7c3aed;
}
.info-icon {
    fill: #d1d5db;
}

</style>
