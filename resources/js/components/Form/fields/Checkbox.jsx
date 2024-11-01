import React, {useContext} from "react";
import {Checkbox as BaseCheckbox, FormControlLabel} from "@mui/material";
import {defaultTo} from "lodash";
import {FormInputContext} from "../contexts";

const Checkbox = ({
    checked,
    disabled = false,
    labelPlacement,
    ...otherProps
}) => {
    const {isRequired, label} = useContext(FormInputContext);

    const control = (
        <BaseCheckbox
            {...otherProps}
            checked={defaultTo(checked, false)}
            disabled={disabled}
        />
    );

    if (!label) return control;

    return (
        <FormControlLabel
            control={control}
            disabled={disabled}
            label={label}
            labelPlacement={labelPlacement}
            required={isRequired}
        />
    );
};

export default Checkbox;
