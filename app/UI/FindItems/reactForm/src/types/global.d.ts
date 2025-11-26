import type { stateType as itemsStateType } from '../context/ItemsSelectReducerClass';
import type { stateType as warehousesStateType } from '../context/WarehousesSelectReducerClass';

declare global {
    interface Window {
        APP_DATA: {
            items: Record<number, string>;
            warehouses: Record<number, string>;
            values: {
                items: itemsStateType;
                warehouses: warehousesStateType;
            };
        };
    }
};

export {};