import React from "react";
import Alert from "@/components/Alert";
import Form from "@/components/Form";
import {useTokenField} from "@/hooks/useTokenField";

const Alerts = () => {
    const field = useTokenField();

    return (
        <Form.Item dependencies={[field]}>
            {(form) => {
                const errors = form.getFieldError(field);

                return errors.map((error, i) => (
                    <Template key={i} content={error} />
                ));
            }}
        </Form.Item>
    );
};

const Template = ({content}) => {
    return <Alert severity="error">{content}</Alert>;
};

export default Alerts;
