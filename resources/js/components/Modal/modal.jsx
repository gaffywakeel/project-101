import React, {
    cloneElement,
    isValidElement,
    useCallback,
    useEffect,
    useState
} from "react";
import {isFunction} from "lodash";
import {
    Dialog,
    DialogActions,
    DialogContent,
    DialogTitle,
    IconButton
} from "@mui/material";
import CloseIcon from "@mui/icons-material/Close";
import useMountHandler from "@/hooks/useMountHandler";

const noop = () => {};

const Modal = ({
    title,
    content,
    actions,
    dialogProps,
    dialogTitleProps,
    dialogContentProps,
    dialogActionsProps,
    resetKey,
    children,
    afterClose = noop
}) => {
    const handler = useMountHandler();
    const [open, setOpen] = useState(true);

    useEffect(() => {
        if (!open) {
            setTimeout(() => {
                handler.execute(afterClose);
            }, 1000);
        }
    }, [handler, open, afterClose]);

    useEffect(() => {
        setOpen(true);
    }, [resetKey]);

    const closeModal = useCallback(() => setOpen(false), []);

    const renderComponent = (component) => {
        if (isValidElement(component)) {
            return cloneElement(component, {closeModal});
        }

        if (isFunction(component)) {
            return component(closeModal);
        }

        return component;
    };

    if (children) {
        return (
            <Dialog {...dialogProps} onClose={closeModal} open={open}>
                {renderComponent(children)}
            </Dialog>
        );
    }

    return (
        <Dialog {...dialogProps} onClose={closeModal} open={open}>
            {title && (
                <DialogTitle
                    {...dialogTitleProps}
                    sx={{mr: 6, ...dialogTitleProps?.sx}}>
                    {renderComponent(title)}

                    <IconButton
                        onClick={closeModal}
                        sx={(theme) => ({
                            position: "absolute",
                            color: theme.palette.grey[500],
                            top: theme.spacing(2),
                            right: theme.spacing(1.5)
                        })}>
                        <CloseIcon />
                    </IconButton>
                </DialogTitle>
            )}

            <DialogContent {...dialogContentProps}>
                {renderComponent(content)}
            </DialogContent>

            {actions && (
                <DialogActions {...dialogActionsProps}>
                    {renderComponent(actions)}
                </DialogActions>
            )}
        </Dialog>
    );
};

export default Modal;
