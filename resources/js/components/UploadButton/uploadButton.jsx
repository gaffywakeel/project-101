import React, {useState} from "react";
import {LoadingButton} from "@mui/lab";
import Upload from "rc-upload";
import {defineMessages, useIntl} from "react-intl";
import {useUploadRequest} from "@/services/Http";
import {get, isEmpty} from "lodash";
import {FormHelperText, Stack, Typography} from "@mui/material";
import {styled} from "@mui/material/styles";
import BackupIcon from "@mui/icons-material/Backup";
import {notify} from "@/utils/index";

const messages = defineMessages({
    uploaded: {defaultMessage: "File was uploaded."}
});

const UploadButton = ({
    name = "file",
    action,
    data,
    mimeTypes,
    caption,
    children,
    value,
    onChange,
    onSuccess,
    onError,
    error = false,
    startIcon = <BackupIcon />,
    color = "primary",
    ...otherProps
}) => {
    const intl = useIntl();
    const [errors, setErrors] = useState([]);
    const [uploadRequest, loading] = useUploadRequest();
    const [file, setFile] = useState(null);

    const hasError = error || !isEmpty(errors);
    const controlled = onChange ? value : file;

    const beforeUpload = (file) => {
        setErrors([]);
        setFile(file);
        return Promise.resolve(file);
    };

    const handleChange = (options) => {
        return onChange(options.file);
    };

    const handleSuccess = (data) => {
        notify.success(intl.formatMessage(messages.uploaded));
        return onSuccess?.(data);
    };

    const handleError = (e, data) => {
        setErrors(get(data, `errors.${name}`, []));
        return onError?.(e, data);
    };

    const helperText = !isEmpty(errors) ? errors.join(", ") : caption;
    const request = onChange ? handleChange : uploadRequest;

    return (
        <Stack spacing={1} sx={{minWidth: 0}}>
            <StyledUpload
                name={name}
                action={action}
                data={data}
                customRequest={request}
                beforeUpload={beforeUpload}
                onError={handleError}
                onSuccess={handleSuccess}
                accept={mimeTypes}>
                <Stack direction="row" spacing={2}>
                    <LoadingButton
                        {...otherProps}
                        variant="outlined"
                        startIcon={startIcon}
                        color={hasError ? "error" : color}
                        loading={loading}>
                        {children}
                    </LoadingButton>

                    {controlled && (
                        <Typography
                            variant="body2"
                            alignSelf="center"
                            flexShrink={2}
                            noWrap>
                            {controlled.name}
                        </Typography>
                    )}
                </Stack>
            </StyledUpload>

            {helperText && (
                <FormHelperText sx={{lineHeight: 1}} error={hasError}>
                    {helperText}
                </FormHelperText>
            )}
        </Stack>
    );
};

const StyledUpload = styled(Upload)({
    // minWidth: 0
});

export default UploadButton;
