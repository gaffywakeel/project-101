import React from "react";
import {Box} from "@mui/material";
import {useTokenField} from "@/hooks/useTokenField";
import Form, {TextField} from "@/components/Form";

const HiddenFields = () => {
    const tokenField = useTokenField();

    return (
        <Box sx={{display: "none"}}>
            {tokenField !== "token" ? (
                <Form.Item name="password">
                    <TextField id="hidden-password" />
                </Form.Item>
            ) : (
                <Form.Item name="token">
                    <TextField id="hidden-token" />
                </Form.Item>
            )}
        </Box>
    );
};

export default HiddenFields;
