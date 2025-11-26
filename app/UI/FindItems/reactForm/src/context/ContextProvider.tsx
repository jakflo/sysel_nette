import { useReducer, createContext } from 'react';
import type { ReactNode, Dispatch } from 'react';
import WarehousesSelectReducerClass from './WarehousesSelectReducerClass';
import ItemsSelectReducerClass from './ItemsSelectReducerClass';
import { newEmptyItemRow } from './ItemsSelectReducerClass';
import type { actionType as itemsActionType, stateType as itemsStateType } from './ItemsSelectReducerClass';
import type { actionType as warehousesActionType, stateType as warehousesStateType } from './WarehousesSelectReducerClass';

const valuesFromBackend = window.APP_DATA.values;
const itemsSelectInit: itemsStateType = valuesFromBackend.items.length > 0 ? valuesFromBackend.items : [{...newEmptyItemRow}];
const warehousesSelectInit: warehousesStateType = valuesFromBackend.warehouses;

type ItemsContextType = {
    itemsSelect: itemsStateType;
    dispatchItemsSelect: Dispatch<itemsActionType>;
};

const ItemsContext = createContext<ItemsContextType>({
    itemsSelect: itemsSelectInit,
    dispatchItemsSelect: () => {}
});

type WarehousesContextType = {
    warehousesSelect: warehousesStateType;
    dispatchWarehousesSelect: Dispatch<warehousesActionType>;
};

const WarehousesContext = createContext<WarehousesContextType>({
    warehousesSelect: warehousesSelectInit,
    dispatchWarehousesSelect: () => {}
});

function ContextProvider({ children }: { children: ReactNode }) {
    const [itemsSelect, dispatchItemsSelect] = useReducer(itemsSelectReducer, itemsSelectInit);
    const [warehousesSelect, dispatchWarehousesSelect] = useReducer(warehousesSelectReducer, warehousesSelectInit);

    return (
        <>
        <ItemsContext.Provider value={{ itemsSelect, dispatchItemsSelect }}>
            <WarehousesContext.Provider value={{ warehousesSelect, dispatchWarehousesSelect }}>
                { children }
            </WarehousesContext.Provider>
        </ItemsContext.Provider>
        </>
    );
}

const itemsSelectReducer = (state: itemsStateType, action: itemsActionType) => {
    const ItemsSelectReducerDispatcher = new ItemsSelectReducerClass(state, action);
    return ItemsSelectReducerDispatcher.dispatch();
};

const warehousesSelectReducer = (state: warehousesStateType, action: warehousesActionType) => {
    const warehousesSelectReducerDispatcher = new WarehousesSelectReducerClass(state, action);
    return warehousesSelectReducerDispatcher.dispatch();
};

export default ContextProvider;
export { ItemsContext, WarehousesContext };
