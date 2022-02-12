

class InfiniteScroll {
    constructor(options) {
        this.settings = $.extend({
            // These are the defaults.
            reloadUrl: null,
            currentPage: false,
            total: false,
            lastPage: false,
            perPage: false,
            timestamp: false,
            selector: false,
            infiniteScroll: false,
            insideModal: false,
            itemsSelector: false,
            onBlock: false,
            onUnblock: false,
            showLoading: true,
            withSort: false,
            sortElement: false,
            withMasonry: false,
            scrollContainerSelector: null,
            loadingAnimation: null
        }, options);

        this.container = $(this.settings.selector);
        this.currentPage = this.settings.currentPage;
        this.total = this.settings.total;
        this.perPage = this.settings.perPage;
        this.timestamp = this.settings.timestamp;
        this.lastPage = this.settings.lastPage;
        this.withSort = this.settings.withSort;
        this.sortElement = this.settings.sortElement;
        this.url = this.settings.reloadUrl;

        this.isLoading = false;

        if (!this.total) {
            this.total = this.container.attr('data-total');
        }
        if (!this.currentPage) {
            this.currentPage = this.container.attr('data-current-page');
        }
        if (!this.perPage) {
            this.perPage = this.container.attr('data-per-page');
        }
        if (!this.lastPage) {
            this.lastPage = this.container.attr('data-last-page');
        }
        if (!this.timestamp) {
            this.timestamp = this.container.attr('data-timestamp');
        }
        if (this.settings.infiniteScroll) {
            if (!this.container.attr('data-infinite-scroll')) {
                this.container.attr('data-infinite-scroll', '1');

                let windowBody = $(window);
                if (this.settings.insideModal) {
                    windowBody = $(this.container);
                }
                if (typeof this.settings.scrollContainerSelector != 'undefined' && this.settings.scrollContainerSelector) {
                    windowBody = $(this.settings.scrollContainerSelector);
                }

                $(windowBody).scroll(() =>{
                    let theWindow = $(windowBody);
                    let theContainer = $(this.container);
                    let tweak = 75;


                    if (theWindow.scrollTop() >= theContainer.height() - theWindow.height() - tweak) {
                        let currentTotal = theContainer.find(this.settings.itemSelector).length;
                        let reachLastPage = currentTotal >= this.total;
                        if (!reachLastPage) {
                            this.append();
                        }
                    }
                });
            }
        }

        this.container.data('capp-infinite-scroll', this);
    }


    reInit() {
        this.total = this.container.attr('data-total');
        this.currentPage = this.container.attr('data-current-page');
        this.perPage = this.container.attr('data-per-page');
        this.lastPage = this.container.attr('data-last-page');
    }


    addQueryString(url, key, value) {
        key = encodeURI(key);
        value = encodeURI(value);
        let urlArray = url.split('?');
        let queryString = '';
        let baseUrl = urlArray[0];
        if (urlArray.length > 1) {
            queryString = urlArray[1];
        }
        let kvp = queryString.split('&');
        let i = kvp.length;
        let x;
        while (i--) {
            x = kvp[i].split('=');
            if (x[0] == key) {
                x[1] = value;
                kvp[i] = x.join('=');
                break;
            }
        }

        if (i < 0) {
            kvp[kvp.length] = [key, value].join('=');
        }

        queryString = kvp.join('&');
        if (queryString.substr(0, 1) == '&') {
            queryString = queryString.substr(1);
        }
        return baseUrl + '?' + queryString;
    }
    nextPageUrl() {
        let url = this.url;
        let sortBy = '';
        if (this.withSort === true) {
            sortBy = $('#'+this.settings.sortElement).val();
            url = this.addQueryString(url, 'sortBy', sortBy);
        }
        url = this.addQueryString(url, 'page', parseInt(this.currentPage) + 1);
        url = this.addQueryString(url, 'perPage', parseInt(this.perPage));
        if (typeof this.timestamp != 'undefined') {
            url = this.addQueryString(url, 'timestamp', parseInt(this.timestamp));
        }
        return url;
    }
    applyPaginationAttr() {
        this.container.attr('data-current-page', this.currentPage);
        this.container.attr('data-last-page', this.lastPage);
        this.container.attr('data-total', this.total);
        this.container.attr('data-per-page', this.perPage);
    }
    applyMasonry() {
        setTimeout(() => {
            this.container.masonry('reloadItems');
            this.container.masonry();
        }, 100);
    }
    showLoading() {
        if (this.settings.showLoading) {
            let loadingContentItem = '<div id="capp-infinite-scroll-loading" class="mx-auto text-center"><div class="sk-wave sk-primary"><div class="sk-rect sk-rect1"></div> <div class="sk-rect sk-rect2"></div> <div class="sk-rect sk-rect3"></div> <div class="sk-rect sk-rect4"></div> <div class="sk-rect sk-rect5"></div></div></div>';
            if (this.settings.loadingAnimation != null) {
                loadingContentItem = this.settings.loadingAnimation;
                if (!$(loadingContentItem).is('#capp-infinite-scroll-loading')) {
                    loadingContentItem = '<div id="capp-infinite-scroll-loading" class="mx-auto text-center">'+this.settings.loadingAnimation+'</div>';
                }
            }
            if (typeof this.settings.onBlock == 'function') {
                this.settings.onBlock();
            } else {
                //                this.container.append('<div id="capp-infinite-scroll-loading" class="mx-auto text-center "><div class="sk-wave sk-primary"><div class="sk-rect sk-rect1"></div> <div class="sk-rect sk-rect2"></div> <div class="sk-rect sk-rect3"></div> <div class="sk-rect sk-rect4"></div> <div class="sk-rect sk-rect5"></div></div></div>');
                this.container.append(loadingContentItem);
                //TB.blockPage();
            }
        }
    }
    hideLoading() {
        if (this.settings.showLoading) {
            if (typeof this.settings.onUnblock == 'function') {
                this.settings.onUnblock();
            } else {
                //TB.unblockPage();
                this.container.find('#capp-infinite-scroll-loading').remove();
            }
        }
    }
    reset() {
        if (!this.isLoading) {
            this.isLoading = true;

            let reloadOptions = {};
            reloadOptions.selector = this.settings.selector;
            reloadOptions.url = this.url;
            reloadOptions.onBlock = () => {
                this.showLoading();
            };
            reloadOptions.onUnblock = () => {
                this.hideLoading();
            };
            reloadOptions.onSuccess = (data) => {
                if (data.dataPagination) {
                    this.currentPage = data.dataPagination.currentPage;
                    this.lastPage = data.dataPagination.lastPage;
                    this.total = data.dataPagination.total;
                    this.perPage = data.dataPagination.perPage;
                } else {
                    this.currentPage++;
                }
                this.applyPaginationAttr();
            };
            reloadOptions.onComplete = () => {
                this.isLoading = false;
            };

            if (!this.isLoading) {
                this.isLoading = true;
                window.cresenity.reload(reloadOptions);
            }
        }
    }
    append() {
        if (!this.isLoading) {
            this.isLoading = true;

            let appendOptions = {};
            appendOptions.selector = this.settings.selector;
            appendOptions.url = this.nextPageUrl();
            appendOptions.onBlock = () => {
                this.showLoading();
            };
            appendOptions.onUnblock = () => {
                this.hideLoading();
            };

            appendOptions.onSuccess = (data) => {
                if (data.dataPagination) {
                    this.currentPage = data.dataPagination.currentPage;
                    this.lastPage = data.dataPagination.lastPage;
                    this.total = data.dataPagination.total;
                    this.perPage = data.dataPagination.perPage;
                } else {
                    this.currentPage++;
                }
                this.applyPaginationAttr();
                if (this.settings.withMasonry) {
                    this.applyMasonry();
                }
            };
            appendOptions.onComplete = () => {
                setTimeout(function () {
                    this.isLoading = false;
                }, 400);
            };


            window.cresenity.append(appendOptions);
        }
    }

    setUrl(url) {
        this.url = url;
    }
}

export default InfiniteScroll;
