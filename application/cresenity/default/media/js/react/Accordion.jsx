const AccordionItem = props => {
    const { title, content } = props;
    const [isActive, setIsActive] = React.useState(false);
    return (
        <div className="accordion-item">
            <div className="accordion-title" onClick={() => setIsActive(!isActive)}>
                <div>{title}</div>
                <div>{isActive ? '-' : '+'}</div>
            </div>
            {isActive && <div className="accordion-content">{content}</div>}
        </div>
    );
};
const Accordion = (props) => {
    const { data } = props;
    return (
        <div className="accordion">
            {data.map(({ title, content }, index) => (
                <AccordionItem title={title} content={content} key={index}/>
            ))}
        </div>
    );
};
