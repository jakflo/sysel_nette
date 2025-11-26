import { useContext, useEffect, useRef } from "react";
import { ItemsContext } from './context/ContextProvider';
import { arrayToList } from './utils';
import type { listType } from './utils';
import type { ChangeEvent } from "react";
import type { actionTypeChangeItemType, actionTypeChangeItemAmount } from './context/ItemsSelectReducerClass';

function Items() {
    const itemsList = arrayToList(window.APP_DATA.items);
    itemsList.unshift({ id: "0", name: "Vyberte položku" });
    const { itemsSelect, dispatchItemsSelect } = useContext(ItemsContext);

    const removeRow = (rowId: number) => {
        dispatchItemsSelect({
            type: 'removeRow',
            rowId: rowId
        });
    };

    return (
        <>
        <table id="item_list" className="w3_table">
            <thead>
                <tr>
                    <th>Položka</th>
                    <th>Množství</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                { itemsSelect.map((item, index) => {
                    return (
                        <tr className="item" key={index}>
                            <ItemSelectorField itemsList={itemsList} value={item.item_id} keyId={index} />
                            <ItemAmountField value={item.item_amount} keyId={index} />
                            <td>
                                <button d-i={index} type="button" onClick={() => {removeRow(index)}}>Odebrat</button>
                            </td>
                        </tr>
                    );
                }) }
            </tbody>
        </table>
        </>
    );

}

type ItemSelectorFieldProps = {
    itemsList: listType[];
    value: number;
    keyId: number;
};

function ItemSelectorField({ itemsList, value, keyId }: ItemSelectorFieldProps) {
    const { dispatchItemsSelect } = useContext(ItemsContext);
    const selectRef = useRef<HTMLSelectElement>(null);

    useEffect(() => {
        if (selectRef.current && selectRef.current.value !== String(value)) {
            selectRef.current.value = String(value ?? "");
        }
    });

    const itemChanged = (event: ChangeEvent<HTMLSelectElement>) => {
        const action = {
            type: 'changeItemType',
            rowId: keyId,
            newItemId: parseInt(event.target.value)
        } as actionTypeChangeItemType;

        dispatchItemsSelect(action);
    };

    return (
        <>
        <td>
            <select ref={selectRef} name={`select_item_${keyId}`} required defaultValue={value} onChange={itemChanged} >
                { itemsList.map(item => {
                    return (
                        <option key={item.id} value={item.id}>{item.name}</option>
                    );
                }) }
            </select>
        </td>
        </>
    );
}

type ItemAmountFieldProps = {
    value: number | '';
    keyId: number
};

function ItemAmountField({ value, keyId }: ItemAmountFieldProps) {
    const { dispatchItemsSelect } = useContext(ItemsContext);
    const inputRef = useRef<HTMLInputElement>(null);

    const amountChanged = (event: ChangeEvent<HTMLInputElement>) => {
        const value = event.target.value;

        const action = {
            type: 'changeItemAmount',
            rowId: keyId,
            newItemAmount: value === '' ? '' : parseInt(value)
        } as actionTypeChangeItemAmount;

        dispatchItemsSelect(action);
    };

    useEffect(() => {
        if (inputRef.current && inputRef.current.value !== value) {
            inputRef.current.value = String(value);
        }
    });

    return (
        <>
        <td>
            <input ref={inputRef} type="number" name={`item_amount_${keyId}`} min="1" step="1" required defaultValue={value} onChange={amountChanged} />
        </td>
        </>
    );
}

export default Items;
