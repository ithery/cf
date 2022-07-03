import DateFormatter from "./DateFormatter";

let dateFormatter = new DateFormatter();

const formatter = {
    formatDate: (date, format)=>{
        let vFormat = format ?? capp.format.date;
        return dateFormatter.formatDate(date, vFormat);
    },
    unformatDate : (date, format) => {
        let vFormat = format ?? capp.format.date;
        return dateFormatter.parseDate(date,vFormat);
    }
}

export default formatter;
