import React, {forwardRef, useImperativeHandle, useMemo} from "react";
import FieldForm, {List, useWatch} from "rc-field-form";
import useForm from "./useForm";
import {FormContext, FormProvider} from "./contexts";
import Item from "./Item";
import Info from "./Info";

const Form = forwardRef((props, ref) => {
    const {
        form,
        name,
        onFinishFailed = () => {},
        className,
        ...otherProps
    } = props;

    const [wrapForm] = useForm(form);
    const context = useMemo(() => ({name, form}), [name, form]);

    wrapForm.__NAME__ = name;

    useImperativeHandle(ref, () => wrapForm);

    return (
        <FormContext.Provider value={context}>
            <FieldForm
                id={name}
                {...otherProps}
                onFinishFailed={onFinishFailed}
                name={name}
                className={className}
                form={wrapForm}
            />
        </FormContext.Provider>
    );
});

Form.Item = Item;
Form.List = List;
Form.useWatch = useWatch;
Form.useForm = useForm;
Form.Provider = FormProvider;
Form.Info = Info;

export default Form;
