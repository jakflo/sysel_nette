class ItemRowDelete extends React.Component 
{
    constructor(props) 
    {
        super(props);
    }
    
    render = () => 
    {
        return (
                <td>
                    <button type="button" onClick={this.remove}>Odebrat</button>
                </td>
                );
    }
    
    remove = () => 
    {
        this.props.removeCallback(this.props.keyName);
    }
    
}


