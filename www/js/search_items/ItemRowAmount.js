class ItemRowAmount extends React.Component
{
    constructor(props) 
    {
        super(props);
    }
    
    render = () => 
    {
        var item_amount = 'item_amount_' + this.props.keyName;
        return (
                <td>
                <input type="number" name={item_amount} min="1" step="1" required="1" defaultValue={this.props.amount} onChange={this.changed} />
                </td>
                );
    }
    
    changed = (event) => 
    {
        var row_id = parseInt($(event.target).attr('name').replace('item_amount_', ''));
        var item_amount = $(event.target).val();
        
        var change_item_amount_event = new CustomEvent(
            'change_item_amount', 
            {detail: 
                {
                    row_id: row_id, 
                    item_amount: item_amount
                }
            }
        );
        document.getElementById('output_form').dispatchEvent(change_item_amount_event);
    }
    
}