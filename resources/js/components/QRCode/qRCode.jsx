import React, {forwardRef, useMemo} from "react";
import {QRCodeSVG} from "qrcode.react";
import {useTheme} from "@mui/material/styles";

const QRCode = forwardRef((props, ref) => {
    const theme = useTheme();
    const {imageSrc, ...otherProps} = props;

    const imageSettings = useMemo(
        () => ({
            src: imageSrc,
            excavate: true,
            width: 20,
            height: 20
        }),
        [imageSrc]
    );

    return (
        <QRCodeSVG
            ref={ref}
            {...(imageSrc && {imageSettings})}
            bgColor={theme.palette.background.paper}
            fgColor={theme.palette.text.primary}
            {...otherProps}
        />
    );
});

export default QRCode;
