const config = {
    exclude: /node_modules/,
    presets: [
        [
            "@babel/preset-env",
            {
                useBuiltIns: "usage",
                targets: "> 10%, not dead",
                corejs: {version: 3},
                modules: false
            }
        ],
        "@babel/preset-react"
    ],
    plugins: [
        [
            "formatjs",
            {
                idInterpolationPattern: "[sha512:contenthash:20]"
            }
        ]
    ],
    env: {
        development: {
            compact: false,
        },
        production: {
            compact: true,
            plugins: [
                [
                    "import",
                    {
                        libraryName: "lodash",
                        libraryDirectory: "",
                        camel2DashComponentName: false
                    },
                    "lodash"
                ],
                [
                    "import",
                    {
                        libraryName: "@mui/material",
                        libraryDirectory: "",
                        camel2DashComponentName: false
                    },
                    "mui-core"
                ],
                [
                    "import",
                    {
                        libraryName: "@mui/icons-material",
                        libraryDirectory: "",
                        camel2DashComponentName: false
                    },
                    "mui-icons"
                ],
                [
                    "import",
                    {
                        libraryName: "@mui/lab",
                        libraryDirectory: "",
                        camel2DashComponentName: false
                    },
                    "mui-lab"
                ],
                [
                    "import",
                    {
                        libraryName: "@mui/utils",
                        libraryDirectory: "",
                        camel2DashComponentName: false
                    },
                    "mui-utils"
                ]
            ]
        }
    }
};

module.exports = config;
