import React, {useCallback, useRef, useState} from "react";
import {styled} from "@mui/material/styles";
import {m} from "framer-motion";
import {Box} from "@mui/material";

const PageSlider = ({
    index,
    children,
    sx,
    offsetY = 0,
    offsetX = 0,
    ...otherProps
}) => {
    const slideRef = useRef(null);
    const [height, setHeight] = useState();

    const updateHeight = useCallback(() => {
        const child = slideRef.current?.children[0];

        if (!child?.offsetHeight) return;

        if (height !== child.offsetHeight) {
            setHeight(child.offsetHeight);
        }
    }, [height]);

    const setSlideRef = (node) => {
        slideRef.current = node;
        updateHeight();
    };

    return (
        <SliderContainer
            sx={{
                mx: -offsetX,
                my: -offsetY,
                ...sx
            }}
            {...otherProps}>
            <Slider
                initial={false}
                animate={{x: index * -100 + "%"}}
                transition={{
                    tension: 190,
                    mass: 0.4,
                    friction: 70
                }}>
                {React.Children.toArray(children).map((child, key) => {
                    const isActive = index === key;
                    const ref = isActive ? setSlideRef : null;

                    return (
                        <SlideContent
                            key={key}
                            tabIndex={isActive ? 0 : -1}
                            aria-hidden={!isActive}
                            ref={ref}
                            sx={{
                                px: offsetX,
                                maxHeight: isActive ? "auto" : height,
                                py: offsetY
                            }}>
                            {child}
                        </SlideContent>
                    );
                })}
            </Slider>
        </SliderContainer>
    );
};

const SliderContainer = styled(Box)(() => ({
    display: "flex",
    flexDirection: "column",
    overflow: "hidden",
    width: "auto"
}));

const Slider = styled(m.div)(() => ({
    display: "flex",
    flexDirection: "row",
    direction: "ltr",
    willChange: "transform",
    alignItems: "flex-start",
    minHeight: "0"
}));

const SlideContent = styled(Box)(({theme}) => ({
    width: "100%",
    transition: theme.transitions.create("max-height"),
    flexShrink: 0
}));

export default PageSlider;
