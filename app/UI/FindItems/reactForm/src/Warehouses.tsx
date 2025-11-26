import { useContext } from "react";
import { WarehousesContext } from './context/ContextProvider';
import { arrayToList } from './utils';
import type { listType } from './utils';

function Warehouses() {
    const warehousesContext = useContext(WarehousesContext);
    const { warehousesSelect, dispatchWarehousesSelect } = warehousesContext;
    const warehouses: listType[] = arrayToList(window.APP_DATA.warehouses);

    const selectAllToggle = () => {
        dispatchWarehousesSelect({ type: 'selectAllToggle' });
    };

    const isWarehouseSelected = (id: number) => {
        return warehousesSelect.selected.find((warehouse: number) => {
            return warehouse === id;
        }) !== undefined;
    }

    const toggleSelectWarehouse = (id: number) => {
        dispatchWarehousesSelect({ type: 'selectToggle', id });
    }

    return (
        <>
        <ul className="unmarked">
            <li key="select_all">
                <input id="cb_use_all_wares" type="checkbox" name="use_all_wares" defaultChecked={warehousesSelect.select_all} onClick={ selectAllToggle } />
                <label htmlFor="cb_use_all_wares">Vybrat všechny sklady</label>
            </li>
            { !warehousesSelect.select_all && (
                <>
                { warehouses.map((warehouse: listType) => {
                    const warehouseNameAttr = `cb_ware_${warehouse.id}`;
                    const isWarehouseSelectedValue = isWarehouseSelected(parseInt(warehouse.id));
                    return (
                        <li key={warehouse.id}>
                            <input
                                id={warehouseNameAttr}
                                type="checkbox"
                                name={warehouseNameAttr}
                                defaultChecked={isWarehouseSelectedValue}
                                onChange={() => {toggleSelectWarehouse(parseInt(warehouse.id))}}
                            />
                            <label htmlFor={warehouseNameAttr}>{warehouse.name}</label>
                        </li>
                    );
                }) }
                </>
            ) }
        </ul>
        </>
    );
}

export default Warehouses;
