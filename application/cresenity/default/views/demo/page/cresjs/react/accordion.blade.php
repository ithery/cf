<style>
.accordion {
  max-width: 600px;
  margin: 2rem auto;
  color:#fff;
}

.accordion-title {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  cursor: pointer;
  background-color: #CC131F;
}

.accordion-title:hover {
  background-color: #FF131F;
}

.accordion-title,
.accordion-content {
  padding: 1rem;
}

.accordion-content {
  background-color: #CC131F;
}

</style>


@CAppStartReact
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

@CAppEndReact('Accordion' ,['data'=>$data])
