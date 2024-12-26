class Warehouses extends React.Component
{
    constructor(props) 
    {
        super(props);
        this.state = {
            useAllWares: this.props.valuesData.select_all
        };
    }
        
    render = () => 
    {
        var allWarehousesSelected = this.state.useAllWares ? 1 : 0;
        var selected_warehouses = this.props.valuesData.selected;
        var warehouses = [];
        var warehouse_id;
        for(warehouse_id in this.props.warehouses) {
            let warehouse_is_selected = (selected_warehouses.indexOf(parseInt(warehouse_id)) == -1) ? 0 : 1;
            let warehouse_name = this.props.warehouses[warehouse_id];
            let warehouse_name_attr = 'cb_ware_' + warehouse_id;
            let key_attr = 'cb_ware_li_key_' + warehouse_id;
            warehouses.push(<li key={key_attr}>
                                <input id={warehouse_name_attr} type="checkbox" name={warehouse_name_attr} defaultChecked={warehouse_is_selected} onChange={this.useWarehouse} />
                                <label htmlFor={warehouse_name_attr}>{warehouse_name}</label>                
                            </li>);
    }
    
        var select_wares_classname = this.state.useAllWares ? 'hidden' : '';
        return (<div id="warehouses_component">
                    <ul className="unmarked">
                        <li>
                            <input id="cb_use_all_wares" type="checkbox" name="use_all_wares" defaultChecked={allWarehousesSelected} onClick={this.useAllWarehouses} />
                            <label htmlFor="cb_use_all_wares">Vybrat všechny sklady</label>
                        </li>
                        <div id="select_wares" className={select_wares_classname}>                
                            {warehouses}                
                        </div>        
                    </ul>
                </div>);
    }
    
    useAllWarehouses = (event) => 
    {
        this.setState({useAllWares: !this.state.useAllWares});
        var all_warehouses_event = new CustomEvent(
            'select_all_warehouses', 
            {detail: 
                {state: $(event.target).prop('checked')}
            }
        );
        document.getElementById('output_form').dispatchEvent(all_warehouses_event);
    }
    
    useWarehouse = (event) => 
    {
        var warehouses_event = new CustomEvent(
            'select_warehouse', 
            {detail: 
                {
                    id: $(event.target).attr('id'), 
                    state: $(event.target).prop('checked')
                }
            }
        );
        document.getElementById('output_form').dispatchEvent(warehouses_event);
    }
}

