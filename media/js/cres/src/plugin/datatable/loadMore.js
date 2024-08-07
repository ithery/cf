// https://www.gyrocode.com/articles/jquery-datatables-load-more-button/
const initDatatableLoadMore = () => {
    if (jQuery) {
        (function ($) {
            if ($.fn.dataTable) {
                //
                // Loads only portion of the data
                //
                $.fn.dataTable.pageLoadMore = function (opts) {
                    // Configuration options
                    let conf = $.extend(
                        {
                            url: '', // script url
                            data: null, // function or object with parameters to send to the server
                            // matching how `ajax.data` works in DataTables
                            method: 'GET' // Ajax HTTP method
                        },
                        opts
                    );

                    let cacheLastRequest = null;
                    let cacheLastJson = null;

                    return function (request, drawCallback, settings) {
                        if (!settings.pageLoadMore) {
                            let api = new $.fn.dataTable.Api(settings);
                            let info = api.page.info();

                            settings.pageLoadMore = { pageLength: info.length };
                        }

                        let pageResetMore = false;

                        if (cacheLastRequest) {
                            if (
                                JSON.stringify(request.order) !==
                                    JSON.stringify(cacheLastRequest.order) ||
                                JSON.stringify(request.columns) !==
                                    JSON.stringify(cacheLastRequest.columns) ||
                                JSON.stringify(request.search) !==
                                    JSON.stringify(cacheLastRequest.search)
                            ) {
                                pageResetMore = true;
                            }
                        }

                        // Store the request for checking next time around
                        cacheLastRequest = $.extend(true, {}, request);

                        if (pageResetMore) {
                            cacheLastJson = null;
                            request.length = settings.pageLoadMore.pageLength;
                        }

                        request.start =
                            request.length - settings.pageLoadMore.pageLength;
                        request.length = settings.pageLoadMore.pageLength;

                        // Provide the same `data` options as DataTables.
                        if ($.isFunction(conf.data)) {
                            // As a function it is executed with the data object as an arg
                            // for manipulation. If an object is returned, it is used as the
                            // data object to submit
                            let d = conf.data(request);
                            if (d) {
                                $.extend(request, d);
                            }
                        } else if ($.isPlainObject(conf.data)) {
                            // As an object, the data given extends the default
                            $.extend(request, conf.data);
                        }

                        settings.jqXHR = $.ajax({
                            type: conf.method,
                            url: conf.url,
                            data: request,
                            dataType: 'json',
                            cache: false,
                            success: function (json) {
                                if (cacheLastJson) {
                                    json.data = cacheLastJson.data.concat(
                                        json.data
                                    );
                                }

                                cacheLastJson = $.extend(true, {}, json);

                                drawCallback(json);
                            }
                        });
                    };
                };

                //
                // Resets page length to initial value on the next draw
                //
                $.fn.dataTable.Api.register('page.resetMore()', function () {
                    return this.iterator('table', function (settings) {
                        // eslint-disable-next-line no-undef
                        api.page.len(settings.pageLoadMore.pageLength);
                    });
                });

                //
                // Determines whether there is more data available
                //
                $.fn.dataTable.Api.register('page.hasMore()', function () {
                    let api = this;
                    let info = api.page.info();
                    return info.pages > 1 ? true : false;
                });

                //
                // Loads more data
                //
                $.fn.dataTable.Api.register('page.loadMore()', function () {
                    return this.iterator('table', function (settings) {
                        let api = this;
                        let info = api.page.info();

                        if (info.pages > 1) {
                            if (!settings.pageLoadMore) {
                                settings.pageLoadMore = {
                                    pageLength: info.length
                                };
                            }

                            api.page
                                .len(
                                    info.length +
                                        settings.pageLoadMore.pageLength
                                )
                                .draw('page');
                        }
                    });
                });
            }
        }(jQuery));
    }
};

export default initDatatableLoadMore;
