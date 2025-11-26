class OutputForm extends React.Component
{
    constructor(props) 
    {
        super(props);
        this.state = {
            data: {
                warehouses: {
                    select_all: this.props.valuesData.warehouses.select_all ? 1 : 0, 
                    selected: this.props.valuesData.warehouses.selected
                }, 
                items: {}
            }
        };
    }
    
    render = () => 
    {
        var data = JSON.stringify(this.state.data);
        return (
                <form name="search_form_output">
                    <input type="hidden" name="query" id="output_form" value={data} />
                </form>
                );
    }
    
    componentDidMount = () => 
    {
        document.getElementById('output_form').addEventListener(
            'select_all_warehouses', 
            (e) => {
                this.changeWarehousesSelectAll(e.detail.state);
            }
        );
        document.getElementById('output_form').addEventListener(
            'select_warehouse', 
            (e) => {
                this.changeWarehouseSelect(e.detail.id, e.detail.state);
            }
        );
        document.getElementById('output_form').addEventListener(
            'add_item_row', 
            (e) => {
                this.addItemsRow(e.detail.key, e.detail.item_id, e.detail.item_amount);
            }
        );
        document.getElementById('output_form').addEventListener(
            'remove_item_row', 
            (e) => {
                this.removeItemsRow(e.detail.key);
            }
        );
        document.getElementById('output_form').addEventListener(
            'select_item', 
            (e) => {
                this.changeItemIdInItemsRow(e.detail.row_id, e.detail.item_id);
            }
        );
        document.getElementById('output_form').addEventListener(
            'change_item_amount', 
            (e) => {
                this.changeItemAmountInItemsRow(e.detail.row_id, e.detail.item_amount);
            }
        );
    }
    
    changeWarehousesSelectAll = (value) => 
    {
        var data = this.state.data;
        data.warehouses.select_all = value ? 1 : 0;
        this.setState({data: data});
    }
    
    changeWarehouseSelect = (id_name, value) => 
    {
        var id = parseInt(id_name.replace('cb_ware_', ''));
        if (value) {
            this.changeWarehouseSelectOn(id);
        } else {
            this.changeWarehouseSelectOff(id);
        }
    }
    
    addItemsRow = (key, item_id, item_amount) => 
    {
        var data = this.state.data;
        var item_row = {
            item_id: item_id, 
            item_amount: item_amount
        };
        data.items[key] = item_row;
        this.setState({data: data});
    }
    
    changeItemIdInItemsRow = (key, item_id) => 
    {
        var data = this.state.data;
        data.items[key].item_id = item_id;
        this.setState({data: data});
    }
    
    changeItemAmountInItemsRow = (key, item_amount) => 
    {
        var data = this.state.data;
        data.items[key].item_amount = item_amount;
        this.setState({data: data});
    }
    
    removeItemsRow = (key) => 
    {
        var data = this.state.data;
        delete data.items[key];
        this.setState({data: data});
    }
    
    changeWarehouseSelectOn = (id) => 
    {
        var data = this.state.data;
        var key = data.warehouses.selected.indexOf(id);
        if (key === -1) {
            data.warehouses.selected.push(id);
            this.setState({data: data});
        }
    }
    
    changeWarehouseSelectOff = (id) => 
    {
        var data = this.state.data;
        var key = data.warehouses.selected.indexOf(id);
        if (key !== -1) {
            data.warehouses.selected.splice(key, 1);
            this.setState({data: data});
        }
    }
    
}