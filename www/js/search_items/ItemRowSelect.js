class ItemRowSelect extends React.Component
{
    constructor(props) 
    {
        super(props);
    }
    
    render = () => 
    {
        var select_key = 'item_id_' + this.props.keyName;
        var select_options = [];
        var itemKey;
        select_options[0] = (<option key="0" value="">Vyberte položku</option>);
        for (itemKey in this.props.items) {            
            select_options[itemKey] = (<option key={itemKey} value={itemKey}>{this.props.items[itemKey]}</option>);
        }
        
        return (                
                    <td>
                        <select name={select_key} required="1" defaultValue={this.props.selectedItem} onChange={this.changed}>
                            {select_options}                            
                        </select>
                    </td>
                );
    }
    
    changed = (event) => 
    {
        var row_id = parseInt($(event.target).attr('name').replace('item_id_', ''));
        var item_id = $(event.target).val();
        
        var select_item_event = new CustomEvent(
            'select_item', 
            {detail: 
                {
                    row_id: row_id, 
                    item_id: item_id
                }
            }
        );
        document.getElementById('output_form').dispatchEvent(select_item_event);
    }
    
}


