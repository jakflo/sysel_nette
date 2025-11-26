import { useContext } from 'react';
import type { FormEvent } from 'react';
import Warehouses from './Warehouses';
import Items from './Items';
import { ItemsContext } from './context/ContextProvider';
import OutputForm from './OutputForm';

function App() {
    const { dispatchItemsSelect } = useContext(ItemsContext);

    const formSubmitted = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        document.querySelector<HTMLFormElement>("#find_items_query_form")!.submit();
    };

    const addNewRow = () => {
        dispatchItemsSelect({ type: 'addNewItemRow' });
    };

    return (
    <>
    <form name="search_form_raw" onSubmit={formSubmitted}>
        <Warehouses />
        <Items />
        <button type="button" onClick={addNewRow}>Přidat položku</button>
        <input type="submit" name="sent" value="Najít" />
    </form>
    <OutputForm />
    </>
    );
}

export default App
