class SearchForm extends React.Component
{
    constructor(props) 
    {
        super(props);
        this.data = JSON.parse(atob($('#data_for_react').text()));
        this.state = {itemRows: []};
        this.itemRowsLastKey = -1;
    }
        
    render = () => 
    {
//        console.log(this.data);
        return (<div id="search_component">
                    <form name="search_form_raw" onSubmit={this.formSubmitted}>
                        <Warehouses warehouses={this.data.warehouses} valuesData={this.data.values.warehouses} />
                        <table id="item_list" className="w3_table">
                            <thead>
                                <tr>
                                    <th>Položka</th>
                                    <th>Množství</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                {this.state.itemRows}
                            </tbody>
                        </table>
                        <button type="button" onClick={() => {this.addItemRow();}}>Přidat položku</button>
                        <input type="submit" name="sent" value="Najít" />
                    </form>
                    <OutputForm valuesData={this.data.values} />
                </div>);
    }
    
    componentDidMount = () => 
    {
        if (this.data.values.items.length > 0) {
            this.data.values.items.forEach((item) => {
                this.addItemRow(item.item_id, item.item_amount);
            });
        } else {
            this.addItemRow();
        }        
    }
    
    formSubmitted = (event) => 
    {
        event.preventDefault();
        $("form[name='search_form_output']").submit();
    }
    
    addItemRow = (selected_item = 0, amount = '') => 
    {
        var item_rows = this.state.itemRows;
        this.itemRowsLastKey++;
        item_rows[this.itemRowsLastKey] = (
                <tr className="item" key={this.itemRowsLastKey}>
                    <ItemRowSelect keyName={this.itemRowsLastKey} items={this.data.items} selectedItem={selected_item} />
                    <ItemRowAmount keyName={this.itemRowsLastKey} amount={amount} />
                    <ItemRowDelete keyName={this.itemRowsLastKey} removeCallback={this.removeItemRow} />
                </tr>
                );
        this.setState({itemRows: item_rows});
        
        var add_item_event = new CustomEvent(
            'add_item_row', 
            {detail: 
                {
                    key: this.itemRowsLastKey, 
                    item_id: selected_item, 
                    item_amount: amount
                }
            }
        );
        document.getElementById('output_form').dispatchEvent(add_item_event);
    }
    
    removeItemRow = (row_id) => 
    {
        var item_rows = this.state.itemRows;
        
        var real_length = 0;
        item_rows.forEach(
            (value) => {
                real_length++;
            }
        );

        if (real_length > 1) {
            delete item_rows[row_id];
            this.setState({itemRows: item_rows});
            
            var remove_item_event = new CustomEvent(
                'remove_item_row', 
                {detail: {key: row_id}}
            );
            document.getElementById('output_form').dispatchEvent(remove_item_event);
        }
    }
    
}
