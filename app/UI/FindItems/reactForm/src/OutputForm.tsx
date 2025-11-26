import { useContext } from "react";
import { WarehousesContext,ItemsContext } from './context/ContextProvider';
import { safeJsonStringify } from './utils';

export default function OutputForm() {
    const warehousesContext = useContext(WarehousesContext);
    const itemsContext = useContext(ItemsContext);
    const { warehousesSelect } = warehousesContext;
    const { itemsSelect } = itemsContext;

    const warehousesSelectNumeric = {
        ...warehousesSelect,
        select_all: warehousesSelect.select_all ? 1 : 0
    };

    const data = {
        warehouses: warehousesSelectNumeric,
        items: itemsSelect
    };



    return (
        <>
        <form name="search_form_output" id="find_items_query_form">
            <input type="hidden" name="query" value={safeJsonStringify(data)} />
        </form>
        </>
    );

}