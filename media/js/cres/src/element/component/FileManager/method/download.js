export const downloadMethod = (items) => {
    items.forEach((item, index) => {
        let data = this.defaultParameters();
        data.file = item.name;
        let token = this.getUrlParam('token');
        if (token) {
            data.token = token;
        }

        setTimeout(() => {
            window.location.href = this.settings.connectorUrl + '/download?' + $.param(data);
        }, index * 100);
    });
};
