import {useCallback, useState} from "react";

export function usePatchElement() {
    const [elements, setElements] = useState([]);

    const patchElement = useCallback((element, key) => {
        setElements((prevState) => {
            const nextState = [...prevState];

            if (key) {
                const index = nextState.findIndex((ele) => ele.key === key);

                if (index < 0) {
                    nextState.push(element);
                } else {
                    nextState[index] = element;
                }
            } else {
                nextState.push(element);
            }

            return nextState;
        });

        return () => {
            if (key) return;

            setElements((o) => o.filter((ele) => ele !== element));
        };
    }, []);
    return [elements, patchElement];
}
