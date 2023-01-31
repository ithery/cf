import DateFormatter from './DateFormatter';
import php from '../php';
let dateFormatter = new DateFormatter();

const formatter = {
    formatDate: (date, format)=>{
        let vFormat = format ?? capp.format.date;
        return dateFormatter.formatDate(date, vFormat);
    },
    unformatDate: (date, format) => {
        let vFormat = format ?? capp.format.date;
        return dateFormatter.parseDate(date, vFormat);
    },
    formatDatetime: (date, format)=>{
        let vFormat = format ?? capp.format.datetime;
        return dateFormatter.formatDate(date, vFormat);
    },
    unformatDatetime: (date, format)=>{
        let vFormat = format ?? capp.format.datetime;
        return dateFormatter.parseDate(date, vFormat);
    },
    formatCurrency: (x, decimalDigit = null, decimalSeparator = null, thousandSeparator = null, currencyPrefix = null, currencySuffix = null, stripZeroDecimal = null) => {
        decimalSeparator = decimalSeparator ?? capp.format.decimalSeparator;
        thousandSeparator = thousandSeparator ?? capp.format.thousandSeparator;
        decimalDigit = decimalDigit ?? (capp.format.currencyDecimalDigit ?? capp.format.decimalDigit);
        currencySuffix = currencySuffix ?? capp.format.currencySuffix;
        currencyPrefix = currencyPrefix ?? capp.format.currencyPrefix;
        stripZeroDecimal = stripZeroDecimal !== null ? stripZeroDecimal : capp.format.currencyStripZeroDecimal;

        x = '' + php.number_format(x, decimalDigit, decimalSeparator, thousandSeparator);
        if (stripZeroDecimal) {
            if (x.substr((decimalDigit + 1) * -1) === decimalSeparator + '0'.repeat(decimalDigit)) {
                x = x.substr(x, 0, x.length - (decimalDigit + 1));
            }
        }

        return '' + currencyPrefix + x + currencySuffix;
    },
    formatDecimal(x, decimalDigit = null, decimalSeparator = null, thousandSeparator = null, stripZeroDecimal = false) {
        decimalDigit = decimalDigit ?? capp.format.decimalDigit;
        return formatter.formatCurrency(x, decimalDigit, decimalSeparator, thousandSeparator, '', '', stripZeroDecimal);
    },

    formatNumber: (x, decimalSeparator = null, thousandSeparator = null) => {
        return formatter.formatDecimal(x, 0, decimalSeparator, thousandSeparator, true);
    },
    unformatCurrency: (number) => {
        // Build regex to strip out everything except digits, decimal point and minus sign:
        const regex = new RegExp(`[^0-9-${capp.format.decimalSeparator}]`, 'g');
        number = number.replace(/\((?=\d+)(.*)\)/, '-$1');
        number = number.replace(regex, '');

        let type = (php.strpos(number, capp.format.decimalSeparator) === false) ? 'int' : 'float';
        number = php.str_replace([capp.format.decimalSeparator, capp.format.thousandSeparator], ['.', ''], number);
        number = type == 'int' ? parseInt(number) : parseFloat(number);

        if(isNaN(number)) {
            return 0;
        }
        return number;
    },
    unformatNumber: (number) => formatter.unformatCurrency(number)
};

export default formatter;
