import React, {useEffect, useState} from "react";
import AutoComplete from "./AutoComplete";
import useDebounce from "@/hooks/useDebounce";

const AsyncAutoComplete = ({fetcher, debounce = 500, ...otherProps}) => {
    const [options, setOptions] = useState([]);
    const [inputValue, setInputValue] = useState("");
    const [loading, setLoading] = useState(false);

    const fetchOptions = useDebounce((value) => {
        setLoading(true);

        fetcher(value)
            .then((data) => {
                setLoading(false);
                setOptions(data);
            })
            .catch(() => {
                setLoading(false);
            });
    }, debounce);

    useEffect(() => {
        if (inputValue) {
            fetchOptions(inputValue);
        } else {
            fetchOptions(undefined);
        }
    }, [fetchOptions, inputValue]);

    return (
        <AutoComplete
            inputValue={inputValue}
            onInputChange={(_, value) => setInputValue(value)}
            loading={loading}
            options={options}
            filterOptions={(x) => x}
            filterSelectedOptions
            {...otherProps}
        />
    );
};

export default AsyncAutoComplete;
