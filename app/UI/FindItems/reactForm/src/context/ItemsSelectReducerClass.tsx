type actionTypeChangeItemType = {
    type: 'changeItemType';
    rowId: number;
    newItemId: number;
};

type actionTypeChangeItemAmount = {
   type: 'changeItemAmount';
   rowId: number;
   newItemAmount: number | '';
};

type actionTypeAddNewRow = {
    type: 'addNewItemRow';
};

type actionTypeRemoveRow = {
    type: 'removeRow';
    rowId: number;
};

type stateItemType = {
    item_id: number;
    item_amount: number | '';
};

type actionType = actionTypeChangeItemType | actionTypeChangeItemAmount | actionTypeAddNewRow | actionTypeRemoveRow;
type stateType = Array<stateItemType>;

export default class ItemsSelectReducerClass {
    private state: stateType;
    private action: actionType;

    constructor(state: stateType, action: actionType) {
        this.state = state;
        this.action = action;
    }

    dispatch() {
        switch (this.action.type) {
            case "addNewItemRow":
                return this.addNewItemRow();
            case "changeItemType":
                return this.changeItemType();
            case "changeItemAmount":
                return this.changeItemAmount();
            case "removeRow":
                return this.removeRow();
            default:
                return this.state;
        }
    }

    private addNewItemRow() {
        const newState = ([] as stateType).concat(this.state);
        newState.push({...newEmptyItemRow});
        return newState;
    }

    private changeItemType() {
        const action = this.action as actionTypeChangeItemType;
        const rowId = action.rowId;
        const newState = ([] as stateType).concat(this.state);
        if (this.state[rowId] === undefined) {
            console.log('radek nenalezen');
            return this.state;
        }

        newState[rowId]['item_id'] = action.newItemId;
        return newState;
    }

    private changeItemAmount() {
        const action = this.action as actionTypeChangeItemAmount;
        const rowId = action.rowId;
        const newState = ([] as stateType).concat(this.state);
        if (this.state[rowId] === undefined) {
            console.log('radek nenalezen');
            return this.state;
        }

        newState[rowId]['item_amount'] = action.newItemAmount;
        return newState;
    }

    private removeRow() {
        const action = this.action as actionTypeRemoveRow;
        const rowId = action.rowId;
        const newState = ([] as stateType).concat(this.state);

        if (this.state[rowId] === undefined) {
            console.log('radek nenalezen');
            return this.state;
        }

        newState.splice(rowId, 1);
        return newState;
    }

}

const newEmptyItemRow: stateItemType = {
    item_id: 0,
    item_amount: ''
};

export { newEmptyItemRow };
export type { actionType, stateType, stateItemType, actionTypeChangeItemType, actionTypeChangeItemAmount };
