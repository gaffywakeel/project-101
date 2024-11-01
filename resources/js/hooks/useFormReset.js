import {useEffect} from "react";

const useFormReset = (form, check) => {
    useEffect(() => {
        if (check) form.resetFields();
    }, [form, check]);
};

export default useFormReset;
