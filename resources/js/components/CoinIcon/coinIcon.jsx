import React from "react";
import IconBuilder from "../IconBuilder";

const CoinIcon = ({coin, size, sx, ...otherProps}) => {
    return (
        <IconBuilder
            icon={coin.svg_icon}
            sx={{...sx, fontSize: size}}
            {...otherProps}
        />
    );
};

export default CoinIcon;
