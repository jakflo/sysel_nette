type actionTypeSelectAllToggle = {
    type: 'selectAllToggle';
};

type actionTypeSelectToggle = {
    type: 'selectToggle';
    id: number
};

type actionType = actionTypeSelectAllToggle | actionTypeSelectToggle;

type stateType = {
    select_all: boolean;
    selected: Array<number>;
};

export default class WarehousesSelectReducerClass {
    private state: stateType;
    private action: actionType;

    constructor(state: stateType, action: actionType) {
        this.state = state;
        this.action = action;
    }

    dispatch() {
        switch (this.action.type) {
            case "selectAllToggle":
                return this.selectAllToggle();
            case "selectToggle":
                return this.selectToggle();
            default:
                return this.state;
        }
    }

    private selectAllToggle() {
        return {
            ...this.state,
            select_all: !this.state.select_all
        };
    }

    private selectToggle() {
        const action = this.action as actionTypeSelectToggle;
        const id = action.id;
        const isWarehouseSelected = this.state.selected.find(warehouse => {
            return warehouse === id;
        }) !== undefined;

        let newSelection = [] as Array<number>;
        if (isWarehouseSelected) {
            newSelection = this.state.selected.filter(item => {
                return item !== id;
            });
        } else {
            newSelection = ([] as Array<number>).concat(this.state.selected);
            newSelection.push(id);
        }

        return {
            ...this.state,
            selected: newSelection
        };
    }
}

export type { actionType, stateType };
