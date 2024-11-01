import React, {useRef, useState} from "react";
import {defineMessages, FormattedMessage, useIntl} from "react-intl";
import {defaultTo, get, isEmpty} from "lodash";
import Cropper from "cropperjs";
import {useUploadRequest} from "@/services/Http";
import {useAbort} from "@/support/Abort";
import Upload from "rc-upload";
import Spin from "../Spin";
import PropTypes from "prop-types";
import {Icon} from "@iconify/react";
import {useObjectUrl} from "@/hooks/useObjectUrl";
import roundAddAPhoto from "@iconify/icons-ic/round-add-a-photo";
import {styled} from "@mui/material/styles";
import {notify} from "@/utils/index";
import {
    Box,
    Button,
    Dialog,
    DialogActions,
    DialogTitle,
    FormHelperText,
    Stack,
    Typography
} from "@mui/material";

const messages = defineMessages({
    uploaded: {defaultMessage: "Picture was uploaded."},
    crop: {defaultMessage: "Crop"}
});

const UploadPhoto = ({
    action,
    preview,
    sx,
    caption,
    value,
    onChange,
    onSuccess,
    onError,
    error = false,
    mimeTypes = "image/jpeg,image/png",
    aspectRatio = 1,
    rounded = false
}) => {
    const intl = useIntl();
    const cropperImg = useRef();
    const cropperAction = useRef();
    const cropperCancel = useRef();
    const uploadRef = useRef();
    const abort = useAbort();

    const [file, setFile] = useState(null);
    const [dragState, setDragState] = useState("drop");
    const [uploadRequest, loading] = useUploadRequest();
    const [showCropper, setShowCropper] = useState(false);
    const [openModal, setOpenModal] = useState(false);
    const [errors, setErrors] = useState([]);

    const controlled = onChange ? value : file;
    const objectUrl = useObjectUrl(controlled);
    const previewImage = defaultTo(objectUrl, preview);
    const isDragging = dragState === "dragover";
    const hasError = error || !isEmpty(errors);

    const beforeUpload = (file) => {
        setErrors([]);
        return new Promise((resolve, reject) => {
            const reader = new FileReader();

            if (!abort.isAborted()) {
                setOpenModal(true);
            }

            const onComplete = (file) => {
                resolve(file);
                setFile(file);
            };

            reader.onloadend = function () {
                if (!abort.isAborted()) {
                    cropperImg.current.src = reader.result;

                    const cropper = new Cropper(cropperImg.current, {
                        aspectRatio,
                        minContainerWidth: "100%",
                        minContainerHeight: 250,
                        viewMode: 2
                    });

                    cropperCancel.current = () => {
                        setOpenModal(false);
                        reject(new Error("Canceled"));
                        cropper.destroy();
                    };

                    cropperAction.current = () => {
                        setOpenModal(false);
                        const canvas = cropper.getCroppedCanvas();
                        canvas.toBlob(onComplete, file.type);
                        cropper.destroy();
                    };

                    setShowCropper(true);
                }
            };

            reader.readAsDataURL(file);
        });
    };

    const handleSuccess = (data) => {
        notify.success(intl.formatMessage(messages.uploaded));
        return onSuccess?.(data);
    };

    const handleError = (e, data) => {
        setErrors(get(data, "errors.file", []));
        return onError?.(e, data);
    };

    const handleChange = (options) => {
        return onChange(options.file);
    };

    const handleDragState = (e) => {
        setDragState(e.type);
    };

    const handleCropAction = () => {
        cropperAction.current?.();
    };

    const handleCropCancel = () => {
        cropperCancel.current?.();
    };

    const helperText = !isEmpty(errors) ? errors.join(", ") : caption;
    const request = onChange ? handleChange : uploadRequest;

    return (
        <Stack spacing={1.5} sx={{minWidth: 0}}>
            <Spin spinning={loading}>
                <UploadContainer
                    onDrop={handleDragState}
                    onDragLeave={handleDragState}
                    onDragOver={handleDragState}
                    rounded={rounded}
                    sx={{
                        ...(hasError && {
                            color: "error.main",
                            borderColor: "error.light",
                            bgcolor: "error.lighter"
                        }),
                        ...(isDragging && {
                            color: "info.main",
                            borderColor: "info.light",
                            bgcolor: "info.lighter",
                            opacity: 0.5
                        }),
                        ...sx
                    }}>
                    <Upload
                        ref={uploadRef}
                        action={action}
                        customRequest={request}
                        beforeUpload={beforeUpload}
                        onError={handleError}
                        onSuccess={handleSuccess}
                        accept={mimeTypes}>
                        <UploadContent rounded={rounded}>
                            {previewImage && (
                                <Box
                                    component="img"
                                    alt="photo preview"
                                    sx={{zIndex: 8, objectFit: "cover"}}
                                    src={previewImage}
                                />
                            )}

                            <Placeholder
                                className="placeholder"
                                sx={{
                                    ...(previewImage && {
                                        opacity: 0,
                                        color: "common.white",
                                        bgcolor: "grey.900",
                                        "&:hover": {opacity: 0.72}
                                    })
                                }}>
                                <Box
                                    component={Icon}
                                    sx={{width: 24, height: 24, mb: 1}}
                                    icon={roundAddAPhoto}
                                />

                                <Typography variant="caption">
                                    {previewImage ? (
                                        <FormattedMessage defaultMessage="Change photo" />
                                    ) : (
                                        <FormattedMessage defaultMessage="Upload photo" />
                                    )}
                                </Typography>
                            </Placeholder>
                        </UploadContent>
                    </Upload>
                </UploadContainer>
            </Spin>

            {helperText && (
                <FormHelperText
                    error={hasError}
                    sx={{lineHeight: 1, textAlign: "center"}}>
                    {helperText}
                </FormHelperText>
            )}

            <Dialog onClose={handleCropCancel} open={openModal}>
                <DialogTitle>
                    <FormattedMessage defaultMessage="Crop Image" />
                </DialogTitle>

                <CropperContent showCropper={showCropper}>
                    <img ref={cropperImg} alt="Crop Photo" />
                </CropperContent>

                <DialogActions>
                    <Button onClick={handleCropCancel} color="inherit">
                        <FormattedMessage defaultMessage="Cancel" />
                    </Button>

                    <Button onClick={handleCropAction} variant="contained">
                        <FormattedMessage defaultMessage="Crop" />
                    </Button>
                </DialogActions>
            </Dialog>
        </Stack>
    );
};

const UploadContainer = styled(({rounded, ...props}) => {
    return <div {...props} />;
})(({theme, rounded}) => ({
    height: 150,
    width: 150,
    margin: "auto",
    padding: theme.spacing(1),
    borderRadius: rounded ? theme.shape.borderRadius : "50%",
    border: `1px dashed ${theme.palette.grey[500_32]}`
}));

const UploadContent = styled(({rounded, ...props}) => {
    return <div {...props} />;
})(({theme, rounded}) => ({
    height: "100%",
    width: "100%",
    display: "flex",
    justifyContent: "center",
    alignItems: "center",
    outline: "none",
    zIndex: 0,
    position: "relative",
    overflow: "hidden",
    borderRadius: rounded ? theme.shape.borderRadius : "50%",
    "& > *": {
        maxWidth: "100%",
        maxHeight: "100%"
    },
    "& > .placeholder": {
        width: "100%",
        height: "100%"
    },
    "&:hover .placeholder": {zIndex: 9, opacity: 0.72},
    "&:hover": {cursor: "pointer"}
}));

const CropperContent = styled(({showCropper, ...props}) => {
    return <Box {...props} />;
})(({showCropper}) => ({
    overflow: "hidden",
    visibility: showCropper ? "visible" : "hidden",
    "& img": {display: "block", maxWidth: "100%"}
}));

const Placeholder = styled("div")(({theme}) => ({
    display: "flex",
    justifyContent: "center",
    alignItems: "center",
    flexDirection: "column",
    position: "absolute",
    color: theme.palette.text.secondary,
    backgroundColor: theme.palette.background.neutral,
    transition: theme.transitions.create("opacity", {
        easing: theme.transitions.easing.easeInOut,
        duration: theme.transitions.duration.shorter
    })
}));

UploadPhoto.propTypes = {
    error: PropTypes.bool,
    file: PropTypes.oneOfType([PropTypes.string, PropTypes.object]),
    caption: PropTypes.node,
    sx: PropTypes.object
};

export default UploadPhoto;
