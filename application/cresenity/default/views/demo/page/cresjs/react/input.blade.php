<style>
    .mw-200 {
        max-width: 200px!important;
    }
</style>
@CAppStartReact
const CounterWrapper = (props) => {
    const { data } = props;
    const [counter, setCounter] = React.useState(data.counter);
    const incrementCounter = () => {
        setCounter(parseInt(counter)+1)
    };
    const decrementCounter = () => {
        setCounter(parseInt(counter)-1)
    };
    const handleChange = (e)=> {
        setCounter(parseInt(e.target.value) ? parseInt(e.target.value) :  0);
    }
    return (
        <div className="counter-wrapper">
            <div className="d-flex">
                <button className="btn btn-primary" onClick={incrementCounter}>Add Counter</button>
                <input className="mw-200" type="text" value={counter} onChange={handleChange}/>
                <button className="btn btn-primary" onClick={decrementCounter}>Remove Counter</button>
            </div>

            <div className="p-3 b-1">
                Counter: { counter }
            </div>
        </div>
    );
};

@CAppEndReact('CounterWrapper' ,['data'=>$data])
